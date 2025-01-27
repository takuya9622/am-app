<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ListHeader extends Component
{
    public $tab;
    public $staff;
    public $previousMonth;
    public $nextMonth;
    public $currentMonth;
    public $todayFormatted;
    public $yesterday;
    public $tomorrow;

    public function __construct($previousMonth, $nextMonth, $staff = null, $currentMonth = null, $todayFormatted = null, $yesterday, $tomorrow, $tab = null)
    {
        $this->previousMonth = $previousMonth;
        $this->nextMonth = $nextMonth;
        $this->staff = $staff;
        $this->currentMonth = $currentMonth;
        $this->todayFormatted = $todayFormatted;
        $this->yesterday = $yesterday;
        $this->tomorrow = $tomorrow;
        $this->tab = $tab;
    }

    public function render(): View|Closure|string
    {
        return view('components.list-header');
    }
}
