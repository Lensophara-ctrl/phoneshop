<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    public function run()
    {
        // Get the first user (or create one if none exists)
        $user = User::first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@phoneshop.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        // Get the start of the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        
        // Create sales data for each week of the current month
        $salesData = [
            // Week 1
            ['days' => [1, 2, 3], 'amounts' => [1200, 850, 1500]],
            // Week 2
            ['days' => [8, 9, 10], 'amounts' => [2100, 1800, 2400]],
            // Week 3
            ['days' => [15, 16, 17], 'amounts' => [1600, 2200, 1900]],
            // Week 4
            ['days' => [22, 23, 24], 'amounts' => [2800, 2500, 3100]],
        ];

        $billNumber = 1000;

        foreach ($salesData as $week) {
            foreach ($week['days'] as $index => $day) {
                // Skip if the day is in the future
                if ($startOfMonth->copy()->addDays($day - 1)->isFuture()) {
                    continue;
                }

                $billNumber++;
                $amount = $week['amounts'][$index];
                $tax = $amount * 0.1; // 10% tax
                $total = $amount + $tax;

                Sale::create([
                    'bill_no' => 'BILL-' . $billNumber,
                    'user_id' => $user->id,
                    'customer_name' => 'Customer ' . $billNumber,
                    'customer_email' => 'customer' . $billNumber . '@example.com',
                    'customer_phone' => '012345' . str_pad($billNumber, 4, '0', STR_PAD_LEFT),
                    'subtotal' => $amount,
                    'tax' => $tax,
                    'total_price' => $total,
                    'payment_method' => ['cash', 'card', 'bakong'][array_rand(['cash', 'card', 'bakong'])],
                    'status' => 'completed',
                    'created_at' => $startOfMonth->copy()->addDays($day - 1)->setTime(rand(9, 17), rand(0, 59)),
                    'updated_at' => $startOfMonth->copy()->addDays($day - 1)->setTime(rand(9, 17), rand(0, 59)),
                ]);
            }
        }

        $this->command->info('✅ Created sample sales data for the current month!');
        $this->command->info('📊 Total sales created: ' . Sale::count());
        $this->command->info('💰 Total revenue: $' . number_format(Sale::sum('total_price'), 2));
    }
}
