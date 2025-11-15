<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $module = $request->get('module', 'all');
        $action = $request->get('action', 'all');
        
        $query = AuditLog::with('user')->orderByDesc('created_at');
        
        if ($module !== 'all') {
            $query->where('module', $module);
        }
        
        if ($action !== 'all') {
            $query->where('action', $action);
        }
        
        $logs = $query->paginate(50);
        
        $modules = AuditLog::distinct()->pluck('module');
        $actions = AuditLog::distinct()->pluck('action');
        
        return view('admin.audit-logs.index', compact('logs', 'modules', 'actions', 'module', 'action'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('admin.audit-logs.show', compact('auditLog'));
    }
}
