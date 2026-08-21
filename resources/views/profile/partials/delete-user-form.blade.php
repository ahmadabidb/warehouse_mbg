<p class="text-muted small">Tindakan ini permanen. Masukkan password untuk menghapus akun Anda.</p>
<form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
    @csrf @method('DELETE')
    <div class="form-group"><label for="delete_password">Password</label><input id="delete_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" required>@error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <button class="btn btn-danger" type="submit"><i class="fas fa-trash mr-1"></i>Hapus Akun</button>
</form>
