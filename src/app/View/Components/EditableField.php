<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EditableField extends Component
{
    public $isApprovalPending;
    public $type;
    public $name;
    public $value;
    public $class;

    public function __construct($isApprovalPending, $name, $type = 'time', $value = null, $class = '')
    {
        $this->isApprovalPending = $isApprovalPending;
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->class = $class;
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
