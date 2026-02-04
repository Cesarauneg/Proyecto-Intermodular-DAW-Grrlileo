<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|confirmed|min:8', // 'confirmed' valida password_confirmation
            // 'captcha_token' => 'required', // Temporarily disabled
        ]);

                // $response = Http::withoutVerifying() // Temporarily disabled
                //     ->asForm()
                //     ->post('https://www.google.com/recaptcha/api/siteverify', [
                //         'secret'   => env('RECAPTCHA_SECRET_KEY'),
                //         'response' => $request->captcha_token,
                //         'remoteip' => $request->ip(),
                //     ]);
        
                // if (!$response->json('success')) { // Temporarily disabled
                //     throw ValidationException::withMessages([
                //         'captcha_token' => 'La verificación del Captcha ha fallado. Inténtalo de nuevo.',
                //     ]);
                // }
                $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/');
    }
}