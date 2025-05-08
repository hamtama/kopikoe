<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class LevelInput extends Field
{
    protected string $view = 'components.level-input';
    protected array $numberRange = [1, 15];
    protected array $colors = ['#ffccbc', '#ffa185', '#ff845f'];
    protected array $labels = ['Low', 'Medium', 'High'];

    public function numberRange(array $range): static
    {
        $this->numberRange = $range;
        return $this;
    }

    public function colors(array $colors): static
    {
        $this->colors = $colors;
        return $this;
    }

    public function labels(array $labels): static
    {
        $this->labels = $labels;
        return $this;
    }

    // Method untuk render custom view
    protected function setUp(): void
    {
        parent::setUp();

        $this->view('components.level-input', [
            'numberRange' => is_callable($this->numberRange) ? ($this->numberRange)() : $this->numberRange,
            'colors' => is_callable($this->colors) ? ($this->colors)() : $this->colors,
            'labels' => is_callable($this->labels) ? ($this->labels)() : $this->labels,
        ]);
    }
}
