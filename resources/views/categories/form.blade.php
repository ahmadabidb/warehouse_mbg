<x-app-layout>
    <x-slot name="title">{{ $category->exists ? 'Edit' : 'Tambah' }} Kategori</x-slot>

    <form 
        class="card border-0 shadow-sm p-4 col-lg-7" 
        method="POST" 
        action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}"
    >
        @csrf
        @if($category->exists)
            @method('PUT')
        @endif

        <!-- Nama Kategori -->
        <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input 
                class="form-control" 
                name="nama_kategori" 
                value="{{ old('nama_kategori', $category->nama_kategori) }}" 
                required
            >
        </div>

        <!-- Deskripsi -->
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" name="deskripsi">
                {{ old('deskripsi', $category->deskripsi) }}
            </textarea>
        </div>

        <!-- Submit Button -->
        <button class="btn btn-primary">Simpan</button>
    </form>
</x-app-layout>
