import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Address } from '@/types';

interface Props {
    address: Address;
}

export default function EditAddress({ address }: Props) {
    const { data, setData, patch, processing, errors } = useForm({
        address_line1: address.address_line1 || '',
        address_line2: address.address_line2 || '',
        city: address.city || '',
        state: address.state || '',
        zip: address.postal_code || '',
        country: address.country || ''
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        patch(route('user.address.update', address.id));
    };

    return (
        <AppSidebarLayout>
            <Head title="Edit Address" />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa]">
                <main className="max-w-4xl mx-auto px-6 py-12">
                    <section className="bg-white rounded-xl shadow p-8 space-y-10">
                        <h1 className="text-3xl font-bold text-[#4B3B2A] font-serif mb-4">Edit Address</h1>
                        <form onSubmit={handleSubmit} className="space-y-8">
                            <div>
                                <h2 className="text-lg font-semibold text-[#4B3B2A] mb-2">1️⃣ Address Details</h2>
                                <div className="space-y-3">
                                    <input
                                        type="text"
                                        placeholder="Address Line 1"
                                        value={data.address_line1}
                                        onChange={(e) => setData('address_line1', e.target.value)}
                                        className="w-full border text-[#4B3B2A] border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                    <input
                                        type="text"
                                        placeholder="Address Line 2 (Optional)"
                                        value={data.address_line2}
                                        onChange={(e) => setData('address_line2', e.target.value)}
                                        className="w-full border text-[#4B3B2A] border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                </div>
                                {errors.address_line1 && <p className="text-sm text-red-600">{errors.address_line1}</p>}
                                {errors.address_line2 && <p className="text-sm text-red-600">{errors.address_line2}</p>}
                            </div>

                            <div>
                                <h2 className="text-lg font-semibold text-[#4B3B2A] mb-2">2️⃣ City & State</h2>
                                <div className="grid grid-cols-2 gap-4">
                                    <input
                                        type="text"
                                        placeholder="City"
                                        value={data.city}
                                        onChange={(e) => setData('city', e.target.value)}
                                        className="w-full text-[#4B3B2A] border border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                    <input
                                        type="text"
                                        placeholder="State"
                                        value={data.state}
                                        onChange={(e) => setData('state', e.target.value)}
                                        className="w-full border text-[#4B3B2A] border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                </div>
                                {errors.city && <p className="text-sm text-red-600">{errors.city}</p>}
                                {errors.state && <p className="text-sm text-red-600">{errors.state}</p>}
                            </div>

                            <div>
                                <h2 className="text-lg font-semibold text-[#4B3B2A] mb-2">3️⃣ ZIP / Country / Mobile</h2>
                                <div className="grid grid-cols-3 gap-4">
                                    <input
                                        type="text"
                                        placeholder="ZIP Code"
                                        value={data.zip}
                                        onChange={(e) => setData('zip', e.target.value)}
                                        className="w-full border text-[#4B3B2A] border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                    <input
                                        type="text"
                                        placeholder="Country"
                                        value={data.country}
                                        onChange={(e) => setData('country', e.target.value)}
                                        className="w-full border text-[#4B3B2A] border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                </div>
                                {errors.zip && <p className="text-sm text-red-600">{errors.zip}</p>}
                                {errors.country && <p className="text-sm text-red-600">{errors.country}</p>}
                            </div>

                            <div className="flex justify-end gap-4">
                                <a
                                    href={route('user.address.index')}
                                    className="bg-gray-200 text-[#4B3B2A] px-4 py-2 rounded hover:bg-gray-300 transition"
                                >
                                    Cancel
                                </a>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="bg-[#704832] text-white px-6 py-2 rounded hover:bg-[#5C4033] transition"
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