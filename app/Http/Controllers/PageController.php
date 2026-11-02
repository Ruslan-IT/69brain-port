<?php

namespace App\Http\Controllers;

use App\Support\SiteContent;
use App\Support\SiteSettings;
use App\Support\SiteUrl;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view(SiteSettings::homepage() === 'new' ? 'pages.home-new' : 'pages.home');
    }

    public function strength(): View
    {
        return $this->serviceByKey('strength');
    }

    public function statistics(): View
    {
        return $this->serviceByKey('statistics');
    }

    public function technology(): View
    {
        return $this->serviceByKey('technology');
    }

    public function show(string $slug): View
    {
        abort_if(in_array($slug, SiteUrl::reserved(), true), 404);

        $service = SiteContent::findPublishedBySlug($slug);
        abort_if($service === null, 404);

        return view('pages.service', ['service' => $service]);
    }

    private function serviceByKey(string $key): View
    {
        $service = SiteContent::findByKey($key);
        abort_if($service === null || ! $service->is_published, 404);

        return view('pages.service', ['service' => $service]);
    }
}
