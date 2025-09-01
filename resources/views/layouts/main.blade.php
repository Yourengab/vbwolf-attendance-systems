<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
     <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
     <script defer src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body>
    @yield('content')
</body>
<script>
    lucide.createIcons();
  </script>
</html>