<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Phone;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Report 1: Sales Summary Report
     */
    public function salesSummary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $data = [
            'total_sales' => Sale::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_price'),
            'total_orders' => Sale::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'average_order_value' => Sale::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('total_price'),
            'total_items_sold' => SaleItem::whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })->sum('qty'),
            'pending_orders' => Sale::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'approved_orders' => Sale::where('approval_status', 'approved')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'rejected_orders' => Sale::where('approval_status', 'rejected')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
        ];

        return $this->formatResponse('Sales Summary Report', $data, $startDate, $endDate);
    }

    /**
     * Report 2: Top Selling Products
     */
    public function topSellingProducts(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $limit = $request->get('limit', 10);

        $data = SaleItem::select('phone_id', DB::raw('SUM(qty) as total_quantity'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with('phone.category')
            ->groupBy('phone_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'product_id' => $item->phone_id,
                    'product_name' => $item->phone->name ?? 'Unknown',
                    'category' => $item->phone->category->name ?? 'N/A',
                    'quantity_sold' => $item->total_quantity,
                    'total_revenue' => number_format($item->total_revenue, 2),
                    'current_stock' => $item->phone->qty ?? 0,
                ];
            });

        return $this->formatResponse('Top Selling Products', $data, $startDate, $endDate);
    }

    /**
     * Report 3: Revenue by Category
     */
    public function revenueByCategory(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $data = Category::select('categories.id', 'categories.name')
            ->leftJoin('phones', 'categories.id', '=', 'phones.category_id')
            ->leftJoin('sale_items', 'phones.id', '=', 'sale_items.phone_id')
            ->leftJoin('sales', function($join) use ($startDate, $endDate) {
                $join->on('sale_items.sale_id', '=', 'sales.id')
                     ->where('sales.status', '=', 'completed')
                     ->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('SUM(sale_items.subtotal) as total_revenue')
            ->selectRaw('SUM(sale_items.qty) as total_quantity')
            ->selectRaw('COUNT(DISTINCT sales.id) as order_count')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'category_id' => $item->id,
                    'category_name' => $item->name,
                    'total_revenue' => number_format($item->total_revenue ?? 0, 2),
                    'total_quantity' => $item->total_quantity ?? 0,
                    'order_count' => $item->order_count ?? 0,
                ];
            });

        return $this->formatResponse('Revenue by Category', $data, $startDate, $endDate);
    }

    /**
     * Report 4: Daily Sales Report
     */
    public function dailySales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $data = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('SUM(total_price) as total_revenue')
            ->selectRaw('AVG(total_price) as average_order_value')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d, Y'),
                    'order_count' => $item->order_count,
                    'total_revenue' => number_format($item->total_revenue, 2),
                    'average_order_value' => number_format($item->average_order_value, 2),
                ];
            });

        return $this->formatResponse('Daily Sales Report', $data, $startDate, $endDate);
    }

    /**
     * Report 5: Customer Report
     */
    public function customerReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $limit = $request->get('limit', 20);

        $data = User::select('users.id', 'users.name', 'users.email')
            ->leftJoin('sales', function($join) use ($startDate, $endDate) {
                $join->on('users.id', '=', 'sales.user_id')
                     ->where('sales.status', '=', 'completed')
                     ->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('users.id', 'users.name', 'users.email')
            ->selectRaw('COUNT(sales.id) as total_orders')
            ->selectRaw('SUM(sales.total_price) as total_spent')
            ->selectRaw('AVG(sales.total_price) as average_order_value')
            ->selectRaw('MAX(sales.created_at) as last_order_date')
            ->having('total_orders', '>', 0)
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'customer_id' => $item->id,
                    'customer_name' => $item->name,
                    'customer_email' => $item->email,
                    'total_orders' => $item->total_orders,
                    'total_spent' => number_format($item->total_spent, 2),
                    'average_order_value' => number_format($item->average_order_value, 2),
                    'last_order_date' => $item->last_order_date ? Carbon::parse($item->last_order_date)->format('M d, Y') : 'N/A',
                ];
            });

        return $this->formatResponse('Customer Report', $data, $startDate, $endDate);
    }

    /**
     * Report 6: Inventory Report
     */
    public function inventoryReport(Request $request)
    {
        $lowStockThreshold = $request->get('low_stock_threshold', 10);

        $data = Phone::with('category')
            ->select('phones.*')
            ->selectRaw('(SELECT COALESCE(SUM(sale_items.qty), 0) FROM sale_items WHERE sale_items.phone_id = phones.id) as total_sold')
            ->get()
            ->map(function($phone) use ($lowStockThreshold) {
                return [
                    'product_id' => $phone->id,
                    'product_name' => $phone->name,
                    'category' => $phone->category->name ?? 'N/A',
                    'current_stock' => $phone->qty,
                    'price' => number_format($phone->price, 2),
                    'total_sold' => $phone->total_sold,
                    'stock_value' => number_format($phone->qty * $phone->price, 2),
                    'status' => $phone->qty == 0 ? 'Out of Stock' : ($phone->qty <= $lowStockThreshold ? 'Low Stock' : 'In Stock'),
                ];
            })
            ->sortBy('current_stock');

        return $this->formatResponse('Inventory Report', $data->values());
    }

    /**
     * Report 7: Payment Method Report
     */
    public function paymentMethodReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $data = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(total_price) as total_revenue')
            ->selectRaw('AVG(total_price) as average_transaction')
            ->groupBy('payment_method')
            ->orderBy('total_revenue', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'payment_method' => strtoupper(str_replace('_', ' ', $item->payment_method)),
                    'transaction_count' => $item->transaction_count,
                    'total_revenue' => number_format($item->total_revenue, 2),
                    'average_transaction' => number_format($item->average_transaction, 2),
                    'percentage' => 0, // Will be calculated below
                ];
            });

        // Calculate percentages
        $totalRevenue = $data->sum(fn($item) => floatval(str_replace(',', '', $item['total_revenue'])));
        $data = $data->map(function($item) use ($totalRevenue) {
            $revenue = floatval(str_replace(',', '', $item['total_revenue']));
            $item['percentage'] = $totalRevenue > 0 ? number_format(($revenue / $totalRevenue) * 100, 2) : 0;
            return $item;
        });

        return $this->formatResponse('Payment Method Report', $data, $startDate, $endDate);
    }

    /**
     * Report 8: Monthly Comparison Report
     */
    public function monthlyComparison(Request $request)
    {
        $months = $request->get('months', 6);
        $data = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();

            $monthData = [
                'month' => $startDate->format('M Y'),
                'total_orders' => Sale::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'total_revenue' => Sale::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_price'),
                'average_order_value' => Sale::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->avg('total_price'),
                'new_customers' => User::whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
            ];

            $monthData['total_revenue'] = number_format($monthData['total_revenue'], 2);
            $monthData['average_order_value'] = number_format($monthData['average_order_value'] ?? 0, 2);

            $data[] = $monthData;
        }

        return $this->formatResponse('Monthly Comparison Report', $data);
    }

    /**
     * Report 9: Order Status Report
     */
    public function orderStatusReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $statusData = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', 'approval_status')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(total_price) as total_value')
            ->groupBy('status', 'approval_status')
            ->get();

        $data = [
            'by_payment_status' => Sale::whereBetween('created_at', [$startDate, $endDate])
                ->select('status')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(total_price) as total_value')
                ->groupBy('status')
                ->get()
                ->map(function($item) {
                    return [
                        'status' => ucfirst($item->status),
                        'count' => $item->count,
                        'total_value' => number_format($item->total_value, 2),
                    ];
                }),
            'by_approval_status' => Sale::whereBetween('created_at', [$startDate, $endDate])
                ->select('approval_status')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('SUM(total_price) as total_value')
                ->groupBy('approval_status')
                ->get()
                ->map(function($item) {
                    return [
                        'status' => ucfirst($item->approval_status),
                        'count' => $item->count,
                        'total_value' => number_format($item->total_value, 2),
                    ];
                }),
            'summary' => [
                'total_orders' => Sale::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed_orders' => Sale::where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
                'pending_orders' => Sale::where('status', 'pending')
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
                'approved_orders' => Sale::where('approval_status', 'approved')
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
                'pending_approval' => Sale::where('approval_status', 'pending')
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
                'rejected_orders' => Sale::where('approval_status', 'rejected')
                    ->whereBetween('created_at', [$startDate, $endDate])->count(),
            ]
        ];

        return $this->formatResponse('Order Status Report', $data, $startDate, $endDate);
    }

    /**
     * Report 10: Profit Analysis Report
     */
    public function profitAnalysis(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $sales = Sale::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('items.phone')
            ->get();

        $totalRevenue = $sales->sum('total_price');
        $totalTax = $sales->sum('tax');
        $totalOrders = $sales->count();

        // Calculate by category
        $categoryData = [];
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $categoryName = $item->phone->category->name ?? 'Uncategorized';
                
                if (!isset($categoryData[$categoryName])) {
                    $categoryData[$categoryName] = [
                        'revenue' => 0,
                        'quantity' => 0,
                        'orders' => 0,
                    ];
                }
                
                $categoryData[$categoryName]['revenue'] += $item->subtotal;
                $categoryData[$categoryName]['quantity'] += $item->qty;
            }
        }

        $data = [
            'summary' => [
                'total_revenue' => number_format($totalRevenue, 2),
                'total_tax_collected' => number_format($totalTax, 2),
                'net_revenue' => number_format($totalRevenue - $totalTax, 2),
                'total_orders' => $totalOrders,
                'average_order_value' => number_format($totalOrders > 0 ? $totalRevenue / $totalOrders : 0, 2),
            ],
            'by_category' => collect($categoryData)->map(function($data, $category) {
                return [
                    'category' => $category,
                    'revenue' => number_format($data['revenue'], 2),
                    'quantity_sold' => $data['quantity'],
                ];
            })->sortByDesc('revenue')->values(),
            'by_currency' => Sale::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('currency')
                ->selectRaw('COUNT(*) as order_count')
                ->selectRaw('SUM(total_price) as total_revenue')
                ->groupBy('currency')
                ->get()
                ->map(function($item) {
                    return [
                        'currency' => $item->currency,
                        'order_count' => $item->order_count,
                        'total_revenue' => number_format($item->total_revenue, 2),
                    ];
                }),
        ];

        return $this->formatResponse('Profit Analysis Report', $data, $startDate, $endDate);
    }

    /**
     * Format response helper
     */
    private function formatResponse($reportName, $data, $startDate = null, $endDate = null)
    {
        $response = [
            'success' => true,
            'report_name' => $reportName,
            'data' => $data,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        if ($startDate && $endDate) {
            $response['period'] = [
                'start_date' => Carbon::parse($startDate)->format('M d, Y'),
                'end_date' => Carbon::parse($endDate)->format('M d, Y'),
            ];
        }

        return response()->json($response);
    }

    /**
     * Lock customer report
     */
    public function lockCustomerReport(User $customer)
    {
        try {
            // Create a locked report record
            DB::table('locked_reports')->insert([
                'user_id' => $customer->id,
                'report_type' => 'customer',
                'locked_by' => auth()->id(),
                'locked_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer report locked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download customer invoice
     */
    public function downloadCustomerInvoice(Request $request, User $customer)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $sales = Sale::where('user_id', $customer->id)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('items.phone')
            ->get();

        $totalRevenue = $sales->sum('total_price');
        $totalOrders = $sales->count();

        // Generate HTML invoice
        $html = view('reports.customer-invoice', compact('customer', 'sales', 'totalRevenue', 'totalOrders', 'startDate', 'endDate'))->render();

        // Return as downloadable HTML
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="customer-invoice-' . $customer->id . '-' . date('Y-m-d') . '.html"');
    }

    /**
     * Download daily invoice
     */
    public function downloadDailyInvoice($date)
    {
        $sales = Sale::where('status', 'completed')
            ->whereDate('created_at', $date)
            ->with('items.phone', 'user')
            ->get();

        $totalRevenue = $sales->sum('total_price');
        $totalOrders = $sales->count();

        // Generate HTML invoice
        $html = view('reports.daily-invoice', compact('sales', 'totalRevenue', 'totalOrders', 'date'))->render();

        // Return as downloadable HTML
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="daily-invoice-' . $date . '.html"');
    }
}
