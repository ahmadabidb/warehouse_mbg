<p class="text-muted small mb-4">Perbarui nama dan alamat email akun Anda.</p>
<form method="POST" action="{{ route('profile.update') }}">
    @csrf @method('PATCH')
    <div class="form-group"><label for="name">Nama</label><input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="form-group"><label for="email">Email</label><input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
</form>
