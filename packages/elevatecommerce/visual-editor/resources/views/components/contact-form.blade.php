@props([
    'showName' => true,
    'showEmail' => true,
    'showPhone' => true,
    'showCompany' => false,
    'showSubject' => true,
    'showMessage' => true,
    'showType' => false,
    'source' => 'contact-form',
    'buttonText' => 'Send Message',
    'successMessage' => null,
])

<div class="contact-form">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ $successMessage ?? session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('inquiries.store') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="source" value="{{ $source }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($showName)
                <div class="{{ $showCompany ? '' : 'md:col-span-2' }}">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name"
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                        placeholder="Your name"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if($showCompany)
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                        Company
                    </label>
                    <input 
                        type="text" 
                        id="company"
                        name="company" 
                        value="{{ old('company') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                        placeholder="Your company"
                    >
                    @error('company')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($showEmail)
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                        placeholder="your@email.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if($showPhone)
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone {{ $showEmail ? '' : '<span class="text-red-500">*</span>' }}
                    </label>
                    <input 
                        type="tel" 
                        id="phone"
                        name="phone" 
                        value="{{ old('phone') }}"
                        {{ !$showEmail ? 'required' : '' }}
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                        placeholder="+1 (555) 000-0000"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        @if($showType)
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                    Inquiry Type
                </label>
                <select 
                    id="type"
                    name="type" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                >
                    <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>General Inquiry</option>
                    <option value="support" {{ old('type') === 'support' ? 'selected' : '' }}>Support</option>
                    <option value="sales" {{ old('type') === 'sales' ? 'selected' : '' }}>Sales</option>
                    <option value="partnership" {{ old('type') === 'partnership' ? 'selected' : '' }}>Partnership</option>
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        @if($showSubject)
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="subject"
                    name="subject" 
                    value="{{ old('subject') }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                    placeholder="How can we help you?"
                >
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        @if($showMessage)
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="message"
                    name="message" 
                    rows="6"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-900"
                    placeholder="Tell us more about your inquiry..."
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        {{ $slot }}

        <div>
            <button 
                type="submit" 
                class="w-full md:w-auto px-8 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors font-medium"
            >
                {{ $buttonText }}
            </button>
        </div>
    </form>
</div>
