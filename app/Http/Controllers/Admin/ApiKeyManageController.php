<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiKeyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiKeyManageController extends Controller
{
    public function index(Request $request)
    {
        $query = ApiKey::with(['user' => function($q) {
            $q->select('id', 'name', 'email');
        }])->withCount('logs');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('key', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $apiKeys = $query->orderBy('created_at', 'desc')
                         ->paginate(20)
                         ->appends($request->all());

        return view('admin.api-keys.index', compact('apiKeys'));
    }

    public function show($id)
    {
        $apiKey = ApiKey::with(['user', 'rateLimit'])
                       ->withCount('logs')
                       ->findOrFail($id);

        $recentLogs = $apiKey->logs()
                           ->orderBy('created_at', 'desc')
                           ->limit(20)
                           ->get();

        $stats = [
            'today' => $apiKey->logs()->whereDate('created_at', today())->count(),
            'week' => $apiKey->logs()->whereBetween('created_at', [now()->subWeek(), now()])->count(),
            'month' => $apiKey->logs()->whereBetween('created_at', [now()->subMonth(), now()])->count(),
        ];

        return view('admin.api-keys.show', compact('apiKey', 'recentLogs', 'stats'));
    }

    public function activate($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->update(['status' => 'active']);

        return redirect()->route('admin.api-keys.show', $apiKey->id)
                        ->with('success', 'API 密钥已激活');
    }

    public function deactivate($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->update(['status' => 'inactive']);

        return redirect()->route('admin.api-keys.show', $apiKey->id)
                        ->with('success', 'API 密钥已禁用');
    }

    public function destroy($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $apiKey->delete();

        return redirect()->route('admin.api-keys.index')
                        ->with('success', 'API 密钥已删除');
    }

    public function logs(Request $request)
    {
        $query = ApiKeyLog::with(['apiKey.user'])
                         ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('endpoint', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'success') {
                $query->where('status_code', '<', 400);
            } elseif ($status === 'error') {
                $query->where('status_code', '>=', 400);
            }
        }

        $logs = $query->paginate(30)->appends($request->all());

        return view('admin.api-logs.index', compact('logs'));
    }
}