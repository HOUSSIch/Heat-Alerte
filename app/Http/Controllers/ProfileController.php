<?php

namespace App\Http\Controllers;

use App\Models\Quartier;
use App\Notifications\AccountSecurityNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('user.profile.show', ['user' => $request->user()]);
    }

    public function edit(Request $request): View
    {
        return view('user.profile.edit', [
            'user' => $request->user(),
            'quartiers' => Quartier::where('actif', true)->orderBy('ville')->orderBy('nom')->get(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $passwordChanged = ! empty($request->input('password'));
        $validated = $request->validate([
            'name' => ['bail', 'required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'quartier_id' => ['nullable', 'exists:quartiers,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'quartier_id' => $validated['quartier_id'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user->save();

        if ($user->wasChanged('email') && ! $user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (TransportExceptionInterface) {
                return redirect()->route('verification.notice')
                    ->with('error', 'Votre adresse e-mail a été modifiée, mais le lien de vérification n’a pas pu être envoyé. Vérifiez la configuration SMTP, puis renvoyez le lien.');
            }
        }

        if ($passwordChanged) {
            $user->notify(AccountSecurityNotification::passwordChanged());
        }

        return redirect()->route('profile.show')->with('success', 'Profil modifié avec succès.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);
        $user = $request->user();

        Auth::logout();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register')->with('success', 'Compte supprimé avec succès.');
    }
}
