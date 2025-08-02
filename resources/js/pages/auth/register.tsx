import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Mail, Lock, User } from 'lucide-react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';

interface RegisterProps {
    canLogin?: boolean;
}

export default function Register({ canLogin }: RegisterProps) {
    const { data, setData, post, processing, errors } = useForm<{
        name: string;
        email: string;
        password: string;
        password_confirmation: string;
    }>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post(route('register'));
    };

    return (
        <AppSidebarLayout>
            <Head title="Register" />

            <div className="h-full flex items-center justify-center bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] px-4">
                <div className="max-w-md w-full bg-white p-10 rounded-2xl shadow-2xl border border-gray-200">
                    <h2 className="text-4xl font-extrabold text-center mb-2 text-[#4B3B2A] font-serif">Create Your Account</h2>
                    <p className="text-center text-sm mb-6 text-[#7D5A4F] font-sans">Sign up to start exploring our bookstore</p>

                    <form onSubmit={submit} className="space-y-6 text-sm font-sans">
                        <div>
                            <label htmlFor="name" className="block mb-1 text-sm font-medium text-gray-700">Name</label>
                            <div className="flex items-center border rounded-xl px-3 py-2 bg-gray-50 shadow-sm">
                                <User className="w-4 h-4 text-gray-400 mr-2" />
                                <input
                                    id="name"
                                    type="text"
                                    placeholder="Enter your name"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full bg-transparent outline-none text-sm text-gray-700 italic"
                                    required
                                    autoFocus
                                />
                            </div>
                            {errors.name && <div className="text-sm text-red-600 mt-1">{errors.name}</div>}
                        </div>

                        <div>
                            <label htmlFor="email" className="block mb-1 text-sm font-medium text-gray-700">Email</label>
                            <div className="flex items-center border rounded-xl px-3 py-2 bg-gray-50 shadow-sm">
                                <Mail className="w-4 h-4 text-gray-400 mr-2" />
                                <input
                                    id="email"
                                    type="email"
                                    placeholder="Enter your email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    className="w-full bg-transparent outline-none text-sm text-gray-700 italic"
                                    required
                                />
                            </div>
                            {errors.email && <div className="text-sm text-red-600 mt-1">{errors.email}</div>}
                        </div>

                        <div>
                            <label htmlFor="password" className="block mb-1 text-sm font-medium text-gray-700">Password</label>
                            <div className="flex items-center border rounded-xl px-3 py-2 bg-gray-50 shadow-sm">
                                <Lock className="w-4 h-4 text-gray-400 mr-2" />
                                <input
                                    id="password"
                                    type="password"
                                    placeholder="Enter your password"
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    className="w-full bg-transparent outline-none text-sm text-gray-700 italic"
                                    required
                                />
                            </div>
                            {errors.password && <div className="text-sm text-red-600 mt-1">{errors.password}</div>}
                        </div>

                        <div>
                            <label htmlFor="password_confirmation" className="block mb-1 text-sm font-medium text-gray-700">Confirm Password</label>
                            <div className="flex items-center border rounded-xl px-3 py-2 bg-gray-50 shadow-sm">
                                <Lock className="w-4 h-4 text-gray-400 mr-2" />
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    placeholder="Re-enter your password"
                                    value={data.password_confirmation}
                                    onChange={e => setData('password_confirmation', e.target.value)}
                                    className="w-full bg-transparent outline-none text-sm text-gray-700 italic"
                                    required
                                />
                            </div>
                            {errors.password_confirmation && <div className="text-sm text-red-600 mt-1">{errors.password_confirmation}</div>}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-[#8B5E3C] hover:bg-[#704832] text-white py-2 rounded-xl text-sm font-semibold tracking-wide uppercase transition transform hover:scale-[1.02]"
                        >
                            {processing ? 'Registering...' : 'Register'}
                        </button>
                    </form>

                    {canLogin && (
                        <div className="mt-8 text-center text-sm text-gray-700 font-sans">
                            Already have an account?{' '}
                            <a href={route('login')} className="text-[#8B5E3C] hover:text-[#704832]">Login</a>
                        </div>
                    )}
                </div>
            </div>
        </AppSidebarLayout>
    );
}