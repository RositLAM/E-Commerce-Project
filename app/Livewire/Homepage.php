<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Brand;
use App\Models\Category;

#[Title('Home Page - Online&Go')]
class Homepage extends Component
{
    public function render()
    {
        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        return view('livewire.homepage', [
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }
}
