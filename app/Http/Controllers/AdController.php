<?php

namespace App\Http\Controllers;

use App\Models\AdSlot;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function show(string $slotIdentifier)
    {
        $adSlot = AdSlot::where('identifier', $slotIdentifier)
            ->where('is_active', true)
            ->first();
        
        if (!$adSlot) {
            return response()->json(['ad' => null]);
        }
        
        $ad = $adSlot->activeAdvertisements()->first();
        
        if (!$ad) {
            return response()->json(['ad' => null]);
        }
        
        $ad->incrementImpressions();
        
        return response()->json([
            'ad' => [
                'id' => $ad->id,
                'type' => $ad->type,
                'content' => $ad->content,
                'link_url' => $ad->link_url,
                'open_new_tab' => $ad->open_new_tab,
            ]
        ]);
    }

    public function click(Advertisement $advertisement)
    {
        $advertisement->incrementClicks();
        return response()->json(['success' => true]);
    }
}
