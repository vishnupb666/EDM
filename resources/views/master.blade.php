<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'My Laravel App')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        display: flex;
        min-height: 100vh;
        flex-direction: column;
    }
    .wrapper {
        display: flex;
        flex: 1;
    }
    .content {
        flex: 1;
        padding: 20px;
    }
</style>
@yield('styles')
</head>
<body>

{{-- HEADER --}}
@include('layout.header')

<div class="wrapper">
    {{-- SIDEBAR --}}
    @include('layout.sidebar')

    {{-- MAIN CONTENT --}}
    <div class="content">
        @yield('content')
    </div>
</div>

{{-- FOOTER --}}
@include('layout.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
