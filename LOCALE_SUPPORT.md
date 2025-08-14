# Application Locale Support in Laravel Export

This demonstrates how the `--locale` option sets both the application locale and the export file organization.

## Example with Translations

When you run:
```bash
php artisan export --locale=fr
```

The command will:

1. **Set the application locale**: `app()->setLocale('fr')`
2. **Set the export locale**: `$exporter->setLocale('fr')`

This means any content that depends on the application locale will be exported with the correct locale:

### Example Route with Translation
```php
// routes/web.php
Route::get('/welcome', function () {
    return view('welcome');
});
```

### Example Blade Template
```html
<!-- resources/views/welcome.blade.php -->
<h1>{{ __('messages.welcome') }}</h1>
<p>{{ __('messages.description') }}</p>
<p>Current locale: {{ app()->getLocale() }}</p>
```

### Translation Files
```php
// resources/lang/en/messages.php
return [
    'welcome' => 'Welcome',
    'description' => 'This is the English version.',
];

// resources/lang/fr/messages.php  
return [
    'welcome' => 'Bienvenue',
    'description' => 'Ceci est la version française.',
];
```

### Export Results

**Default export:**
```bash
php artisan export
```
Creates: `dist/welcome/index.html` with English content:
```html
<h1>Welcome</h1>
<p>This is the English version.</p>
<p>Current locale: en</p>
```

**French export:**
```bash
php artisan export --locale=fr
```
Creates: `dist/fr/welcome/index.html` with French content:
```html
<h1>Bienvenue</h1>
<p>Ceci est la version française.</p>
<p>Current locale: fr</p>
```

## Benefits

- **Automatic locale organization**: Files are automatically organized into locale subdirectories
- **Proper translation support**: All Laravel translation functions work correctly
- **Date/number formatting**: Locale-specific formatting is applied automatically
- **Multi-language static sites**: Easy to generate multiple language versions

## Usage Patterns

### Single Language Export
```bash
php artisan export --locale=fr
```

### Multiple Language Export (using scripts)
```bash
# Export English version
php artisan export --locale=en

# Export French version  
php artisan export --locale=fr

# Export Spanish version
php artisan export --locale=es
```

This creates:
```
dist/
├── en/
│   ├── welcome/index.html
│   └── about/index.html
├── fr/
│   ├── welcome/index.html
│   └── about/index.html
└── es/
    ├── welcome/index.html
    └── about/index.html
```
