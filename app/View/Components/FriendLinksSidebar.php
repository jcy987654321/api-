<?php

namespace App\View\Components;

use App\Models\FriendLink;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FriendLinksSidebar extends Component
{
    public $friendLinks;

    public function __construct(int $limit = 10)
    {
        $this->friendLinks = FriendLink::approved()
            ->limit($limit)
            ->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.friend-links-sidebar');
    }
}
