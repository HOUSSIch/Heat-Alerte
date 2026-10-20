<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        return $this->pendingUser($request)
            ? view('auth.two-factor-challenge')
            : redirect()->route('login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['nullable', 'digits:6', 'required_without:recovery_code'],
            'recovery_code' => ['nullable', 'string', 'required_without:code'],
        ]);

        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $valid = $request->filled('code')
            ? app(Google2FA::class)->verifyKey($user->two_factor_secret, $request->string('code')->toString(), 1)
            : $this->consumeRecoveryCode($user, $request->string('recovery_code')->toString());

        if (! $valid) {
            return back()->withErrors(['code' => 'Le code de vérification est invalide ou a déjà été utilisé.'])->withInput();
        }

        $remember = (bool) $request->session()->pull('two_factor_login.remember', false);
        $request->session()->forget('two_factor_login.id');
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return $user->role === 'admin'
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('dashboard'));
    }

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('two_factor_login.id');

        return $id ? User::find($id) : null;
    }

    private function consumeRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];
        $index = array_search($code, $codes, true);

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        return true;
    }
}
