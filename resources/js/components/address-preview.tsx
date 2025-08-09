import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import { Address } from '@/types';

interface Props {
    addresses: Address[];
    totalAddresses?: number;
}

export default function AddressPreview({ addresses, totalAddresses }: Props) {
    const { delete: destroy } = useForm();

    const handleDelete = (id: number) => {
        if (confirm('Are you sure you want to delete this address?')) {
            destroy(route('addresses.destroy', id));
        }
    };

    return (
        <div>
            <div className="flex justify-between items-center mb-6 mx-2 ">
                <h2 className="text-2xl font-semibold text-[#4B3B2A] font-serif">My Saved Addresses</h2>
                <Link
                    href={route('addresses.index')}
                    className="text-sm bg-white border border-[#704832] text-[#704832] px-4 py-2 rounded hover:bg-[#704c38f3] hover:text-white transition"
                >
                    View All Addresses {totalAddresses && `(${totalAddresses})`}
                </Link>
            </div>

            {addresses.length === 0 ? (
                <div className="text-center py-8 bg-[#fdf9f4] rounded-xl shadow-sm">
                    <p className="text-[#5C4033] italic mb-4">No addresses saved yet.</p>
                    <Link
                        href={route('addresses.create')}
                        className="bg-[#704832] text-white px-6 py-2 rounded hover:bg-[#5C4033] transition"
                    >
                        + Add Your First Address
                    </Link>
                </div>
            ) : (
                <ul className="space-y-4 mx-2 p-2">
                    {addresses.map((addr) => (
                        <li
                        key={addr.id}
                        className="border rounded-lg p-4 bg-[#f9f5f0] shadow-sm space-y-1.5" // Reduced from p-4 and space-y-2
                    >
                        <div className="flex justify-between items-center">
                            <h4 className="text-[#4B3B2A] font-semibold text-base"> {/* Added text-base for consistent sizing */}
                                {addr.name} — {addr.address_type}
                            </h4>
                            {addr.is_default && (
                                <span className="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">
                                    Default
                                </span>
                            )}
                        </div>
                        <div className="text-sm text-[#5C4033] italic space-y-0.5"> {/* Reduced from space-y-1 */}
                            <div>{addr.address_line_1}{addr.address_line_2 ? `, ${addr.address_line_2}` : ''}</div>
                            <div>{addr.city}, {addr.state} - {addr.postal_code}</div>
                            <div>{addr.country}</div>
                            <div>Phone: {addr.phone}</div>
                        </div>
                        {/*<div className="flex gap-4 mt-1.5"> }
                            <button
                                onClick={() => handleDelete(addr.id)}
                                className="text-sm bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700 transition"
                            >
                                Delete
                            </button>
                        </div>*/}
                    </li>
                    ))}
                </ul>
            )}
        </div>
    );
}