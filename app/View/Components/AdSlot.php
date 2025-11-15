<?php

namespace App\View\Components;

use App\Models\AdSlot as AdSlotModel;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdSlot extends Component
{
    public $slot;
    public $advertisement;

    public function __construct(string $identifier)
    {
        $this->slot = AdSlotModel::where('identifier', $identifier)
            ->where('is_active', true)
            ->first();
        
        if ($this->slot) {
            $this->advertisement = $this->slot->activeAdvertisements()->first();
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.ad-slot');
    }
}
