<p class="text-muted small mb-4">Gunakan password yang kuat untuk melindungi akun Anda.</p>
<form method="POST" action="{{ route('password.update') }}">
    @csrf @method('PUT')
    <div class="form-group"><label for="current_password">Password Saat Ini</label><input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror">@error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="form-group"><label for="password">Password Baru</label><input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror">@error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="form-group"><label for="password_confirmation">Konfirmasi Password Baru</label><input id="password_confirmation" name="password_confirmation" type="password" class="form-control"></div>
    <button class="btn btn-primary" type="submit"><i class="fas fa-key mr-1"></i>Ubah Password</button>
</form>
