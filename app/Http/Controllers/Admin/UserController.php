<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quartier;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::withCount('equipements')->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User,
            'quartiers' => $this->quartiers(),
        ]);
    }

    public function store(Request $r)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,agent,habitant',
            'quartier_id' => 'nullable|exists:quartiers,id',
        ]);

        User::create($d);

        return redirect()->route('admin.users.index');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'quartiers' => $this->quartiers(),
        ]);
    }

    public function update(Request $r, User $user)
    {
        $d = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:admin,agent,habitant',
            'quartier_id' => 'nullable|exists:quartiers,id',
        ]);

        if ($r->user()->is($user) && $d['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer votre rôle admin.']);
        }

        if (empty($d['password'])) {
            unset($d['password']);
        }

        $user->update($d);

        return redirect()->route('admin.users.show', $user);
    }

    public function destroy(Request $r, User $user)
    {
        abort_if($r->user()->is($user), 403);
        $user->delete();

        return redirect()->route('admin.users.index');
    }

    private function quartiers()
    {
        return Quartier::orderBy('nom')->get();
    }
}
