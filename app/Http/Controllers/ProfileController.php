<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $this->authorize('edit profil');

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->authorize('edit profil');
        $validated = $request->validated();
        $user = $request->user();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        if ($user->anggota) {
            $user->anggota()->update([
                'nama' => $validated['name'],
                'nip' => $validated['nip'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            ]);
        }

        return Redirect::back()
            ->with('status', 'profile-updated')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Delete the user's account.
     */
    // public function destroy(Request $request): RedirectResponse
    // {
    //     $this->authorize('edit profil');
    //     $request->validateWithBag('userDeletion', [
    //         'password' => ['required', 'current_password'],
    //     ]);

    //     $user = $request->user();

    //     Auth::logout();

    //     $user->delete();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return Redirect::to('/');
    // }
}
