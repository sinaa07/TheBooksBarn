<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show user profile with stats and addresses
     */
    public function show(): Response
    {
        $user = Auth::user();
        
        $addresses = $user->addresses()
        ->orderBy('is_default', 'desc')
        ->orderBy('created_at', 'desc')
        ->limit(2)
        ->get();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount'),
            'pending_orders' => $user->orders()
                ->whereIn('order_status', ['pending', 'confirmed', 'processing'])
                ->count(),
            'completed_orders' => $user->orders()
                ->where('order_status', 'delivered')
                ->count(),
        ];

        $totalAddresses = $user->addresses()->count();

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'stats' => $stats,
            'addresses' => $addresses,
            'totalAddresses' => $totalAddresses
        ]);
    }

    /**
     * Show the user's profile edit page
     */
    public function edit(): Response
    {
        return Inertia::render('Profile/Edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // Handle email change - require re-verification
        if ($user->email !== $validated['email']) {
            $user->email_verified_at = null;
        }

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
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

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}