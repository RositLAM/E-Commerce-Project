<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

#[Title('Register')]
class RegisterPage extends Component
{
    public $name;
    public $email;
    public $password;

    public function save(){
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6|max:255',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);


        return redirect()->intended();
    }

    public function render()
    {
        return view('livewire.auth.register-page');
    }
}

