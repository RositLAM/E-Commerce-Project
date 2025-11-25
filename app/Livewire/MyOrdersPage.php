<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Order;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Title('My Orders')]
class MyOrdersPage extends Component
{
    use WithPagination;

    public function render()
    {
        $userId = Auth::id(); // safest

        $my_orders = Order::where('user_id', $userId)
            ->latest()
            ->paginate(5);

        return view('livewire.my-orders-page', [
            'orders' => $my_orders,
        ]);
    }
}
