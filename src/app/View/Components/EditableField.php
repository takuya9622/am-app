<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EditableField extends Component
{
    public $name;
    public $isApprovalPending;
    public $type;
    public $value;
    public $class;
    public $isApproved;

    public function __construct(
        $name,
        $isApprovalPending = null,
        $type = 'time',
        $value = null,
        $class = '',
        $isApproved = null,
        )
    {
        $this->isApprovalPending = $isApprovalPending;
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->class = $class;
        $this->isApproved = ($isApproved ?? true);
    }

    public function getOldKey()
    {
        return preg_replace('/\[(\d+)\]/', '.$1', $this->name);
    }

    public function getValue()
    {
        $oldKey = $this->getOldKey();
        $oldValue = old($oldKey);
        return $oldValue !== null ? $oldValue : $this->value;
    }

    public function render()
    {
        return view('components.editable-field');
    }
}
