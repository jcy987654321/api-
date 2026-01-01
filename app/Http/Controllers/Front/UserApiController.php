<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiKeyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserApiController extends Controller
{
    public function index()
    {
        $apiKeys = Auth::user()->apiKeys()
            ->withCount('logs')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pages.api-keys.index', compact('apiKeys'));
    }

    public function create()
    {
        return view('pages.api-keys.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rate_limit' => ['required', 'integer', 'min:1', 'max:100000'],
            'rate_window' => ['required', 'integer', 'min:1', 'max:86400'],
        ]);

        $secret = ApiKey::generateSecret();
        
        $apiKey = Auth::user()->apiKeys()->create([
            'name' => $validated['name'],
            'secret' => bcrypt($secret),
            'rate_limit' => $validated['rate_limit'],
            'rate_window' => $validated['rate_window'],
            'status' => 'active',
        ]);

        // Store plaintext values for displaying once
        session([
            'new_key' => $apiKey->key,
            'new_secret' => $secret,
            'success' => 'API 密钥生成成功！',
        ]);

        return redirect()->route('user.api-keys.index');
    }

    public function show($id)
    {
        $apiKey = Auth::user()->apiKeys()
            ->with(['rateLimit'])
            ->withCount('logs')
            ->findOrFail($id);

        $recentLogs = $apiKey->logs()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pages.api-keys.show', compact('apiKey', 'recentLogs'));
    }

    public function destroy($id)
    {
        $apiKey = Auth::user()->apiKeys()->findOrFail($id);
        $apiKey->delete();

        return redirect()->route('user.api-keys.index')
            ->with('success', 'API 密钥已删除');
    }

    public function regenerate($id)
    {
        $apiKey = Auth::user()->apiKeys()->findOrFail($id);
        
        $newSecret = $apiKey->regenerateSecret();
        
        session([
            'new_key' => $newSecret->plaintextKey,
            'new_secret' => $newSecret->plaintextSecret,
            'success' => 'API 密钥重新生成成功！',
        ]);

        return redirect()->route('user.api-keys.index');
    }

    public function stats($id)
    {
        $apiKey = Auth::user()->apiKeys()->findOrFail($id);

        $stats = [
            'today' => $apiKey->logs()->whereDate('created_at', today())->count(),
            'week' => $apiKey->logs()->whereBetween('created_at', [now()->subWeek(), now()])->count(),
            'month' => $apiKey->logs()->whereBetween('created_at', [now()->subMonth(), now()])->count(),
            'total' => $apiKey->logs()->count(),
            'avg_response_time' => round($apiKey->logs()->avg('response_time'), 2),
            'success_rate' => $apiKey->logs()->count() > 0 ? 
                round(($apiKey->logs()->where('status_code', '<', 400)->count() / $apiKey->logs()->count()) * 100, 2) : 0,
        ];

        $recentLogs = $apiKey->logs()
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at')
            ->get()
            ->groupBy(function($log) {
                return $log->created_at->format('Y-m-d');
            })
            ->map(function($logs) {
                return $logs->count();
            });

        return view('pages.api-keys.stats', compact('apiKey', 'stats', 'recentLogs'));
    }
}