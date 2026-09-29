<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $google_user = Socialite::driver('google')->user();
            $user = User::where('google_id', $google_user->getId())->first();
            if (! $user) {

                $new_user = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                    'profile_path' => $google_user->getId().'.jpg',
                ]);
                $filename = $google_user->getId().'.jpg';
                // Storage::disk('profile_pic')->put($filename, file_get_contents($google_user->getAvatar()));

                Auth::login($new_user);

                return redirect(route('home'));
            } else {

                Auth::login($user);

                return redirect(route('home'));
            }
        } catch (\Throwable $th) {
            dd('Something went wrong!'.$th->getMessage());
        }
    }
}
