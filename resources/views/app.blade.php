<!DOCTYPE html>
<html lang="id" translate="yes">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'SkillPath AI') }}</title>

    <link rel="icon" type="image/png" href="/assets/logoKibot.png?v=2">
    <link rel="shortcut icon" type="image/png" href="/assets/logoKibot.png?v=2">
    <link rel="apple-touch-icon" href="/assets/logoKibot.png?v=2">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
