<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;
use Illuminate\View\View;

class Guest extends Component
{
    public function render(): View
    {
        return view('layouts.guest');
    }
}