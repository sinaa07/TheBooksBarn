import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { Address } from '@/types';

interface Props {
    addresses: Address[];
}

export default function AddressIndex({ addresses }: Props) {
    const { delete: destroy } = useForm();

    const handleDelete = (id: number) => {
        if (confirm('Are you sure you want to delete this address?')) {
            destroy(route('addresses.destroy', id));
        }
    };

    return (
        <div>
            <Head title="My Addresses" />
            <main className="max-w-4xl mx-auto px-6 py-12">
                <section className="bg-[#fdf9f4] rounded-xl p-8 shadow-sm backdrop-blur-sm space-y-8">
                    <div className="flex justify-between items-center">
                        <h2 className="text-3xl font-bold text-[#4B3B2A] font-serif">My Saved Addresses</h2>
                        <Link
                            href={route('addresses.create')}
                            className="bg-[#704832] text-white px-6 py-2 rounded hover:bg-[#5C4033] transition"
                        >
                            + Add New
                        </Link>
                    </div>

                    {addresses.length === 0 ? (
                        <p className="text-[#5C4033] italic">No addresses saved yet.</p>
                    ) : (
                        <ul className="space-y-4">
                            {(addresses ?? []).length === 0 ? 
                            ( <p>No addresses saved yet.</p>) 
                             :
                             addresses.map((addr) => (
                                <li
                                    key={addr.id}
                                    className="border rounded-lg p-4 bg-[#f9f5f0] shadow-sm space-y-2"
                                >
                                    <div className="flex justify-between items-center">
                                        <h4 className="text-[#4B3B2A] font-semibold">
                                            {addr.name} — {addr.address_type}
                                        </h4>
                                        {addr.is_default && (
                                            <span className="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                                                Default
                                            </span>
                                        )}
                                    </div>
                                    <div className="text-sm text-[#5C4033] italic space-y-1">
                                        <div>{addr.address_line_1}{addr.address_line_2 ? `, ${addr.address_line_2}` : ''}</div>
                                        <div>{addr.city}, {addr.state} - {addr.postal_code}</div>
                                        <div>{addr.country}</div>
                                        <div>Phone: {addr.phone}</div>
                                    </div>
                                    <div className="flex gap-4 mt-2">
                                        <Link
                                            href={route('addresses.edit', addr.id)}
                                            className="text-sm bg-[#4B3B2A] text-white px-4 py-1 rounded hover:bg-[#3B1F1B] transition"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            onClick={() => handleDelete(addr.id)}
                                            className="text-sm bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700 transition"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            </main>
        </div>
    );
}