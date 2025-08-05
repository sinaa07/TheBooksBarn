import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';
import { Address } from '@/types';

interface Props {
    address: Address;
}

export default function ShowAddress({ address }: Props) {
    return (
        <AppSidebarLayout>
            <Head title={`Address #${address.id}`} />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa]">
                <main className="max-w-4xl mx-auto px-6 py-12">
                    <section className="bg-white rounded-xl shadow p-8 space-y-8">
                        {/* Header */}
                        <div className="flex justify-between items-center">
                            <h1 className="text-3xl font-bold text-[#4B3B2A] font-serif">
                                Address Details
                            </h1>
                            <div className="flex gap-3">
                                <Link
                                    href={route('addresses.edit', address.id)}
                                    className="bg-[#704832] text-white px-4 py-2 rounded hover:bg-[#5C4033] transition"
                                >
                                    Edit
                                </Link>
                                <Link
                                    href={route('addresses.index')}
                                    className="bg-gray-200 text-[#4B3B2A] px-4 py-2 rounded hover:bg-gray-300 transition"
                                >
                                    Back
                                </Link>
                            </div>
                        </div>

                        {/* Address Info */}
                        <dl className="grid grid-cols-2 gap-x-6 gap-y-4">
                            <dt className="font-semibold text-[#4B3B2A]">Full Name</dt>
                            <dd className="text-[#5C4033]">{address.name}</dd>

                            <dt className="font-semibold text-[#4B3B2A]">Phone</dt>
                            <dd className="text-[#5C4033]">{address.phone}</dd>

                            <dt className="font-semibold text-[#4B3B2A]">Address Line 1</dt>
                            <dd className="text-[#5C4033]">{address.address_line_1}</dd>

                            {address.address_line_2 && (
                                <>
                                    <dt className="font-semibold text-[#4B3B2A]">Address Line 2</dt>
                                    <dd className="text-[#5C4033]">{address.address_line_2}</dd>
                                </>
                            )}

                            <dt className="font-semibold text-[#4B3B2A]">City</dt>
                            <dd className="text-[#5C4033]">{address.city}</dd>

                            <dt className="font-semibold text-[#4B3B2A]">State</dt>
                            <dd className="text-[#5C4033]">{address.state}</dd>

                            <dt className="font-semibold text-[#4B3B2A]">Postal Code</dt>
                            <dd className="text-[#5C4033]">{address.postal_code}</dd>

                            <dt className="font-semibold text-[#4B3B2A]">Country</dt>
                            <dd className="text-[#5C4033]">{address.country}</dd>

                            <dt className="font-semibold text-[#4B3B2A]">Address Type</dt>
                            <dd className="capitalize text-[#5C4033]">{address.address_type}</dd>

                            <dt className="font-semibold text-[#4B3B2A]">Default Address</dt>
                            <dd className="text-[#5C4033]">{address.is_default ? 'Yes' : 'No'}</dd>
                        </dl>
                    </section>
                </main>
            </div>
        </AppSidebarLayout>
    );
}