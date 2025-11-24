<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
        }
        .debug-info {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .language-selector {
            margin: 20px 0;
        }
        .translated-text {
            margin-top: 30px;
            padding: 20px;
            background: #e9f7fe;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Language Test Page</h1>
    
    <div class="debug-info">
        <h2>Debug Information</h2>
        <p><strong>Current Lang Code:</strong> {{ session()->get('currentLangCode') ?? 'Not set' }}</p>
        <p><strong>Current Locale Code:</strong> {{ session()->get('currentLocaleCode') ?? 'Not set' }}</p>
        <p><strong>App Locale:</strong> {{ app()->getLocale() }}</p>
    </div>
    
    <div class="language-selector">
        <h2>Switch Language</h2>
        <a href="{{ url('changelanguage/en') }}" style="margin-right: 20px;">Switch to English</a>
        <a href="{{ url('changelanguage/fa') }}">Switch to Persian</a>
    </div>
    
    <div class="translated-text">
        <h2>Translated Text</h2>
        <p>{{ __('Admin Login') }}</p>
        <p>{{ __('Dashboard') }}</p>
        <p>{{ __('Welcome back,') }}</p>
        <p>{{ __('Login') }}</p>
        <p>{{ __('Change Password') }}</p>
    </div>
</body>
</html> 