<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Quartier;

class ProfileController extends Controller
{
    // READ : afficher le profil
    public function show()
    {
        $user = request()->user();

        return view('user.profile.show', compact('user'));
    }

    // Afficher le formulaire de modification
    public function edit()
    {
        $user = request()->user();

        $quartiers = Quartier::where('actif', true)->orderBy('ville')->orderBy('nom')->get();
        return view('user.profile.edit', compact('user','quartiers'));
    }

    // UPDATE : modifier le profil
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => 'nullable|min:6|confirmed',
            'quartier_id' => 'nullable|exists:quartiers,id',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->quartier_id = $validated['quartier_id'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profil modifié avec succès.');
    }

    // DELETE : supprimer le compte
    public function destroy(Request $request)
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/register')
            ->with('success', 'Votre compte a été supprimé.');
    }
}
