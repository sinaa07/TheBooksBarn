<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class AddressController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $addresses = Address::where('user_id',auth()->user()->user_id)->get();
        return Inertia::render('user/addresses/index', ['addresses' => $addresses]);
    }

    public function create()
    {
        return Inertia::render('user/addresses/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        auth()->user()->addresses()->create([
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Address saved.');
    }

    public function edit(Address $address)
    {
        $this->authorize('update', $address);
        return Inertia::render('user/addresses/edit', ['address' => $address]);
    }

    public function update(Request $request, Address $address)
    {
        $this->authorize('update', $address);

        $validated = $request->validate([
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:100',
        ]);

        $address->update($validated);

        return redirect()->route('user.dashboard')->with('success', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        $this->authorize('update',$address);
        $address->delete();

        return redirect()->route('user.dashboard')->with('success', 'Address deleted.');
    }
}