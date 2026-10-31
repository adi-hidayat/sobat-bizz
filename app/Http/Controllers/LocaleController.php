<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    /**
     * Switch the application locale for the current user.
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('localization.supported', [])), 404);

        $request->session()->put('locale', $locale);

        if ($user = $request->user()) {
            $user->forceFill(['locale' => $locale])->save();
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return redirect()->back(fallback: route('filament.admin.pages.dashboard'));
    }
}
