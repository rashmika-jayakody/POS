<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashDrawerSession;
use App\Services\CashDrawerSessionService;
use Illuminate\Http\Request;

class CashDrawerSessionController extends Controller
{
    protected CashDrawerSessionService $sessionService;

    public function __construct(CashDrawerSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $tenantId = $user->tenant_id;
        $branchId = $user->branch_id;

        $branches = Branch::where('tenant_id', $tenantId)->orderBy('name')->get();

        $activeSession = $this->sessionService->getActiveSession($tenantId, $branchId);
        $history = $this->sessionService->getSessionHistory($tenantId, $branchId);

        $currencySymbol = $user->tenant?->businessSetting?->currency_symbol ?? 'Rs';

        return view('cash-drawer-sessions.index', compact('activeSession', 'history', 'branches', 'currencySymbol'));
    }

    public function open(Request $request)
    {
        $validated = $request->validate([
            'opening_balance' => 'required|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $tenantId = $user->tenant_id;
        $branchId = $validated['branch_id'] ?? $user->branch_id;

        if (! $branchId) {
            $branch = Branch::where('tenant_id', $tenantId)->first();
            $branchId = $branch?->id;
        }

        if (! $branchId) {
            return response()->json([
                'success' => false,
                'message' => 'No branch found for this user.',
            ], 400);
        }

        try {
            $session = $this->sessionService->openSession(
                $tenantId,
                $branchId,
                $user->id,
                (float) $validated['opening_balance'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash drawer opened successfully.',
                'session' => $session,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function close(Request $request, CashDrawerSession $session)
    {
        $validated = $request->validate([
            'closing_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($session->tenant_id !== $request->user()->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        try {
            $session = $this->sessionService->closeSession(
                $session,
                (float) $validated['closing_balance'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash drawer closed successfully.',
                'session' => $session->load(['user', 'branch']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(CashDrawerSession $session)
    {
        if ($session->tenant_id !== request()->user()->tenant_id) {
            abort(403);
        }

        $totals = $this->sessionService->calculateSessionTotals($session);
        $session->load(['user', 'branch', 'sales.items.product', 'restaurantOrders.items.product', 'refunds.items.product']);

        $currencySymbol = request()->user()->tenant?->businessSetting?->currency_symbol ?? 'Rs';

        return view('cash-drawer-sessions.show', compact('session', 'totals', 'currencySymbol'));
    }

    public function status(Request $request)
    {
        $user = $request->user();
        $session = $this->sessionService->getActiveSession($user->tenant_id, $user->branch_id);

        if (! $session) {
            return response()->json([
                'is_open' => false,
                'session' => null,
            ]);
        }

        $totals = $this->sessionService->calculateSessionTotals($session);
        $expectedBalance = $this->sessionService->calculateExpectedBalance($session, $totals);

        return response()->json([
            'is_open' => true,
            'session' => $session,
            'totals' => $totals,
            'expected_balance' => $expectedBalance,
        ]);
    }

    public function addCash(Request $request, CashDrawerSession $session)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($session->tenant_id !== $request->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $session = $this->sessionService->addCash(
                $session,
                (float) $validated['amount'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash added successfully.',
                'session' => $session,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function removeCash(Request $request, CashDrawerSession $session)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($session->tenant_id !== $request->user()->tenant_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        try {
            $session = $this->sessionService->removeCash(
                $session,
                (float) $validated['amount'],
                $validated['notes'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash removed successfully.',
                'session' => $session,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
