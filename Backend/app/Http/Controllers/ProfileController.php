<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAccountRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $villagers = Cache::remember('villager_images', 86400, function () {
            $path = public_path('images/villagers');
            if (!is_dir($path)) {
                return [];
            }
            $files = scandir($path);

            return array_values(array_filter($files, function ($file) {
                return str_ends_with(strtolower($file), '.png') || str_ends_with(strtolower($file), '.jpg');
            }));
        });

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'villagers' => $villagers,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();
            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            return Redirect::route('profile.edit');
        } catch (\Exception $e) {
            Log::error('Profile update failed', ['error' => $e->getMessage(), 'user_id' => $request->user()->id]);
            return back()->withErrors(['general' => 'Error al actualizar el perfil. Inténtelo de nuevo.']);
        }
    }

    public function destroy(DeleteAccountRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
