<?php

namespace App\Http\Controllers;

use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnbController extends Controller
{
    public function show(Request $request): View
    {
        if (! $this->allowed($request)) {
            return view('enb.login');
        }

        return view('enb.index', [
            'homepageVariant' => SiteSettings::homepage(),
            'visualizationMode' => SiteSettings::get(SiteSettings::VISUALIZATION, 'animated'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $password = (string) config('site.enb_password');

        if ($password !== '' && hash_equals($password, (string) $request->input('password'))) {
            $request->session()->put('enb_ok', true);
        } else {
            return back()->withErrors(['password' => 'Неверный пароль.']);
        }

        return redirect()->route('enb');
    }

    public function update(Request $request): RedirectResponse
    {
        if (! $this->allowed($request)) {
            abort(403);
        }

        $data = $request->validate([
            'homepage_variant' => ['required', 'in:original,new'],
            'visualization_mode' => ['required', 'in:animated,static'],
        ]);

        SiteSettings::put(SiteSettings::HOMEPAGE, $data['homepage_variant']);
        SiteSettings::put(SiteSettings::VISUALIZATION, $data['visualization_mode']);

        return redirect()->route('enb')->with('enb_notice', 'Настройки сохранены.');
    }

    private function allowed(Request $request): bool
    {
        $password = (string) config('site.enb_password');

        if ($password === '') {
            return app()->environment('local');
        }

        return $request->session()->get('enb_ok') === true;
    }
}
