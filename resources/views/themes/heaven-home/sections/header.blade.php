{{-- Heaven Home Navigation --}}
<nav class="sticky top-0 z-50 backdrop-blur-lg bg-background/90 border-b border-border">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="/" class="font-serif text-2xl font-bold text-foreground hover:text-accent transition-colors">
                {{ $settings->logo_text ?? 'Haven Home' }}
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="/" class="text-foreground hover:text-accent transition-colors font-medium">
                    Home
                </a>
                <a href="/our-products" class="text-foreground hover:text-accent transition-colors font-medium">
                    Our Catalog
                </a>
                <a href="/about-us" class="text-foreground hover:text-accent transition-colors font-medium">
                    About
                </a>
                <a href="/contact-us" class="text-foreground hover:text-accent transition-colors font-medium">
                    Contact
                </a>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-4">
                {{-- Wishlist --}}
                <a href="/wishlist" class="relative inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-10 w-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                    </svg>
                    <span class="wishlist-count absolute -top-1 -right-1 bg-accent text-accent-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium {{ session('wishlist_count', 0) > 0 ? '' : 'hidden' }}">
                        {{ session('wishlist_count', 0) }}
                    </span>
                </a>
                
                {{-- Cart --}}
                <a href="/cart" class="relative inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-10 w-10">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="21" r="1"/>
                        <circle cx="19" cy="21" r="1"/>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                    </svg>
                    <span class="cart-count absolute -top-1 -right-1 bg-accent text-accent-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium {{ session('cart_count', 0) > 0 ? '' : 'hidden' }}">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>

                {{-- Mobile Menu Toggle --}}
                <button type="button" class="md:hidden inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-10 w-10" onclick="toggleMobileMenu()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" x2="20" y1="12" y2="12"/>
                        <line x1="4" x2="20" y1="6" y2="6"/>
                        <line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="hidden md:hidden border-t border-border">
        <div class="container mx-auto px-4 py-4 flex flex-col gap-4">
            <a href="/" class="text-foreground hover:text-accent transition-colors font-medium py-2">
                Home
            </a>
            <a href="/catalog" class="text-foreground hover:text-accent transition-colors font-medium py-2">
                Shop
            </a>
            <a href="/about" class="text-foreground hover:text-accent transition-colors font-medium py-2">
                About
            </a>
            <a href="/contact" class="text-foreground hover:text-accent transition-colors font-medium py-2">
                Contact
            </a>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}

// Listen for cart and wishlist update events
window.addEventListener('cart:updated', function(event) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        const count = event.detail.count || 0;
        cartCountElement.textContent = count;
        if (count > 0) {
            cartCountElement.classList.remove('hidden');
        } else {
            cartCountElement.classList.add('hidden');
        }
    }
});

window.addEventListener('wishlist:updated', function(event) {
    const wishlistCountElement = document.querySelector('.wishlist-count');
    if (wishlistCountElement) {
        const count = event.detail.count || 0;
        wishlistCountElement.textContent = count;
        if (count > 0) {
            wishlistCountElement.classList.remove('hidden');
        } else {
            wishlistCountElement.classList.add('hidden');
        }
    }
});

</script>
