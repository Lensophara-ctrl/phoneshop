<?php

use Livewire\Component;
use App\Models\Sale;
use Illuminate\Support\Collection;

new class extends Component
{
    public $filter = '';
    public $date = '';

    public function with()
    {
        $query = Sale::with(['items.phone', 'user'])
            ->where('status', 'completed');

        if ($this->filter) {
            if ($this->filter == 'today') {
                $query->whereDate('created_at', now()->today());
            } elseif ($this->filter == 'this_month') {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            } elseif ($this->filter == 'this_year') {
                $query->whereYear('created_at', now()->year);
            }
        }

        if ($this->date) {
            $query->whereDate('created_at', $this->date);
        }

        return [
            'sales' => $query->latest()->get(),
        ];
    }
    
    public function clearFilters()
    {
        $this->filter = '';
        $this->date = '';
    }
};
?>

<div wire:poll.5s>
    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Quick Filter</label>
                    <select wire:model.live="filter" class="form-select border-0 bg-light">
                        <option value="">All Transactions</option>
                        <option value="today">Today</option>
                        <option value="this_month">This Month</option>
                        <option value="this_year">This Year</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Specific Date</label>
                    <input type="date" wire:model.live="date" class="form-control border-0 bg-light">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    @if($filter || $date)
                        <button wire:click="clearFilters" class="btn btn-light w-100 fw-bold">
                            <i class="fa-solid fa-rotate-left me-2"></i>Clear
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card p-4 border-0 shadow-sm bg-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-3 p-3 me-3">
                        <i class="fa-solid fa-receipt fs-4"></i>
                    </div>
                    <div>
                        <div class="text-white-50 small">Total Orders</div>
                        <div class="h4 fw-bold mb-0">{{ $sales->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                        <i class="fa-solid fa-money-bill-trend-up fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Revenue</div>
                        <div class="h4 fw-bold mb-0">${{ number_format($sales->sum('total_price'), 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                        <i class="fa-solid fa-users fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Unique Customers</div>
                        <div class="h4 fw-bold mb-0">{{ $sales->unique('user_id')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4 border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                        <i class="fa-solid fa-box-open fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Products Sold</div>
                        <div class="h4 fw-bold mb-0">{{ $sales->sum(fn($sale) => $sale->items->sum('qty')) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase fw-bold">
                        <tr>
                            <th class="px-4 py-3 border-bottom">Bill / Order Info</th>
                            <th class="py-3 border-bottom">Customer</th>
                            <th class="py-3 border-bottom">Items Count</th>
                            <th class="py-3 border-bottom text-end">Total Amount</th>
                            <th class="py-3 border-bottom text-center">Payment</th>
                            <th class="px-4 py-3 border-bottom text-end">Order Date</th>
                            <th class="py-3 border-bottom"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td class="px-4 py-4">
                                <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 rounded-pill small">
                                    #{{ $sale->bill_no }}
                                </span>
                            </td>
                            <td class="py-4">
                                @if($sale->user)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ substr($sale->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $sale->user->name }}</div>
                                            <div class="small text-muted">{{ $sale->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted italic">Guest Customer</span>
                                @endif
                            </td>
                            <td class="py-4">
                                <div class="fw-semibold">{{ $sale->items->count() }} Items</div>
                                <div class="small text-muted">({{ $sale->items->sum('qty') }} units total)</div>
                            </td>
                            <td class="py-4 text-end">
                                <div class="fw-bold text-primary h6 mb-0">${{ number_format($sale->total_price, 2) }}</div>
                            </td>
                            <td class="py-4 text-center text-uppercase">
                                <span class="badge bg-info-subtle text-info border px-2 py-1 small">{{ str_replace('_', ' ', $sale->payment_method) }}</span>
                            </td>
                            <td class="px-4 py-4 text-end">
                                <div class="text-dark fw-medium small">{{ $sale->created_at->format('M d, Y') }}</div>
                                <div class="text-muted x-small" style="font-size: 0.75rem;">{{ $sale->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="py-4 pe-4 text-end">
                                <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary" wire:navigate>
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No sales found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
