<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log; 

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $path = public_path('images/villagers');
        
        $villagers = [];
        if (is_dir($path)) {
            $files = scandir($path);
            $villagers = array_values(array_filter($files, function($file) {
                return str_ends_with(strtolower($file), '.png') || str_ends_with(strtolower($file), '.jpg');
            }));
        }

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'villagers' => $villagers, 
        ]);
    }

    /**
     * Update the user's profile information.
     */
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
            Log::error($e->getMessage());
            dd($e->getMessage()); 
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}