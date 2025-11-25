{{-- Heaven Home - Contact Section --}}
<div class="min-h-screen">
    {{-- Hero Section --}}
    <section class="relative h-[300px] flex items-center bg-gradient-to-r from-gray-900 to-gray-800">
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                @if(!empty($settings->page_title))
                    <h1 class="font-serif text-5xl md:text-6xl font-bold text-white mb-4">
                        {{ $settings->page_title }}
                    </h1>
                @endif
                
                @if(!empty($settings->page_subtitle))
                    <p class="text-xl text-white/90">
                        {{ $settings->page_subtitle }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Contact Form + Info Section --}}
    <section class="container mx-auto px-4 lg:px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Contact Information --}}
            <div class="space-y-8">
                @if(!empty($settings->contact_intro))
                    <div>
                        <p class="text-gray-700 leading-relaxed">
                            {{ $settings->contact_intro }}
                        </p>
                    </div>
                @endif

                @if(!empty($settings->show_contact_info))
                    <div class="space-y-6">
                        @if(!empty($settings->contact_email))
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-1">Email</h3>
                                    <a href="mailto:{{ $settings->contact_email }}" class="text-gray-600 hover:text-gray-900">
                                        {{ $settings->contact_email }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if(!empty($settings->contact_phone))
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-1">Phone</h3>
                                    <a href="tel:{{ $settings->contact_phone }}" class="text-gray-600 hover:text-gray-900">
                                        {{ $settings->contact_phone }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if(!empty($settings->contact_address))
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-1">Address</h3>
                                    <p class="text-gray-600 whitespace-pre-line">
                                        {{ $settings->contact_address }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if(!empty($settings->contact_hours))
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-1">Hours</h3>
                                    <p class="text-gray-600 whitespace-pre-line">
                                        {{ $settings->contact_hours }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Contact Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-8">
                    @if(!empty($settings->form_title))
                        <h2 class="font-serif text-3xl font-bold mb-2">
                            {{ $settings->form_title }}
                        </h2>
                    @endif
                    
                    @if(!empty($settings->form_subtitle))
                        <p class="text-gray-600 mb-8">
                            {{ $settings->form_subtitle }}
                        </p>
                    @endif

                    <x-visual-editor::contact-form
                        :showName="$settings->show_name ?? true"
                        :showEmail="$settings->show_email ?? true"
                        :showPhone="$settings->show_phone ?? true"
                        :showCompany="$settings->show_company ?? false"
                        :showSubject="$settings->show_subject ?? true"
                        :showMessage="$settings->show_message ?? true"
                        :showType="$settings->show_type ?? false"
                        source="contact-page"
                        :buttonText="$settings->button_text ?? 'Send Message'"
                        :successMessage="$settings->success_message ?? null"
                    />
                </div>
            </div>
        </div>
    </section>

    {{-- Map Section (Optional) --}}
    @if(!empty($settings->show_map) && !empty($settings->map_embed))
        <section class="w-full h-[400px]">
            <div class="w-full h-full">
                {!! $settings->map_embed !!}
            </div>
        </section>
    @endif
</div>
