import React from 'react';
import AppLayout from '@/layouts/app-layout';
import { Instagram, Facebook, Mail, Phone } from 'lucide-react';

export default function ContactPage() {
  return (
    <AppLayout>
      <div className="h-full bg-gradient-to-br from-[#f9f5f0] via-[#ede3d9] to-[#d6c2aa] py-12 px-6">
        <div className="max-w-3xl mx-auto">

          <h1 className="text-4xl font-extrabold text-[#4B3B2A] font-serif text-center mb-10">📬 Contact Us</h1>

          {/* Social Cards */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
            <div className="flex items-center gap-3 bg-white p-4 rounded-lg border border-[#d6c2aa] shadow-sm transition transform hover:shadow-md hover:-translate-y-1 hover:font-semibold">
              <Instagram className="text-[#8B5E3C] w-5 h-5" />
              <div className="text-[#4B3B2A] text-sm font-medium">@thebooksbarn</div>
            </div>

            <div className="flex items-center gap-3 bg-white p-4 rounded-lg border border-[#d6c2aa] shadow-sm transition transform hover:shadow-md hover:-translate-y-1 hover:font-semibold">
              <Facebook className="text-[#8B5E3C] w-5 h-5" />
              <div className="text-[#4B3B2A] text-sm font-medium">facebook.com/thebooksbarn</div>
            </div>

            <div className="flex items-center gap-3 bg-white p-4 rounded-lg border border-[#d6c2aa] shadow-sm transition transform hover:shadow-md hover:-translate-y-1 hover:font-semibold">
              <Mail className="text-[#8B5E3C] w-5 h-5" />
              <div className="text-[#4B3B2A] text-sm font-medium">support@booksbarn.in</div>
            </div>

            <div className="flex items-center gap-3 bg-white p-4 rounded-lg border border-[#d6c2aa] shadow-sm transition transform hover:shadow-md hover:-translate-y-1 hover:font-semibold">
              <Phone className="text-[#8B5E3C] w-5 h-5" />
              <div className="text-[#4B3B2A] text-sm font-medium">+91 98765 43210</div>
            </div>
          </div>

          {/* Contact Form */}
          <div className="bg-white border border-[#d6c2aa] rounded-2xl shadow-lg p-8">
            <p className="text-[#5C4033] font-sans mb-6 text-center text-sm">
              Have a question or message? Fill out the form and we’ll respond as soon as possible.
            </p>

            <form className="space-y-5">
              <div>
                <label className="block text-[#4B3B2A] font-medium mb-1 text-sm">Your Name</label>
                <input
                  type="text"
                  className="w-full border border-[#d6c2aa] rounded-md px-4 py-2 text-sm font-sans bg-[#f9f5f0] focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]"
                  placeholder="Enter your name"
                />
              </div>

              <div>
                <label className="block text-[#4B3B2A] font-medium mb-1 text-sm">Email Address</label>
                <input
                  type="email"
                  className="w-full border border-[#d6c2aa] rounded-md px-4 py-2 text-sm font-sans bg-[#f9f5f0] focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]"
                  placeholder="you@example.com"
                />
              </div>

              <div>
                <label className="block text-[#4B3B2A] font-medium mb-1 text-sm">Message</label>
                <textarea
                  rows={5}
                  className="w-full border border-[#d6c2aa] rounded-md px-4 py-2 text-sm font-sans bg-[#f9f5f0] focus:outline-none focus:ring-2 focus:ring-[#8B5E3C]"
                  placeholder="How can we help you?"
                />
              </div>

              <button
                type="submit"
                className="bg-[#8B5E3C] hover:bg-[#704832] text-white font-semibold text-sm px-6 py-2 rounded-md transition w-full uppercase tracking-wider"
              >
                Send Message
              </button>
            </form>
          </div>

        </div>
      </div>
    </AppLayout>
  );
}