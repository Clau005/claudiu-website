<?php

namespace ElevateCommerce\VisualEditor\View\Components;

use Illuminate\View\Component;

class TagsInput extends Component
{
    public $model;
    public $name;
    public $label;
    public $placeholder;
    public $help;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $model = null,
        string $name = 'tags',
        string $label = 'Tags',
        string $placeholder = 'Add tags...',
        string $help = 'Press Enter or comma to add a tag'
    ) {
        $this->model = $model;
        $this->name = $name;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->help = $help;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('visual-editor::components.tags-input');
    }
}
