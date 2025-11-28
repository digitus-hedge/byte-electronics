<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Products;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        // Total users (excluding admins)
        $totalUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();

        // Total products
        $totalProducts = Products::count();

        // Total orders
        $totalOrders = Order::count();

        // Orders from last week (past 7 days)
        $lastWeekOrders = Order::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        // New products from last week (past 7 days)
        $lastWeekProducts = Products::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        // dd($userId);
        return view('dashboard.dashboard', compact(
            'userId',
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'lastWeekOrders',
            'lastWeekProducts'
        ));
    }
}
