import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Address } from '@/types';

interface Props {
    address: Address;
}

export default function EditAddress({ address }: Props) {
    const { data, setData, patch, processing, errors } = useForm({
        name: address.name || '',
        phone: address.phone || '',
        address_line_1: address.address_line_1 || '',
        address_line_2: address.address_line_2 || '',
        city: address.city || '',
        state: address.state || '',
        postal_code: address.postal_code || '',
        country: address.country || '',
        address_type: address.address_type || 'both',
        is_default: address.is_default || false,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(route('addresses.update', address.id)); // matches backend resource route
    };

    return (
        <AppSidebarLayout>
            <Head title="Edit Address" />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa]">
                <main className="max-w-4xl mx-auto px-6 py-12">
                    <section className="bg-white rounded-xl shadow p-8 space-y-10">
                        <h1 className="text-3xl font-bold text-[#4B3B2A] font-serif mb-4">Edit Address</h1>
                        <form onSubmit={handleSubmit} className="space-y-8">
                            
                            {/* Contact Info */}
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <input
                                        type="text"
                                        placeholder="Full Name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                    />
                                    {errors.name && <p className="text-sm text-red-600">{errors.name}</p>}
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        placeholder="Phone Number"
                                        value={data.phone}
                                        onChange={(e) => setData('phone', e.target.value)}
                                        className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                    />
                                    {errors.phone && <p className="text-sm text-red-600">{errors.phone}</p>}
                                </div>
                            </div>

                            {/* Address Lines */}
                            <div className="space-y-3">
                                <input
                                    type="text"
                                    placeholder="Address Line 1"
                                    value={data.address_line_1}
                                    onChange={(e) => setData('address_line_1', e.target.value)}
                                    className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                />
                                {errors.address_line_1 && <p className="text-sm text-red-600">{errors.address_line_1}</p>}

                                <input
                                    type="text"
                                    placeholder="Address Line 2 (Optional)"
                                    value={data.address_line_2 || ''}
                                    onChange={(e) => setData('address_line_2', e.target.value)}
                                    className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                />
                                {errors.address_line_2 && <p className="text-sm text-red-600">{errors.address_line_2}</p>}
                            </div>

                            {/* City & State */}
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <input
                                        type="text"
                                        placeholder="City"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                    />
                                    {errors.city && <p className="text-sm text-red-600">{errors.city}</p>}
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        placeholder="State"
                                        value={data.state}
                                        onChange={(e) => setData('state', e.target.value)}
                                        className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                    />
                                    {errors.state && <p className="text-sm text-red-600">{errors.state}</p>}
                                </div>
                            </div>

                            {/* Postal & Country */}
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <input
                                        type="text"
                                        placeholder="Postal Code"
                                        value={data.postal_code}
                                        onChange={(e) => setData('postal_code', e.target.value)}
                                        className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                    />
                                    {errors.postal_code && <p className="text-sm text-red-600">{errors.postal_code}</p>}
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        placeholder="Country"
                                        value={data.country}
                                        onChange={(e) => setData('country', e.target.value)}
                                        className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                    />
                                    {errors.country && <p className="text-sm text-red-600">{errors.country}</p>}
                                </div>
                            </div>

                            {/* Address Type */}
                            <div>
                                <label className="block text-sm font-medium mb-1">Address Type</label>
                                <select
                                    value={data.address_type}
                                    onChange={(e) =>
                                        setData('address_type', e.target.value as 'billing' | 'shipping' | 'both')
                                    }
                                    className="w-full border border-[#d6c2aa] rounded px-3 py-2"
                                >
                                    <option value="billing">Billing</option>
                                    <option value="shipping">Shipping</option>
                                    <option value="both">Both</option>
                                </select>
                                {errors.address_type && <p className="text-sm text-red-600">{errors.address_type}</p>}
                            </div>

                            {/* Default Address */}
                            <div className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={data.is_default}
                                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => 
                                        setData('is_default', e.target.checked)
                                    }
                                />
                                <span className="text-sm">Set as default address</span>
                            </div>

                            {/* Actions */}
                            <div className="flex justify-end gap-4">
                                <a
                                    href={route('addresses.index')}
                                    className="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="bg-[#704832] text-white px-6 py-2 rounded hover:bg-[#5C4033]"
                                >
                                    Update Address
                                </button>
                            </div>
                        </form>
                    </section>
                </main>
            </div>
        </AppSidebarLayout>
    );
}