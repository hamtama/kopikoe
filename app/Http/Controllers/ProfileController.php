<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Decode the JSON data from the social_media column
        $socialMediaAccounts = json_decode($user->social_media, true);

        return view('profile.edit', [
            'user' => $user,
            'socialMediaAccounts' => $socialMediaAccounts,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();




        $socialMediaAccounts = [
            'instagram' => $request->input('instagram_account'),
            'facebook' => $request->input('facebook_account'),
            'twitter' => $request->input('twitter_account'),
        ];

        foreach ($socialMediaAccounts as $platform => $account) {
            // Jika checkbox tidak dipilih, set akun menjadi null
            if (!in_array($platform, $request->input('social_media', []))) {
                $socialMediaAccounts[$platform] = null;
            }
        }

        //Masukkan data sosial media yang telah diproses ke dalam validasi data
        $validatedData['social_media'] = json_encode($socialMediaAccounts);


        $request->user()->fill($validatedData);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
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
