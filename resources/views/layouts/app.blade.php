<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} | SIM-STOK MBG GENENGAN</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bgn.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('SB Admin 2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('SB Admin 2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        .page-heading { font-size: 1.5rem; font-weight: 700; color: #5a5c69; margin-bottom: 1.5rem; }
        .container-fluid > .card, .container-fluid > form.card { border: 0; border-radius: .35rem; box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15)!important; margin-bottom: 1.5rem; }
        .container-fluid .table { color: #5a5c69; margin-bottom: 0; }
        .container-fluid .table thead th { color: #4e73df; font-size: .75rem; text-transform: uppercase; letter-spacing: .04em; border-top: 0; background: #f8f9fc; }
        .container-fluid .table td, .container-fluid .table th { vertical-align: middle; padding: .9rem; }
        .container-fluid .form-control, .container-fluid .form-select { min-height: 38px; border-radius: .35rem; }
        .container-fluid textarea.form-control { min-height: 110px; }
        .container-fluid .btn { border-radius: .35rem; font-size: .82rem; font-weight: 600; }
        .container-fluid .alert { border: 0; box-shadow: 0 .15rem 1.25rem rgba(58,59,69,.12); }
        .table-responsive { border-radius: .35rem; }
        .pagination { margin-top: 1rem; }
        .pagination .page-link { color: #4e73df; }
        .pagination .active .page-link { background-color: #4e73df; border-color: #4e73df; }
        .sidebar .nav-item .nav-link:hover,
        .sidebar .nav-item.active .nav-link { background: #fff; color: #4e73df!important; border-radius: .35rem; margin: 0 .5rem; width: auto; }
        .sidebar .nav-item .nav-link:hover i,
        .sidebar .nav-item.active .nav-link i { color: #4e73df!important; }
        .sidebar .nav-item .nav-link { transition: all .18s ease-in-out; }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
            <div class="sidebar-brand-icon"><img src="{{ asset('images/logo-bgn.png') }}" alt="Logo BGN" style="width: 40px; height: 40px;"></div>
            <div class="sidebar-brand-text mx-2">SIM-STOK <sup>MBG</sup></div>
        </a>
        <hr class="sidebar-divider my-0">
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-fw fa-tachometer-alt"></i><span>Beranda</span></a>
        </li>
        @canany(['category.manage', 'bahan_baku.view', 'bahan_baku.manage'])
            <hr class="sidebar-divider"><div class="sidebar-heading">Master Data</div>
            @can('category.manage')<li class="nav-item"><a class="nav-link" href="{{ route('categories.index') }}"><i class="fas fa-fw fa-tags"></i><span>Kategori</span></a></li>@endcan
            @canany(['bahan_baku.view', 'bahan_baku.manage'])<li class="nav-item"><a class="nav-link" href="{{ route('bahan-bakus.index') }}"><i class="fas fa-fw fa-carrot"></i><span>Bahan Baku</span></a></li>@endcan
        @endcanany
        <hr class="sidebar-divider"><div class="sidebar-heading">Transaksi</div>
        @can('stok_masuk.create')<li class="nav-item"><a class="nav-link" href="{{ route('stok-masuk.index') }}"><i class="fas fa-fw fa-arrow-circle-down"></i><span>Stok Masuk</span></a></li>@endcan
        @can('stok_keluar.create')<li class="nav-item"><a class="nav-link" href="{{ route('stok-keluar.index') }}"><i class="fas fa-fw fa-arrow-circle-up"></i><span>Stok Keluar</span></a></li>@endcan
        @can('stok.view')<li class="nav-item"><a class="nav-link" href="{{ route('stok.monitoring') }}"><i class="fas fa-fw fa-clipboard-list"></i><span>Pemantauan Stok</span></a></li>@endcan
        @can('laporan.view')
            <hr class="sidebar-divider"><div class="sidebar-heading">Laporan</div>
            <li class="nav-item"><a class="nav-link" href="{{ route('reports.incoming') }}"><i class="fas fa-fw fa-file-import"></i><span>Laporan Stok Masuk</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('reports.outgoing') }}"><i class="fas fa-fw fa-file-export"></i><span>Laporan Stok Keluar</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('reports.opname') }}"><i class="fas fa-fw fa-file-alt"></i><span>Stok Opname</span></a></li>
        @endcan
        @can('user.manage')<hr class="sidebar-divider"><li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}"><i class="fas fa-fw fa-users"></i><span>Pengguna</span></a></li>@endcan
        <hr class="sidebar-divider d-none d-md-block">
    </ul>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                <span class="font-weight-bold text-primary">{{ $title ?? 'Dashboard' }}</span>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->name }}</span>
                            <i class="fas fa-user-circle fa-lg text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profil</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item" type="submit"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Keluar</button></form>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="container-fluid">
                @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="close" data-dismiss="alert">&times;</button></div>@endif
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <h1 class="page-heading">{{ $title ?? 'Dashboard' }}</h1>
                {{ $slot }}
            </div>
        </div>
        <footer class="sticky-footer bg-white"><div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; SIM-STOK MBG GENENGAN {{ now()->year }}</span></div></div></footer>
    </div>
</div>
<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
<script src="{{ asset('SB Admin 2/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('SB Admin 2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('SB Admin 2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('SB Admin 2/js/sb-admin-2.min.js') }}"></script>
</body>
</html>
