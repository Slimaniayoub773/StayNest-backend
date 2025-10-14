<?php

namespace App\Models\Traits;

use Illuminate\Auth\Passwords\CanResetPassword as BaseCanResetPassword;
use Illuminate\Support\Facades\Password;

trait CanResetGuestPassword
{
    use BaseCanResetPassword;

    public function broker()
    {
        return Password::broker('guests');
    }
}