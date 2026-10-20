<?php

namespace App\Http\Controllers;

use App\Notifications\AccountSecurityNotification;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function setup(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->two_factor_secret) {
            return redirect()->route('profile.edit')->with('error', 'Activez d’abord l’authentification à deux facteurs.');
        }

        return view('profile.two-factor-setup', [
            'secret' => $user->two_factor_secret,
            'qrCode' => (new SvgWriter)->write(
                QrCode::create($this->google2fa()->getQRCodeUrl('HeatAlert', $user->email, $user->two_factor_secret))
            )->getString(),
        ]);
    }

    public function enable(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorEnable', [
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->forceFill([
            'two_factor_secret' => $this->google2fa()->generateSecretKey(),
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()->route('two-factor.setup');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorConfirm', [
            'code' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (! $user->two_factor_secret || ! $this->validCode($user->two_factor_secret, $request->string('code')->toString())) {
            return back()->withErrors(['code' => 'Le code de vérification est invalide.'], 'twoFactorConfirm');
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $this->recoveryCodes(),
        ])->save();

        $user->notify(AccountSecurityNotification::twoFactorEnabled());

        return redirect()->route('profile.edit')->with('success', 'L’authentification à deux facteurs est activée. Conservez vos codes de récupération.');
    }

    public function regenerate(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorRegenerate', [
            'current_password' => ['required', 'current_password'],
        ]);

        $request->user()->forceFill([
            'two_factor_recovery_codes' => $this->recoveryCodes(),
        ])->save();

        return back()->with('success', 'Les codes de récupération ont été régénérés. Les anciens codes ne sont plus valables.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validateWithBag('twoFactorDisable', [
            'current_password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $user->notify(AccountSecurityNotification::twoFactorDisabled());

        return back()->with('success', 'L’authentification à deux facteurs est désactivée.');
    }

    private function google2fa(): Google2FA
    {
        return app(Google2FA::class);
    }

    private function validCode(string $secret, string $code): bool
    {
        return $this->google2fa()->verifyKey($secret, $code, 1);
    }

    /** @return array<int, string> */
    private function recoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn () => Str::upper(Str::random(4)).'-'.Str::upper(Str::random(4)))
            ->all();
    }
}
