{{-- Contact CTA Section --}}
@php
    $bgClass = ($settings->background_style ?? 'gradient') === 'gradient' 
        ? 'bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900' 
        : 'bg-slate-950';
@endphp

<section id="contact" class="relative py-20 lg:py-32 {{ $bgClass }} overflow-hidden">
    {{-- Background Elements --}}
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="max-w-5xl mx-auto">
            {{-- Header --}}
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6">
                    {{ $settings->title }}
                </h2>
                @if(!empty($settings->subtitle))
                    <p class="text-xl text-slate-300 max-w-2xl mx-auto">
                        {{ $settings->subtitle }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Contact Info --}}
                <div class="space-y-8">
                    <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10">
                        <h3 class="text-2xl font-bold text-white mb-6">Get In Touch</h3>
                        
                        <div class="space-y-4">
                            @if(!empty($settings->email))
                                <a href="mailto:{{ $settings->email }}" 
                                   class="flex items-center gap-4 text-slate-300 hover:text-white transition-colors group">
                                    <div class="w-12 h-12 bg-indigo-500/20 rounded-lg flex items-center justify-center group-hover:bg-indigo-500/30 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-slate-400">Email</div>
                                        <div class="font-medium">{{ $settings->email }}</div>
                                    </div>
                                </a>
                            @endif

                            @if(!empty($settings->phone))
                                <a href="tel:{{ $settings->phone }}" 
                                   class="flex items-center gap-4 text-slate-300 hover:text-white transition-colors group">
                                    <div class="w-12 h-12 bg-pink-500/20 rounded-lg flex items-center justify-center group-hover:bg-pink-500/30 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm text-slate-400">Phone</div>
                                        <div class="font-medium">{{ $settings->phone }}</div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Social Links --}}
                    <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10">
                        <h3 class="text-xl font-bold text-white mb-6">Connect With Me</h3>
                        <div class="flex flex-wrap gap-4">
                            @if(!empty($settings->github_url))
                                <a href="{{ $settings->github_url }}" target="_blank" rel="noopener"
                                   class="flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                    GitHub
                                </a>
                            @endif

                            @if(!empty($settings->linkedin_url))
                                <a href="{{ $settings->linkedin_url }}" target="_blank" rel="noopener"
                                   class="flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    LinkedIn
                                </a>
                            @endif

                            @if(!empty($settings->twitter_url))
                                <a href="{{ $settings->twitter_url }}" target="_blank" rel="noopener"
                                   class="flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors text-white">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    Twitter
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Resume Download --}}
                    @if(!empty($settings->resume_url) && !empty($settings->cta_secondary_text))
                        <a href="{{ $settings->resume_url }}" 
                           class="flex items-center justify-center gap-2 w-full px-8 py-4 text-lg font-semibold text-white border-2 border-white/20 rounded-lg hover:bg-white/10 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ $settings->cta_secondary_text }}
                        </a>
                    @endif
                </div>

                {{-- Contact Form --}}
                @if($settings->show_contact_form ?? true)
                    <div class="bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10" id="contact-form">
                        <h3 class="text-2xl font-bold text-white mb-6">Send a Message</h3>
                        
                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-100 animate-slide-in-fade"
                                 x-data="{ show: true }"
                                 x-show="show"
                                 x-init="setTimeout(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'center' }) }, 100)">
                                <div class="flex items-center gap-3">
                                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-100 animate-slide-in-fade"
                                 x-data="{ show: true }"
                                 x-show="show"
                                 x-init="setTimeout(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'center' }) }, 100)">
                                <div class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <ul class="space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        
                        <form action="{{ route('inquiries.store') }}" method="POST" class="space-y-6" 
                              x-data="{ submitting: false }"
                              @submit="submitting = true">
                            @csrf
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Subject</label>
                                <input type="text" name="subject" value="{{ old('subject') }}" required
                                       class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Message</label>
                                <textarea name="message" rows="5" required
                                          class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('message') }}</textarea>
                            </div>

                            <button type="submit"
                                    :disabled="submitting"
                                    class="w-full px-8 py-4 text-lg font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-300 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <span x-show="!submitting">{{ $settings->cta_primary_text ?? 'Send Message' }}</span>
                                <span x-show="submitting" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
    @keyframes slide-in-fade {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-slide-in-fade {
        animation: slide-in-fade 0.5s ease-out;
    }
</style>

