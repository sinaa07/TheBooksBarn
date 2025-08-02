<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page (Breeze style)
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'user' => $request->user(),
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Show the user's profile edit page (Custom style)
     */
    public function editProfile()
    {
        return Inertia::render('User/EditProfile', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile settings (Breeze style with ProfileUpdateRequest)
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile (Custom style with manual validation)
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Handle email change
        if ($user->email !== $validated['email']) {
            $user->email_verified_at = null;
        }

        $user->update($validated);

        return redirect()->route('user.dashboard')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show user profile with stats and addresses
     */
    public function show()
    {
        $user = Auth::user();
        
        $user->load([
            'addresses' => function($query) {
                $query->orderBy('is_default', 'desc')
                      ->orderBy('created_at', 'desc');
            }
        ]);

        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount'),
            'pending_orders' => $user->orders()
                ->whereIn('order_status', ['pending', 'confirmed', 'processing'])
                ->count(),
        ];

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Check if user has orders before deletion
        if ($user->orders()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete account with existing orders. Please contact support.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}