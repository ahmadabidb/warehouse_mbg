<x-guest-layout>
    <div class="text-center">
        <div class="mb-3">
            <img src="{{ asset('images/logo-bgn.png') }}" alt="Logo BGN" style="max-width: 200px; height: auto;">
        </div>
        <h1 class="h4 text-gray-900 mb-2">SISTEM STOK MBG GENENGAN</h1>
        <p class="small text-muted mb-4">Masuk untuk mengelola persediaan dapur.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form class="user" method="POST" action="{{ route('login') }}" style="max-width: 400px; margin: 0 auto;">
        @csrf
        <div class="form-group">
            <input id="email" class="form-control form-control-user @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Alamat email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <input id="password" class="form-control form-control-user @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" placeholder="Password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox small">
                <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                <label class="custom-control-label" for="remember">Ingat saya</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-user btn-block">Masuk</button>
    </form>
    <hr>    
    @if (Route::has('password.request'))
        <div class="text-center"><a class="small" href="{{ route('password.request') }}">Lupa password?</a></div>
    @endif
</x-guest-layout>
