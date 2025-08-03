import { Head, useForm } from '@inertiajs/react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';

import { Address, User } from '@/types';

interface Props {
    user: User;
    addresses: Address[];
}

export default function Dashboard({ user, addresses }: Props) {
    const { post } = useForm();

    const handleLogout = () => {
        post(route('logout'));
    };

    return (
        <AppSidebarLayout>
            <Head title="Dashboard" />

            <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa]">
                <header className="bg-white shadow py-6">
                    <div className="max-w-6xl mx-auto px-6 flex justify-between items-center">
                        <h1 className="flex items-center gap-2 text-[#5C4033] font-serif text-2xl font-bold">
                            My Account
                        </h1>
                        <div className="flex gap-4">
                            <a
                                href={route('profile.edit')}
                                className="bg-[#704832] text-white px-4 py-2 rounded hover:bg-[#5C4033] transition"
                            >
                                Profile Settings
                            </a>
                        </div>
                    </div>
                </header>

                <main className="max-w-4xl mx-auto px-6 py-12">
                    <section className="bg-white rounded-xl shadow p-8 space-y-10">
                        <div>
                            <h2 className="text-2xl font-semibold text-[#4B3B2A] font-serif mb-4">
                                👤 Profile Information
                            </h2>
                            <dl className="grid grid-cols-2 gap-x-8 gap-y-4 border-t border-gray-200 pt-6">
                                <dt className="text-[#4B3B2A] font-semibold">Name</dt>
                                <dd className="text-[#5C4033] italic">
                                    {`${user.first_name} ${user.last_name}`}
                                </dd>

                                <dt className="text-[#4B3B2A] font-semibold">Email</dt>
                                <dd className="text-[#5C4033] italic">{user.email}</dd>

                                <dt className="text-[#4B3B2A] font-semibold">Phone</dt>
                                <dd className="text-[#5C4033] italic">{user.phone ?? '—'}</dd>
                            </dl>
                        </div>

                        {/*<AddressIndex addresses={addresses} />*/}
                    </section>
                </main>

                <footer className="text-center text-sm text-[#7D5A4F] font-sans py-8 border-t">
                    &copy; {new Date().getFullYear()} BookCart. All rights reserved.
                </footer>
            </div>
        </AppSidebarLayout>
    );
}