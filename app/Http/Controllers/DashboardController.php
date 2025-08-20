<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Dataset;
use App\Models\Infografis;
use App\Models\Mapset;
use App\Models\Visualisasi;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get stats based on user role
        $stats = $this->getStatsForRole($user);
        
        // Get recent activities based on user role
        $recentActivities = $this->getRecentActivitiesForRole($user);
        
        // Get chart data for super admin
        $chartsData = [];
        if ($user->hasRole('super-admin')) {
            $chartsData = $this->getChartsData();
        }

        return view('dashboard', compact('stats', 'recentActivities', 'chartsData'));
    }

    private function getStatsForRole($user)
    {
        $stats = [];

        if ($user->hasRole('super-admin')) {
            // Super Admin sees all data
            $stats['datasets'] = [
                'total' => Dataset::count(),
                'pending' => Dataset::where('approval_status', 'pending')->count(),
                'approved' => Dataset::where('approval_status', 'approved')->count(),
                'rejected' => Dataset::where('approval_status', 'rejected')->count(),
                'total_views' => Dataset::sum('view_count') ?? 0,
                'total_downloads' => Dataset::sum('download_count') ?? 0,
            ];

            $stats['infografis'] = [
                'total' => Infografis::count(),
                'public' => Infografis::where('is_public', true)->count(),
                'total_views' => Infografis::sum('views') ?? 0,
                'total_downloads' => Infografis::sum('downloads') ?? 0,
            ];

            $stats['mapsets'] = [
                'total' => Mapset::count(),
                'total_views' => Mapset::sum('views') ?? 0,
                'total_downloads' => 0,
            ];

            $stats['visualisasi'] = [
                'total' => Visualisasi::count(),
                'total_views' => Visualisasi::sum('views') ?? 0,
                'total_downloads' => 0,
            ];

            $stats['users'] = [
                'total' => User::count(),
                'opd' => User::whereHas('roles', function($query) {
                    $query->where('name', 'opd');
                })->count(),
            ];

        } elseif ($user->hasRole('opd')) {
            // OPD sees only their own data
            $stats['datasets'] = [
                'total' => Dataset::where('user_id', $user->id)->count(),
                'pending' => Dataset::where('user_id', $user->id)->where('approval_status', 'pending')->count(),
                'approved' => Dataset::where('user_id', $user->id)->where('approval_status', 'approved')->count(),
                'rejected' => Dataset::where('user_id', $user->id)->where('approval_status', 'rejected')->count(),
                'total_views' => Dataset::where('user_id', $user->id)->sum('view_count') ?? 0,
                'total_downloads' => Dataset::where('user_id', $user->id)->sum('download_count') ?? 0,
            ];

            $stats['infografis'] = [
                'total' => Infografis::where('user_id', $user->id)->count(),
                'public' => Infografis::where('user_id', $user->id)->where('is_public', true)->count(),
                'total_views' => Infografis::where('user_id', $user->id)->sum('views') ?? 0,
                'total_downloads' => Infografis::where('user_id', $user->id)->sum('downloads') ?? 0,
            ];

        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung Jawab sees approved/public data
            $stats['datasets'] = [
                'total' => Dataset::where('approval_status', 'approved')->count(),
                'total_views' => Dataset::where('approval_status', 'approved')->sum('view_count') ?? 0,
                'total_downloads' => Dataset::where('approval_status', 'approved')->sum('download_count') ?? 0,
            ];

            $stats['infografis'] = [
                'total' => Infografis::where('is_public', true)->count(),
                'total_views' => Infografis::where('is_public', true)->sum('views') ?? 0,
                'total_downloads' => Infografis::where('is_public', true)->sum('downloads') ?? 0,
            ];

            $stats['mapsets'] = [
                'total' => Mapset::count(),
                'total_views' => Mapset::sum('views') ?? 0,
                'total_downloads' => 0,
            ];

            $stats['visualisasi'] = [
                'total' => Visualisasi::count(),
                'total_views' => Visualisasi::sum('views') ?? 0,
                'total_downloads' => 0,
            ];

            $stats['recent_activity'] = [
                'new_datasets_this_month' => Dataset::where('approval_status', 'approved')
                    ->whereMonth('approved_at', Carbon::now()->month)->count(),
            ];

        } elseif ($user->hasRole('reviewer')) {
            // Reviewer sees approval-related data
            $stats['datasets'] = [
                'pending' => Dataset::where('approval_status', 'pending')->count(),
                'approved_today' => Dataset::where('approval_status', 'approved')
                    ->whereDate('approved_at', Carbon::today())->count(),
                'rejected_today' => Dataset::where('approval_status', 'rejected')
                    ->whereDate('updated_at', Carbon::today())->count(),
                'total_approved' => Dataset::where('approval_status', 'approved')->count(),
                'total_rejected' => Dataset::where('approval_status', 'rejected')->count(),
            ];

            $stats['approval_activity'] = [
                'my_approvals' => Dataset::where('approved_by', $user->id)->count(),
                'approved_this_week' => Dataset::where('approval_status', 'approved')
                    ->whereBetween('approved_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->count(),
            ];

            $stats['recent_activity'] = [
                'datasets_submitted_today' => Dataset::where('approval_status', 'pending')
                    ->whereDate('created_at', Carbon::today())->count(),
            ];
        }

        return $stats;
    }

    private function getRecentActivitiesForRole($user)
    {
        $activities = [];

        if ($user->hasRole('super-admin')) {
            $activities['recent_datasets'] = Dataset::with('user')
                ->latest()
                ->take(10)
                ->get();

        } elseif ($user->hasRole('opd')) {
            $activities['recent_datasets'] = Dataset::where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();

            $activities['pending_review'] = Dataset::where('approval_status', 'pending')
                ->with('user')
                ->latest()
                ->take(5)
                ->get();

        } elseif ($user->hasRole('penanggung-jawab')) {
            $activities['recent_approved'] = Dataset::where('approval_status', 'approved')
                ->with('user')
                ->latest('approved_at')
                ->take(10)
                ->get();

        } elseif ($user->hasRole('reviewer')) {
            $activities['pending_review'] = Dataset::where('approval_status', 'pending')
                ->with('user')
                ->latest()
                ->take(10)
                ->get();

            $activities['recent_approved'] = Dataset::where('approval_status', 'approved')
                ->with('user')
                ->latest('approved_at')
                ->take(5)
                ->get();
        }

        return $activities;
    }

    private function getChartsData()
    {
        // Approval trend for last 7 days
        $approvalTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $approvalTrend[] = [
                'date' => $date->format('M d'),
                'approved' => Dataset::where('approval_status', 'approved')
                    ->whereDate('approved_at', $date)
                    ->count(),
                'rejected' => Dataset::where('approval_status', 'rejected')
                    ->whereDate('updated_at', $date)
                    ->count(),
            ];
        }

        // Topic distribution
        $topicDistribution = Dataset::select('topic', DB::raw('count(*) as count'))
            ->where('approval_status', 'approved')
            ->groupBy('topic')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get()
            ->toArray();

        return [
            'approval_trend' => $approvalTrend,
            'topic_distribution' => $topicDistribution,
        ];
    }

    public function apiStats()
    {
        // API endpoint for refreshing stats
        $user = Auth::user();
        $stats = $this->getStatsForRole($user);
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'timestamp' => now(),
        ]);
    }
}