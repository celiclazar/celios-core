# Celios Starter Theme Kit

A dead-simple starter boilerplate for creating custom frontend themes in **Celios CMS**.

## Theme Directory Structure
```text
starter-theme/
├── theme.json                     # Theme manifest (name, ID, version, menu locations)
├── screenshot.png                 # Gallery card preview image in Admin Panel
├── assets/                        # Public assets (copied to public/themes/starter-theme/)
│   ├── css/
│   │   └── theme.css              # Custom stylesheet
│   └── js/
│       └── theme.js               # Custom client-side JavaScript
└── views/                         # Blade templates (overrides core views)
    ├── partials/
    │   ├── nav.blade.php          # Header and navigation
    │   └── footer.blade.php       # Footer and copyright
    └── cms/
        └── render.blade.php       # Full page wrapper template
```

## How to Create & Install a Theme

1. **Customize HTML & CSS**:
   - Edit `views/partials/nav.blade.php` and `views/partials/footer.blade.php` with your HTML layout.
   - Add styles in `assets/css/theme.css`.
   - Update `theme.json` with your custom theme `name`, `id`, and `author`.

2. **Package into a ZIP**:
   - Compress the folder contents into a `.zip` file (make sure `theme.json` is at the root of the ZIP).

3. **Install & Activate**:
   - Open **Celios Admin** > **Settings** > **Theme Settings** (`/admin/settings/themes`).
   - Click **Install Theme (.zip)**, select your zip file, and click Submit.
   - Click **Activate** on your newly installed theme card.

## Menu Locations
Declare menu slots in `theme.json`:
```json
{
  "menu_locations": {
    "primary": "Header Navigation Menu",
    "footer": "Footer Links"
  }
}
```
Celios will automatically create dropdown inputs in the Admin Panel so admins can link menus to your theme slots!
