<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\BaseService;
use App\Services\DashboardService;
use App\Models\User;
use App\Models\Team;
use App\Models\Product;
use App\Enums\DeleteFlag;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
        $this->service = $dashboardService;
    }

    protected function getService(): BaseService
    {
        return $this->dashboardService;
    }

    public function index(): JsonResponse
    {
        try {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();
            
            $lastYear = $now->copy()->subYear()->year;
            $currentYear = $now->year;

            // 1. Basic Stats
            $totalUsers = User::where('del_flag', DeleteFlag::OFF)->count();
            $totalTeams = Team::where('del_flag', DeleteFlag::OFF)->count();
            $totalProducts = Product::where('del_flag', DeleteFlag::OFF)->count();

            // 2. User trends in current month
            $userTrends = DB::table('users')
                ->select(DB::raw('DAY(created_at) as day'), DB::raw('count(*) as count'))
                ->where('del_flag', DeleteFlag::OFF->value)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('count', 'day')
                ->all();

            // Fill missing days with 0
            $daysInMonth = $now->daysInMonth;
            $formattedTrends = [];
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $formattedTrends[] = $userTrends[$i] ?? 0;
            }

            // 3. Yearly Comparison
            $usersLastYear = User::whereYear('created_at', $lastYear)->where('del_flag', DeleteFlag::OFF)->count();
            $usersCurrentYear = User::whereYear('created_at', $currentYear)->where('del_flag', DeleteFlag::OFF)->count();
            
            $productsLastYear = Product::whereYear('created_at', $lastYear)->where('del_flag', DeleteFlag::OFF)->count();
            $productsCurrentYear = Product::whereYear('created_at', $currentYear)->where('del_flag', DeleteFlag::OFF)->count();

            return $this->successResponse([
                'totalUsers' => $totalUsers,
                'totalTeams' => $totalTeams,
                'totalProducts' => $totalProducts,
                'userTrends' => $formattedTrends,
                'yearlyComparison' => [
                    'users' => [
                        'currentYear' => $usersCurrentYear,
                        'lastYear' => $usersLastYear,
                    ],
                    'products' => [
                        'currentYear' => $productsCurrentYear,
                        'lastYear' => $productsLastYear,
                    ],
                ],
            ], 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return $this->errorResponse('Failed to fetch dashboard stats', ['error' => $e->getMessage()]);
        }
    }
}
