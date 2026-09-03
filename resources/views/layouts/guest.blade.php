<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Autentikasi | SIM-STOK MBG GENENGAN</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bgn.png') }}">
    <link href="{{ asset('SB Admin 2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('SB Admin 2/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                     <div class="card-body p-0">
                         <div class="row">
                             <div class="col-lg-12">
                                 <div class="p-5">{{ $slot }}</div>
                             </div>
                         </div>
                     </div>
                </div>
                <div class="text-center text-white small mb-4">SIM-STOK MBG GENENGAN &copy; {{ now()->year }}</div>
            </div>
        </div>
    </div>
</body>
</html>
