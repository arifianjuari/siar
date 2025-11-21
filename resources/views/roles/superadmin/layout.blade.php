@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Sidebar dan Navbar sudah diatur di layouts.app -->
    <main class="py-4 mt-5">
        @yield('content')
    </main>
</div>
@endsection

@push('styles')
    {{-- Jika ada CSS khusus superadmin, import di resources/css/app.css dan gunakan @push jika sangat spesifik --}}
@endpush 