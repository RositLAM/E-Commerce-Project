<?php

namespace App\Livewire;

use App\Livewire\Partials\Navbar;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Helpers\CartManagement;

#[Title('Product Detail - Online&Go')]
class ProductDetailPage extends Component
{
    public $slug;
    public $quantity = 1;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    public function increaseQty()
    {
        $this->quantity++;
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemsToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count)
             ->to(Navbar::class);

        LivewireAlert::title('Success')
            ->text('Product added to cart!')
            ->position('bottom-end')
            ->toast()
            ->timer(3000)
            ->success()
            ->show();
    }

    public function render()
    {
        $product = Product::where('slug', $this->slug)
            ->where('is_active', 1)
            ->firstOrFail();

        return view('livewire.product-detail-page', [
            'product' => $product,
        ]);
    }
}
