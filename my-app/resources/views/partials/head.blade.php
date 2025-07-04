<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/vuejs.svg" sizes="any">
<link rel="icon" href="/vuejs.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/vuejs.svg">
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<!-- ApexCharts CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.css">
<!-- ApexCharts JS -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">


@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance