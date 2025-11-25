# Setup Guide - This Is Me Theme

## Quick Setup

### 1. Database Setup

First, ensure your database is configured:

```bash
# If using SQLite, create the database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# Create admin user
php artisan visual-editor:create-admin
```

### 2. Sync Theme

```bash
php artisan visual-editor:sync-themes
```

You should see:
```
✓ Created theme: This Is Me (v1.0.0)
```

### 3. Activate Theme

```bash
php artisan tinker
```

Then in tinker:
```php
$theme = \ElevateCommerce\VisualEditor\Models\Theme::where('slug', 'this-is-me')->first();
$theme->activate();
```

### 4. Create a Homepage

Create a page in the admin panel or via tinker:

```php
$page = \ElevateCommerce\VisualEditor\Models\Page::create([
    'title' => 'Home',
    'slug' => '/',
    'theme_id' => $theme->id,
    'is_published' => true,
]);

// Add sections to the page
$page->render_config = [
    ['key' => 'this-is-me-hero', 'settings' => []],
    ['key' => 'this-is-me-portfolio-grid', 'settings' => []],
    ['key' => 'this-is-me-platform-showcase', 'settings' => []],
    ['key' => 'this-is-me-skills-showcase', 'settings' => []],
    ['key' => 'this-is-me-contact-cta', 'settings' => []],
];
$page->save();

// Set header and footer
$theme->header_config = [
    ['key' => 'this-is-me-header', 'settings' => []],
];
$theme->footer_config = [
    ['key' => 'this-is-me-footer', 'settings' => []],
];
$theme->save();
```

### 5. Add Portfolio Projects

Create products and tag them with 'portfolio':

```php
$project = \App\Models\Product::create([
    'name' => 'Visual Editor Platform',
    'slug' => 'visual-editor',
    'excerpt' => 'A powerful Laravel-based CMS platform I built from scratch',
    'description' => 'Complete description of your project...',
    'category' => 'Web Apps',
    'preview' => '/storage/projects/visual-editor.jpg', // Upload image first
    'price' => 0, // Not for sale, just portfolio
    'is_active' => true,
]);

// Tag it as portfolio
$project->tag('portfolio');

// Add more projects
$project2 = \App\Models\Product::create([
    'name' => 'E-Commerce Platform',
    'slug' => 'ecommerce-platform',
    'excerpt' => 'Full-featured e-commerce solution with payment integration',
    'category' => 'Platforms',
    'is_active' => true,
]);
$project2->tag('portfolio');
```

### 6. Customize Content

Visit `/admin` and customize:

1. **Hero Section**
   - Add your name
   - Upload profile photo
   - Update tagline and description
   - Set your animated roles

2. **Portfolio Grid**
   - Configure layout style
   - Set number of projects to show
   - Enable/disable filters

3. **Platform Showcase**
   - Update platform description
   - Add your tech stack
   - Customize features

4. **Skills Showcase**
   - Add your skills with proficiency levels
   - Choose layout style

5. **Contact CTA**
   - Add your email
   - Link social profiles
   - Upload resume

6. **Header & Footer**
   - Update logo
   - Set navigation links
   - Add social links

## 🎨 Customization Tips

### Upload Images

```bash
# Upload via admin panel at /admin/media
# Or programmatically:
php artisan tinker
```

```php
$media = \ElevateCommerce\VisualEditor\Models\Media::create([
    'filename' => 'profile.jpg',
    'path' => 'profile.jpg',
    'disk' => 'public',
    'mime_type' => 'image/jpeg',
    'size' => filesize(storage_path('app/public/profile.jpg')),
]);
```

### Update Colors

Edit `theme.json` to change the color scheme:

```json
{
  "settings": {
    "primary_color": "#6366F1",
    "secondary_color": "#EC4899",
    "accent_color": "#14B8A6"
  }
}
```

## 🚀 Going Live

### Before Sharing with Recruiters

1. ✅ Add at least 3-6 portfolio projects
2. ✅ Upload a professional profile photo
3. ✅ Fill in all skills with accurate proficiency levels
4. ✅ Add your real contact information
5. ✅ Upload your resume PDF
6. ✅ Link all social profiles (GitHub, LinkedIn)
7. ✅ Test on mobile devices
8. ✅ Run image optimization: `php artisan images:optimize`
9. ✅ Clear cache: `php artisan cache:clear`
10. ✅ Test contact form

### Performance Optimization

```bash
# Optimize images
php artisan images:optimize

# Cache routes and config
php artisan route:cache
php artisan config:cache
php artisan view:cache

# Enable production mode
APP_ENV=production
APP_DEBUG=false
```

## 📧 Contact Form Setup

The contact form posts to `/contact`. Create a controller:

```php
// app/Http/Controllers/ContactController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|max:255',
        'email' => 'required|email',
        'subject' => 'required|max:255',
        'message' => 'required',
    ]);

    // Save to database or send email
    Mail::to('your@email.com')->send(new ContactMessage($validated));

    return back()->with('success', 'Message sent successfully!');
}
```

## 🎯 Sharing with Recruiters

When you share your portfolio:

1. **Email Template:**
   ```
   Hi [Recruiter Name],

   I wanted to share my portfolio with you: [your-domain.com]

   I'm particularly excited about the platform I built that powers the site itself - 
   it's a full-featured CMS I developed from scratch using Laravel and Vue.js.

   Looking forward to discussing opportunities!

   Best regards,
   [Your Name]
   ```

2. **LinkedIn Post:**
   ```
   🚀 Just launched my new portfolio site!

   But here's the kicker: I built the entire CMS platform that powers it.

   Check it out: [your-domain.com]

   #Laravel #VueJS #WebDevelopment #Portfolio
   ```

## 🆘 Troubleshooting

### Theme not showing up
```bash
php artisan visual-editor:sync-themes
php artisan cache:clear
```

### Images not loading
```bash
php artisan storage:link
php artisan images:optimize
```

### Sections not rendering
Check that sections are registered:
```php
dd(app('visual-editor.section')->all());
```

---

**You're all set! Time to impress those recruiters! 🎉**

