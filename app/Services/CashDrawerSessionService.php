<?php

namespace App\Services;

use App\Models\CashDrawerSession;
use App\Models\Refund;
use App\Models\RestaurantOrder;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CashDrawerSessionService
{
    public function openSession(int $tenantId, int $branchId, int $userId, float $openingBalance, ?string $notes = null): CashDrawerSession
    {
        $existingSession = CashDrawerSession::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->first();

        if ($existingSession) {
            throw new InvalidArgumentException('A cash drawer session is already open for this branch.');
        }

        return CashDrawerSession::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'user_id' => $userId,
            'session_number' => CashDrawerSession::generateSessionNumber(),
            'status' => 'open',
            'opening_balance' => $openingBalance,
            'notes' => $notes,
            'opened_at' => now(),
        ]);
    }

    public function closeSession(CashDrawerSession $session, float $closingBalance, ?string $notes = null): CashDrawerSession
    {
        if (! $session->isOpen()) {
            throw new InvalidArgumentException('Cash drawer session is already closed.');
        }

        return DB::transaction(function () use ($session, $closingBalance, $notes) {
            $totals = $this->calculateSessionTotals($session);

            $expectedBalance = $this->calculateExpectedBalance($session, $totals);
            $variance = $closingBalance - $expectedBalance;

            $session->update([
                'status' => 'closed',
                'closing_balance' => $closingBalance,
                'expected_balance' => $expectedBalance,
                'variance' => $variance,
                'cash_sales' => $totals['cash_sales'],
                'card_sales' => $totals['card_sales'],
                'other_sales' => $totals['other_sales'],
                'refunds_total' => $totals['refunds_total'],
                'notes' => $notes ? $session->notes."\n".$notes : $session->notes,
                'closed_at' => now(),
            ]);

            ActivityLogService::log('cash_drawer_closed', "Cash drawer session {$session->session_number} closed", [
                'session_id' => $session->id,
                'session_number' => $session->session_number,
                'opening_balance' => $session->opening_balance,
                'closing_balance' => $closingBalance,
                'expected_balance' => $expectedBalance,
                'variance' => $variance,
            ]);

            return $session->fresh();
        });
    }

    public function calculateSessionTotals(CashDrawerSession $session): array
    {
        $sales = Sale::where('cash_drawer_session_id', $session->id)->get();
        $orders = RestaurantOrder::where('cash_drawer_session_id', $session->id)
            ->where('is_paid', true)
            ->get();
        $refunds = Refund::where('cash_drawer_session_id', $session->id)->get();

        $cashSales = $sales->where('payment_method', 'Cash')->sum('grand_total')
            + $orders->where('payment_method', 'Cash')->sum('grand_total');

        $cardSales = $sales->where('payment_method', 'Card')->sum('grand_total')
            + $orders->where('payment_method', 'Card')->sum('grand_total');

        $otherSales = $sales->whereNotIn('payment_method', ['Cash', 'Card'])->sum('grand_total')
            + $orders->whereNotIn('payment_method', ['Cash', 'Card'])->sum('grand_total');

        $refundsTotal = $refunds->sum('grand_total');

        return [
            'cash_sales' => (float) $cashSales,
            'card_sales' => (float) $cardSales,
            'other_sales' => (float) $otherSales,
            'refunds_total' => (float) $refundsTotal,
            'total_sales' => (float) ($cashSales + $cardSales + $otherSales),
            'sales_count' => $sales->count() + $orders->count(),
            'refunds_count' => $refunds->count(),
        ];
    }

    public function calculateExpectedBalance(CashDrawerSession $session, array $totals): float
    {
        return (float) $session->opening_balance
            + $totals['cash_sales']
            + (float) $session->cash_added
            - (float) $session->cash_removed
            - $totals['refunds_total'];
    }

    public function addCash(CashDrawerSession $session, float $amount, ?string $notes = null): CashDrawerSession
    {
        if (! $session->isOpen()) {
            throw new InvalidArgumentException('Cannot add cash to a closed session.');
        }

        $session->increment('cash_added', $amount);

        if ($notes) {
            $session->update(['notes' => $session->notes."\n[Added Cash: {$amount}] ".$notes]);
        }

        return $session->fresh();
    }

    public function removeCash(CashDrawerSession $session, float $amount, ?string $notes = null): CashDrawerSession
    {
        if (! $session->isOpen()) {
            throw new InvalidArgumentException('Cannot remove cash from a closed session.');
        }

        $session->increment('cash_removed', $amount);

        if ($notes) {
            $session->update(['notes' => $session->notes."\n[Removed Cash: {$amount}] ".$notes]);
        }

        return $session->fresh();
    }

    public function getActiveSession(int $tenantId, int $branchId): ?CashDrawerSession
    {
        return CashDrawerSession::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('status', 'open')
            ->with(['user', 'branch'])
            ->first();
    }

    public function getSessionHistory(int $tenantId, int $branchId, int $limit = 30)
    {
        return CashDrawerSession::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('status', 'closed')
            ->with(['user', 'branch'])
            ->orderBy('closed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
