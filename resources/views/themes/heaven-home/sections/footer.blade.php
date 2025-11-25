{{-- Heaven Home Footer --}}
<footer class="bg-muted mt-20">
    <div class="container mx-auto px-4 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div>
                <h3 class="font-serif text-xl font-bold mb-4">{{ $settings->brand_name ?? 'Haven Home' }}</h3>
                <p class="text-muted-foreground text-sm">
                    {{ $settings->brand_tagline ?? 'Curating beautiful spaces with timeless pieces for modern living.' }}
                </p>
            </div>

            {{-- Shop --}}
            <div>
                <h4 class="font-semibold mb-4">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="/collections/living-room" class="text-muted-foreground hover:text-foreground transition-colors">
                            Living Room
                        </a>
                    </li>
                    <li>
                        <a href="/collections/bedroom" class="text-muted-foreground hover:text-foreground transition-colors">
                            Bedroom
                        </a>
                    </li>
                    <li>
                        <a href="/collections/kitchen" class="text-muted-foreground hover:text-foreground transition-colors">
                            Kitchen
                        </a>
                    </li>
                    <li>
                        <a href="/catalog" class="text-muted-foreground hover:text-foreground transition-colors">
                            All Products
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h4 class="font-semibold mb-4">Company</h4>
                <ul class="space-y-2 text-sm">
                    <li>
                        <a href="/about" class="text-muted-foreground hover:text-foreground transition-colors">
                            About Us
                        </a>
                    </li>
                    <li>
                        <a href="/contact" class="text-muted-foreground hover:text-foreground transition-colors">
                            Contact
                        </a>
                    </li>
                    <li>
                        <a href="/shipping" class="text-muted-foreground hover:text-foreground transition-colors">
                            Shipping & Returns
                        </a>
                    </li>
                    <li>
                        <a href="/privacy" class="text-muted-foreground hover:text-foreground transition-colors">
                            Privacy Policy
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Follow Us --}}
            <div>
                <h4 class="font-semibold mb-4">Follow Us</h4>
                <div class="flex gap-4">
                    <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                        </svg>
                        <span class="sr-only">Instagram</span>
                    </a>
                    <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                        </svg>
                        <span class="sr-only">Facebook</span>
                    </a>
                    <a href="#" class="text-muted-foreground hover:text-foreground transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                        </svg>
                        <span class="sr-only">Twitter</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-border mt-8 pt-8 text-center text-sm text-muted-foreground">
            &copy; {{ date('Y') }} {{ $settings->brand_name ?? 'Haven Home' }}. {{ $settings->copyright ?? 'All rights reserved.' }}
        </div>
    </div>
</footer>
