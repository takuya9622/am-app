<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectableField extends Component
{
    public $isApprovalPending;
    public $id;
    public $name;
    public $type;
    public $startYear;
    public $endYear;
    public $selected;
    public $isLeapYear;
    public $value;

    public function __construct(
        $isApprovalPending,
        $id,
        $name,
        $type,
        $startYear = null,
        $endYear = null,
        $selected = null,
        $value = null,
    ) {
        $this->isApprovalPending = $isApprovalPending;
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->startYear = $startYear ?? 2000;
        $this->endYear = $endYear ?? now()->year;
        $this->selected = $selected;
        $this->isLeapYear = $this->checkLeapYear($this->endYear);
        $this->value = $value;
    }

    private function checkLeapYear($year)
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
    }

    public function render()
    {
        return view('components.selectable-field');
    }
}
