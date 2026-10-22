<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Ad;

class AdController extends Controller
{
    public function click(Ad $ad)
    {
        abort_unless($ad->url, 404);
        $ad->increment('clicks');

        return redirect()->away($ad->url);
    }
}
