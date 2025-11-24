<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Illuminate\Support\Facades\Auth;

#[Title('Success - Online&Go')]
class SuccessPage extends Component
{
    #[Url]
    public $session_id;

    public $latest_order;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->latest_order = Order::with('address')
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($this->session_id && $this->latest_order) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session_info = Session::retrieve($this->session_id);

            if ($session_info->payment_status !== 'paid') {
                $this->latest_order->payment_status = 'failed';
                $this->latest_order->save();
                redirect()->route('cancel')->send();
            } else {
                $this->latest_order->payment_status = 'paid';
                $this->latest_order->save();
            }
        }
    }

    public function render()
    {
        return view('livewire.success-page', [
            'order' => $this->latest_order,
        ]);
    }
}
