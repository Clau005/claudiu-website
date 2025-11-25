# This Is Me - Portfolio Theme

A stunning, modern portfolio theme designed to impress recruiters and showcase both your work and the Visual Editor platform you built.

## 🎨 Theme Overview

**"This Is Me"** is a premium portfolio theme that combines beautiful design with powerful storytelling. It's specifically crafted to help you land your dream job by showcasing:

- Your technical skills and expertise
- Your portfolio projects
- The Visual Editor platform you built (THE KICKER!)
- Your professional brand and personality

## ✨ Features

### Sections Included

1. **Hero Section** (`hero.blade.php`)
   - Animated introduction with rotating roles
   - Gradient background options
   - Profile image with glowing effects
   - Dual CTAs for engagement
   - Smooth scroll indicator

2. **Portfolio Grid** (`portfolio-grid.blade.php`)
   - 3 card styles: Modern, Minimal, Glassmorphism
   - Category filtering
   - Pulls from database (Products tagged with 'portfolio')
   - Responsive masonry or grid layouts
   - Tech stack tags display

3. **Platform Showcase** (`platform-showcase.blade.php`) ⚡ **THE KICKER!**
   - Highlights that this site runs on YOUR platform
   - Feature grid with icons
   - Tech stack badges
   - Live code snippet preview
   - Animated background effects
   - **This is what makes recruiters go "WOW!"**

4. **Skills Showcase** (`skills-showcase.blade.php`)
   - Categorized skills (Frontend, Backend, Tools, Other)
   - 3 layout options: Categories, Grid, Progress Bars
   - Animated progress bars on scroll
   - Proficiency levels

5. **Contact CTA** (`contact-cta.blade.php`)
   - Contact form integration
   - Social links (GitHub, LinkedIn, Twitter)
   - Resume download button
   - Email and phone display
   - Dual-column layout

6. **Header** (`header.blade.php`)
   - Fixed navigation with blur effect
   - Mobile-responsive menu
   - Smooth scroll to sections
   - CTA button in nav

7. **Footer** (`footer.blade.php`)
   - Quick links
   - Social media icons
   - Platform credit (shows off your work!)
   - Copyright information

## 🚀 Getting Started

### 1. Sync the Theme

```bash
php artisan visual-editor:sync-themes
```

### 2. Activate the Theme

```php
use ElevateCommerce\VisualEditor\Models\Theme;

$theme = Theme::where('slug', 'this-is-me')->first();
$theme->activate();
```

### 3. Create Your Portfolio Projects

Tag products with 'portfolio' to display them in the portfolio grid:

```php
use App\Models\Product;

$project = Product::create([
    'name' => 'Visual Editor Platform',
    'slug' => 'visual-editor',
    'excerpt' => 'A powerful Laravel-based CMS platform',
    'description' => 'Full description...',
    'category' => 'Web Apps',
    'preview' => '/path/to/image.jpg',
    'is_active' => true,
]);

$project->tag('portfolio');
```

### 4. Customize Your Content

Edit the section settings in the Visual Editor admin panel:

- Update your name, tagline, and bio in the Hero section
- Add your skills and proficiency levels
- Configure your contact information
- Add your social media links
- Upload your profile photo

## 🎯 The Kicker - Platform Showcase

The **Platform Showcase** section is your secret weapon. It subtly (but impressively) reveals that:

1. This entire website runs on a platform YOU built
2. You're not just a developer, you're a platform architect
3. You have the skills to build complex, production-ready systems

This section includes:
- Live code snippet showing theme configuration
- Feature highlights of your platform
- Tech stack display
- Animated effects that draw attention
- Stats showing 100% custom-built

**Pro Tip:** When sharing with recruiters, casually mention: *"Oh, and by the way, this entire site runs on a CMS platform I built from scratch."* 🎤⬇️

## 🎨 Design Features

- **Dark theme** with gradient accents (Indigo, Pink, Purple, Teal)
- **Glassmorphism** effects for modern UI
- **Smooth animations** and transitions
- **Responsive** - looks great on all devices
- **Performance optimized** with lazy loading
- **Accessible** with semantic HTML

## 📱 Responsive Design

All sections are fully responsive:
- Mobile: Single column, hamburger menu
- Tablet: 2-column grids
- Desktop: Full multi-column layouts

## 🛠️ Tech Stack

This theme showcases:
- Laravel 11 (Backend)
- Vue.js 3 (Interactive components)
- Alpine.js (Lightweight interactions)
- Tailwind CSS (Styling)
- Custom animations
- Intersection Observer API (Scroll animations)

## 📝 Customization

### Colors

The theme uses these color variables:
- Primary: Indigo (`#6366F1`)
- Secondary: Pink (`#EC4899`)
- Accent: Teal (`#14B8A6`)
- Background: Slate 950
- Text: Slate 50

### Fonts

- Headings: Space Grotesk (or fallback to system fonts)
- Body: Inter (or fallback to system fonts)

### Adding Custom Sections

You can add more sections by creating:
1. `section-configs/your-section.json` - Configuration
2. `sections/your-section.blade.php` - Template

Then run `php artisan visual-editor:sync-themes`

## 🎓 Tips for Recruiters

When presenting this portfolio to recruiters, highlight:

1. **The Platform** - "I built the CMS that powers this site"
2. **The Architecture** - "Modular theme system with JSON-driven sections"
3. **The Performance** - "Optimized images, caching, lazy loading"
4. **The Flexibility** - "Can build any type of site with this platform"
5. **The Code Quality** - "Clean, maintainable, well-documented"

## 📄 License

This theme is part of the Visual Editor platform.

---

**Built with ❤️ and Visual Editor**

*This theme is designed to help you land your dream job. Good luck! 🚀*

