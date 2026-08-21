<x-app-layout>
    <x-slot name="title">{{ $user->exists ? 'Edit' : 'Tambah' }} User</x-slot>

    <form 
        class="card border-0 shadow-sm p-4 col-lg-7" 
        method="POST" 
        action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}"
    >
        @csrf
        @if($user->exists)
            @method('PUT')
        @endif

        <!-- Nama -->
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input 
                class="form-control" 
                name="name" 
                value="{{ old('name', $user->name) }}" 
                required
            >
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input 
                class="form-control" 
                type="email" 
                name="email" 
                value="{{ old('email', $user->email) }}" 
                required
            >
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label">
                Password {{ $user->exists ? '(kosongkan bila tidak diubah)' : '' }}
            </label>
            <input 
                class="form-control" 
                type="password" 
                name="password" 
                {{ $user->exists ? '' : 'required' }}
            >
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input 
                class="form-control" 
                type="password" 
                name="password_confirmation"
            >
        </div>

        <!-- Role -->
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" name="role">
                @foreach($roles as $role)
                    <option @selected(old('role', $user->getRoleNames()->first()) === $role->name)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Submit Button -->
        <button class="btn btn-primary">Simpan</button>
    </form>
</x-app-layout>
