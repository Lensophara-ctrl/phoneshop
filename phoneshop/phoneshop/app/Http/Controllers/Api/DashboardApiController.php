<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Phone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardApiController extends Controller
{
    public function stats()
    {
        $totalSales = Sale::where('status', 'completed')->sum('total_price');
        $todaySales = Sale::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_price');
        $totalOrders = Sale::where('status', 'completed')->count();
        $todayOrders = Sale::where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();
        $lowStockProducts = Phone::where('qty', '<', 5)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_sales' => $totalSales,
                'today_sales' => $todaySales,
                'total_orders' => $totalOrders,
                'today_orders' => $todayOrders,
                'low_stock_products' => $lowStockProducts,
            ],
            'message' => 'Dashboard stats retrieved successfully',
        ]);
    }

    public function monthlySales(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // Get daily sales for the specified month
        $sales = Sale::where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sales,
            'message' => 'Monthly sales retrieved successfully',
        ]);
    }

    public function orders(Request $request)
    {
        $query = Sale::with(['items.phone', 'user'])
            ->where('status', 'completed')
            ->latest();

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Orders retrieved successfully',
        ]);
    }

    public function orderDetails($id)
    {
        $order = Sale::with(['items.phone', 'user'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order details retrieved successfully',
        ]);
    }

    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:phones,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        // Calculate total
        $total = 0;
        $items = [];

        foreach ($validated['items'] as $item) {
            $phone = Phone::find($item['product_id']);
            
            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => "Product {$item['product_id']} not found",
                ], Response::HTTP_NOT_FOUND);
            }

            if ($phone->qty < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$phone->name}",
                ], Response::HTTP_BAD_REQUEST);
            }

            $subtotal = $phone->price * $item['quantity'];
            $total += $subtotal;

            $items[] = [
                'phone' => $phone,
                'quantity' => $item['quantity'],
                'price' => $phone->price,
                'subtotal' => $subtotal,
            ];
        }

        // Create sale
        $sale = Sale::create([
            'user_id' => $request->api_user->id ?? null,
            'bill_no' => 'API-' . strtoupper(uniqid()),
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'total_price' => $total,
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
        ]);

        // Create sale items and reduce stock
        foreach ($items as $item) {
            $sale->items()->create([
                'phone_id' => $item['phone']->id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ]);

            // Reduce stock
            $item['phone']->decrement('qty', $item['quantity']);
        }

        return response()->json([
            'success' => true,
            'data' => $sale->load('items.phone'),
            'message' => 'Order created successfully',
        ], Response::HTTP_CREATED);
    }
}
