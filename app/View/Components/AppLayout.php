<?php

namespace App\View\Components;

use App\Models\Project;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function __construct(
        public ?Project $project = null,
    ) {}

    public function render(): View
    {
        return view('layouts.app');
    }
}
