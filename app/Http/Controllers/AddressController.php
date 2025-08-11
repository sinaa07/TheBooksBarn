<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Response;

class AddressController extends Controller
{
    use AuthorizesRequests;

    public function index(): Response
    {
        $addresses = auth()->user()->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Addresses/Index', [
            'addresses' => $addresses ?? []
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Addresses/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'address_type' => ['required', 'in:billing,shipping,both'],
            'is_default' => ['boolean'],
        ]);

        $user = auth()->user();
        if ($validated['is_default'] ?? false) {
            $user->addresses()->update(['is_default' => false]);
        }

        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = $user->addresses()->create($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Address created successfully.');
    }

    public function show(Address $address): Response
    {
        $this->authorize('view', $address);

        return Inertia::render('Addresses/Show', [
            'address' => $address
        ]);
    }

    public function edit(Address $address): Response
    {
        $this->authorize('update', $address);

        return Inertia::render('Addresses/Edit', [
            'address' => $address
        ]);
    }

    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'address_type' => ['required', 'in:billing,shipping,both'],
            'is_default' => ['boolean'],
        ]);

        if (($validated['is_default'] ?? false) && !$address->is_default) {
            auth()->user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        $this->authorize('delete', $address);

        $user = auth()->user();

        // Check if this is the only address
        if ($user->addresses()->count() === 1) {
            return redirect()->back()
                ->with('error', 'Cannot delete your only address.');
        }

        // If deleting default address, set another one as default
        if ($address->is_default) {
            $newDefault = $user->addresses()
                ->where('id', '!=', $address->id)
                ->first();
            
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address): RedirectResponse
    {
        $this->authorize('update', $address);

        $user = auth()->user();

        // Remove default from all addresses
        $user->addresses()->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return redirect()->back()
            ->with('success', 'Default address updated successfully.');
    }

    public function getByType(Request $request)
    {
        $type = $request->get('type', 'both');
        
        $addresses = auth()->user()->addresses()
            ->whereIn('address_type', [$type, 'both'])
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'addresses' => $addresses
        ]);
    }

    public function getDefault()
    {
        $defaultAddress = auth()->user()->addresses()
            ->where('is_default', true)
            ->first();

        if (!$defaultAddress) {
            $defaultAddress = auth()->user()->addresses()->first();
        }

        return response()->json([
            'address' => $defaultAddress
        ]);
    }
}