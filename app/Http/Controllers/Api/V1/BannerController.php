<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BannerResource;
use App\Models\Admin\Banner;
use App\Models\Admin\BannerAds;
use App\Models\Admin\BannerText;
use App\Traits\HttpResponses;

class BannerController extends Controller
{
    use HttpResponses;

    public function index()
    {
        $data = Banner::agentPlayer();
        // return $this->success($data);
        return $this->success(BannerResource::collection($data));
    }

    public function bannerText()
    {
        $data = BannerText::latest()->first();

        return $this->success($data);
    }

    public function AdsBannerIndex()
    {
        $data = BannerAds::latest()->first();

        return $this->success($data);
    }
}
