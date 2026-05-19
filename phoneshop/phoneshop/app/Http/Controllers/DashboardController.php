<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Phone;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = Category::count();
        $phones = Phone::count();
        $users = \App\Models\User::count();
        $totalRevenue = Sale::sum('total_price');
        $totalOrders = Sale::distinct('bill_no')->count('bill_no');

        // Get sales data for current month (grouped by week)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Get the first day of the current month
        $startOfMonth = now()->startOfMonth();
        
        // Calculate sales for each week of the month
        $salesData = [];
        for ($week = 0; $week < 4; $week++) {
            $weekStart = $startOfMonth->copy()->addWeeks($week);
            $weekEnd = $weekStart->copy()->addWeek()->subSecond();
            
            // Make sure we don't go beyond the current month
            if ($weekStart->month != $currentMonth) {
                $salesData[] = 0;
                continue;
            }
            
            if ($weekEnd->month != $currentMonth) {
                $weekEnd = $startOfMonth->copy()->endOfMonth();
            }
            
            $weekTotal = Sale::where('status', 'completed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_price');
            
            $salesData[] = $weekTotal ?? 0;
        }

        return view('dashboard', compact('categories', 'phones', 'users', 'totalRevenue', 'totalOrders', 'salesData'));
    }
}
