<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

#[Title('Products - Online&Go')]
class ProductsPage extends Component
{
    use WithPagination;


    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $sort = 'latest';

    public $price_range = 30000;


    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemsToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count)
             ->to(Navbar::class);

        LivewireAlert::title('Success')
            ->text('Product Added To The Cart Successfully!')
            ->position('bottom-end')
            ->toast()
            ->timer(3000)
            ->success()
            ->show();
    }


    public function someAction()
    {
        LivewireAlert::title('Hello')
            ->text('This is a v4 alert')
            ->success()
            ->show();
    }


    public function render()
    {
        $productQuery = Product::query()->where('is_active', 1);

        if (!empty($this->selected_categories)) {
            $productQuery->whereIn('category_id', $this->selected_categories);
        }

        if (!empty($this->selected_brands)) {
            $productQuery->whereIn('brand_id', $this->selected_brands);
        }

        if ($this->featured) {
            $productQuery->where('featured', 1);
        }

        if ($this->on_sale) {
            $productQuery->where('on_sale', 1);
        }

        if ($this->price_range) {
            $productQuery->whereBetween('price', [0, $this->price_range]);
        }

        if ($this->sort === 'latest') {
            $productQuery->latest();
        } elseif ($this->sort === 'price') {
            $productQuery->orderBy('price');
        }

        return view('livewire.products-page', [
            'products'   => $productQuery->paginate(9),
            'brands'     => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}
