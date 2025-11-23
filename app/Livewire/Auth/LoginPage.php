<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Session; 



class LoginPage extends Component
{
    public $email;
    public $password;

    public function save()
    {
        $this->validate([
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|min:6|max:255',
        ]);

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
        session()->flash('error', 'Invalid Credentials');
        return;
        }


        return redirect()->intended();
    }

    public function render()
    {
        return view('livewire.auth.login-page', [
        'title' => 'Login',
        ]);
    }
}
