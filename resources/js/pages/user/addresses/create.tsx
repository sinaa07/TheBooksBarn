import React from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';

export default function CreateAddress() {
    const { data, setData, post, processing, errors } = useForm({
        address_line1: '',
        address_line2: '',
        city: '',
        state: '',
        postal_code: '',
        country: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('user.address.store'));
    };

    return (
        <AppSidebarLayout>
            <Head title="Add Address" />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa]">
                <main className="max-w-4xl mx-auto px-6 py-12">
                    <section className="bg-white rounded-xl shadow p-8 space-y-10">
                        <h1 className="text-3xl font-bold text-[#4B3B2A] font-serif mb-4">Add New Address</h1>
                        <form onSubmit={handleSubmit} className="space-y-8">

                            <div>
                                <h2 className="text-lg font-semibold text-[#4B3B2A] mb-2"> Address Details</h2>
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
                                <h2 className="text-lg font-semibold text-[#4B3B2A] mb-2"> City & State</h2>
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
                                        className="w-full text-[#4B3B2A] border border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                </div>
                                {errors.city && <p className="text-sm text-red-600">{errors.city}</p>}
                                {errors.state && <p className="text-sm text-red-600">{errors.state}</p>}
                            </div>

                            <div>
                                <h2 className="text-lg font-semibold text-[#4B3B2A] mb-2"> Postal Code / Country / Phone </h2>
                                <div className="grid grid-cols-3 gap-4">
                                    <input
                                        type="text"
                                        placeholder="Postal Code"
                                        value={data.postal_code}
                                        onChange={(e) => setData('postal_code', e.target.value)}
                                        className="w-full text-[#4B3B2A] border border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                    <input
                                        type="text"
                                        placeholder="Country"
                                        value={data.country}
                                        onChange={(e) => setData('country', e.target.value)}
                                        className="w-full text-[#4B3B2A] border border-[#d6c2aa] rounded px-3 py-2 focus:outline-none focus:ring"
                                    />
                                    
                                </div>
                                {errors.postal_code && <p className="text-sm text-red-600">{errors.postal_code}</p>}
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
                                    Save Address
                                </button>
                            </div>
                        </form>
                    </section>
                </main>
            </div>
        </AppSidebarLayout>
    );
}