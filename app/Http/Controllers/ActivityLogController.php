<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs with optional filters (user, date range, log type).
     * Access controlled by route middleware: auth + permission:view activity log
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user:id,name,email', 'tenant:id,name'])
            ->orderByDesc('created_at');

        $user = auth()->user();
        if (! $user->hasRole('system_owner')) {
            $query->where(function ($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id)
                    ->orWhereNull('tenant_id');
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('log_type')) {
            $query->where('log_type', $request->log_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        $logTypes = ActivityLog::select('log_type')
            ->distinct()
            ->orderBy('log_type')
            ->pluck('log_type');

        $usersForFilter = User::select('id', 'name', 'email')
            ->when(! $user->hasRole('system_owner'), fn ($q) => $q->where('tenant_id', $user->tenant_id))
            ->orderBy('name')
            ->get();

        return view('activity-logs.index', compact('logs', 'logTypes', 'usersForFilter'));
    }
}
