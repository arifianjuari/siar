<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Metadata dasar -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'SIAR') }}</title>
    
    @include('layouts.partials.head')
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    @stack('styles')
</head>
<body>
    @include('layouts.partials.navbar')
    <div id="app" class="wrapper">
        <div class="container-fluid">
            <div class="row">
                @auth
                    <!-- Sidebar -->
                    <aside class="col-12 col-md-3 col-lg-2 p-0">
                        @include('layouts.partials.sidebar')
                    </aside>

                    <!-- Main Content -->
                    <main class="col-12 col-md-9 col-lg-10 content-wrapper py-3">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-check-circle me-2"></i></strong> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-circle me-2"></i></strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i></strong> Terdapat kesalahan dalam input:
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        <!-- Page Header -->
                        @hasSection('header')
                            <div class="mb-4 pb-3 border-bottom">
                                @yield('header')
                            </div>
                        @elseif(!isset($hideDefaultHeader) && !Route::is('*.risk-analysis.*') && !Route::is('modules.risk-management.dashboard'))
                            <div class="mb-4 pb-3 border-bottom">
                                <h1 class="h3 mb-0">@yield('title', 'Dashboard')</h1>
                            </div>
                        @endif
                        
                        <!-- Page Content -->
                        @yield('content')
                    </main>
                @else
                    <!-- Full Width Content for guests -->
                    <main class="col-12 py-4">
                        <div class="container">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-check-circle me-2"></i></strong> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="fas fa-exclamation-circle me-2"></i></strong> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @yield('content')
                        </div>
                    </main>
                @endauth
            </div>
        </div>
        
        @include('layouts.partials.footer')
    </div>

    @stack('scripts')
</body>
</html> 