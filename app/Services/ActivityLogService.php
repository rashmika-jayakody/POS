<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity. Call from controllers after the action is done.
     *
     * @param  string  $logType  e.g. 'sale_completed', 'refund_processed', 'product_created'
     * @param  string  $description  Human-readable description
     * @param  array<string, mixed>|null  $properties  Optional extra data (e.g. invoice_no, amount, old/new values)
     * @param  string|null  $subjectType  Optional model class (e.g. Product::class)
     * @param  int|string|null  $subjectId  Optional model id
     */
    public static function log(
        string $logType,
        string $description,
        ?array $properties = null,
        ?string $subjectType = null,
        int|string|null $subjectId = null
    ): void {
        $user = Auth::user();
        $request = app()->has(Request::class) ? request() : null;

        ActivityLog::create([
            'tenant_id' => $user?->tenant_id,
            'user_id' => $user?->id,
            'log_type' => $logType,
            'description' => $description,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId !== null ? (string) $subjectId : null,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
