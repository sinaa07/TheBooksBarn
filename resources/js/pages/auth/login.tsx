import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';
import { Mail, Lock } from 'lucide-react';
import AppSidebarLayout from '@/layouts/app/app-sidebar-layout';

interface LoginProps {
    status?: string;
    canResetPassword?: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
  const { data, setData, post, processing, errors } = useForm<{
    email: string;
    password: string;
    remember: boolean;
}>({
    email: '',
    password: '',
    remember: false,
});;

    const submit = (e: FormEvent) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <AppSidebarLayout>
            <Head title="Login" />

            <div className="h-full flex items-center justify-center bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] px-4">
                <div className="max-w-md w-full bg-white p-10 rounded-2xl shadow-2xl border border-gray-200">
                    <h2 className="text-4xl font-extrabold text-center mb-2 text-[#4B3B2A] font-serif">Welcome Back</h2>
                    <p className="text-center text-sm mb-6 text-[#7D5A4F] font-sans">Login to your account to continue</p>

                    {status && (
                        <div className="mb-4 text-sm text-green-600">{status}</div>
                    )}

                    <form onSubmit={submit} className="space-y-6 text-sm font-sans">
                        <div className="space-y-3">
                            <label htmlFor="email" className="block mb-1 text-sm font-medium text-[#4B3B2A] font-sans">Email</label>
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
                                    autoFocus
                                />
                            </div>
                            {errors.email && <div className="text-sm text-red-600 mt-1">{errors.email}</div>}
                        </div>

                        <div className="space-y-3">
                            <label htmlFor="password" className="block mb-1 text-sm font-medium text-[#4B3B2A] font-sans">Password</label>
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

                        <div className="flex items-center justify-between text-sm mb-4">
                            <label className="flex items-center  text-[#4B3B2A] font-sans">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                    className="mr-2"
                                />
                                Remember me
                            </label>
                            {canResetPassword && (
                                <a href={route('password.request')} className="text-[#8B5E3C] hover:text-[#704832] font-sans">
                                    Forgot password?
                                </a>
                            )}
                        </div>

                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-[#8B5E3C] hover:bg-[#704832] text-white py-2 rounded-xl text-sm font-semibold tracking-wide uppercase transition transform hover:scale-[1.02]"
                        >
                            {processing ? 'Logging in...' : 'Login'}
                        </button>
                    </form>

                    <div className="mt-8 text-center text-sm text-gray-700 font-sans">
                        Don’t have an account?{' '}
                        <a href={route('register')} className="text-[#8B5E3C] hover:text-[#704832]">Register</a>
                    </div>
                </div>
            </div>
        </AppSidebarLayout>
    );
}