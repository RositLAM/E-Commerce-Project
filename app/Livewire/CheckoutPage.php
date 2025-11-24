<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\CartManagement;
use App\Models\Address;
use App\Models\Order;
use Livewire\Attributes\Title;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;

#[Title('Checkout')]
class CheckoutPage extends Component
{
    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;

    public function mount()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();

        if (empty($cart_items)) {
            return redirect('/products');
        }

        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function placeOrder()
    {
        $this->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'street_address'  => 'required|string|max:255',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'zip_code'        => 'required|string|max:20',
            'payment_method'  => 'required|string|in:stripe,cod',
        ]);

        $cart_items = CartManagement::getCartItemsFromCookie();
        if (empty($cart_items)) {
            return redirect('/products');
        }

        $line_items = [];
        foreach ($cart_items as $item) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'php',
                    'unit_amount' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Create Order
        $order = Order::create([
            'user_id'        => Auth::id(),
            'grand_total'    => CartManagement::calculateGrandTotal($cart_items),
            'payment_method' => $this->payment_method,
            'payment_status' => 'pending',
            'status'         => 'new',
            'currency'       => 'php',
            'shipping_amount'=> 0,
            'shipping_method'=> 'none',
            'notes'          => 'Order placed by ' . Auth::user()->name,
        ]);

        // Create Address
        Address::create([
            'order_id'       => $order->id,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'phone'          => $this->phone,
            'street_address' => $this->street_address,
            'city'           => $this->city,
            'state'          => $this->state,
            'zip_code'       => $this->zip_code,
        ]);

        // Handle payment redirect
        $redirect_url = route('success'); // default for COD

        if ($this->payment_method === 'stripe') {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = Session::create([
                'customer_email' => Auth::user()->email,
                'line_items'     => $line_items,
                'mode'           => 'payment',
                'success_url'    => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'     => route('cancel'),
                'payment_method_types' => ['card'],
            ]);

            $redirect_url = $session->url;
        }

        // Save order items correctly
        $order_items = [];
        foreach ($cart_items as $item) {
            $order_items[] = [
                'product_name' => $item['name'], // match your order_items table column
                'unit_amount'  => $item['unit_amount'],
                'quantity'     => $item['quantity'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        $order->items()->createMany($order_items);

        // Clear cart
        CartManagement::clearCartItems();
        Mail::to(request()->user())->send(new OrderPlaced($order));
        return redirect($redirect_url);
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);

        return view('livewire.checkout-page', [
            'cart_items'  => $cart_items,
            'grand_total' => $grand_total,
        ]);
    }
}
