<x-app-layout>
    <x-slot name="title">{{ $item->exists ? 'Ubah' : 'Tambah' }} Bahan Baku</x-slot>

    <form 
        class="card border-0 shadow-sm p-4 row g-3" 
        method="POST" 
        action="{{ $item->exists ? route('bahan-bakus.update', $item) : route('bahan-bakus.store') }}"
    >
        @csrf
        @if($item->exists)
            @method('PUT')
        @endif

        <!-- Kode Bahan -->
        <div class="col-md-6">
            <label class="form-label">Kode Bahan</label>
            <input 
                class="form-control" 
                value="{{ $item->exists ? $item->kode_bahan : 'Dibuat otomatis saat disimpan' }}" 
                readonly
            >
        </div>

        <!-- Nama Bahan -->
        <div class="col-md-6">
            <label class="form-label">Nama Bahan</label>
            <input 
                class="form-control" 
                name="nama_bahan" 
                value="{{ old('nama_bahan', $item->nama_bahan) }}" 
                required
            >
        </div>

        <!-- Stok Awal & Stok Minimum -->
        @foreach(['stok' => 'Stok Awal', 'stok_minimum' => 'Stok Minimum'] as $field => $label)
            <div class="col-md-6">
                <label class="form-label">{{ $label }}</label>
                <input 
                    class="form-control" 
                    type="number" 
                    step="0.01" 
                    name="{{ $field }}" 
                    value="{{ old($field, $item->$field) }}" 
                    required
                >
            </div>
        @endforeach

        <!-- Kategori -->
        <div class="col-md-6">
            <label class="form-label">Kategori</label>
            <select class="form-select" name="kategori_id">
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('kategori_id', $item->kategori_id) == $c->id)>
                        {{ $c->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Satuan -->
        <div class="col-md-6">
            <label class="form-label">Satuan</label>
            <select class="form-select" name="satuan">
                @foreach(['Kg', 'Gram', 'Liter', 'Ml', 'Pcs', 'Pack'] as $u)
                    <option @selected(old('satuan', $item->satuan) == $u)>
                        {{ $u }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Deskripsi -->
        <div class="col-12">
            <label class="form-label">Deskripsi</label>
            <textarea class="form-control" name="deskripsi">{{ old('deskripsi', $item->deskripsi) }}</textarea>
        </div>

        <!-- Submit Button -->
        <div>
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
</x-app-layout>
