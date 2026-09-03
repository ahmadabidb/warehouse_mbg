<x-app-layout>
    <x-slot name="title">
        {{ $record->exists ? 'Ubah' : 'Catat' }} Stok {{ $type === 'incoming' ? 'Masuk' : 'Keluar' }}
    </x-slot>

    @php
        $isIncoming = $type === 'incoming';
        $dateField  = $isIncoming ? 'tanggal_masuk' : 'tanggal_keluar';
        $storeRoute = $isIncoming ? 'stok-masuk.store' : 'stok-keluar.store';
        $updateRoute = $isIncoming ? 'stok-masuk.update' : 'stok-keluar.update';
    @endphp

    <form 
        class="card border-0 shadow-sm p-4 row g-3 col-lg-8" 
        method="POST" 
        action="{{ $record->exists ? route($updateRoute, $record) : route($storeRoute) }}"
    >
        @csrf
        @if($record->exists)
            @method('PUT')
        @endif

        <!-- Tanggal -->
        <div class="col-md-6">
            <label class="form-label">Tanggal</label>
            <input 
                class="form-control" 
                type="date" 
                name="{{ $dateField }}" 
                value="{{ old($dateField, $record->exists ? optional($record->$dateField)->format('Y-m-d') : now()->toDateString()) }}" 
                required
            >
        </div>

        <!-- Bahan Baku -->
        <div class="col-md-6">
            <label class="form-label">Bahan Baku</label>
            <select class="form-select" name="bahan_baku_id">
                @foreach($items as $item)
                    <option 
                        value="{{ $item->id }}"
                        @selected(old('bahan_baku_id', $record->bahan_baku_id) == $item->id)
                    >
                        {{ $item->kode_bahan }} — {{ $item->nama_bahan }} ({{ $item->stok }} {{ $item->satuan }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Jumlah -->
        <div class="col-md-6">
            <label class="form-label">Jumlah</label>
            <input 
                class="form-control" 
                type="number" 
                step="0.01" 
                min="0.01" 
                name="jumlah" 
                value="{{ old('jumlah', $record->jumlah) }}"
                required
            >
        </div>

        <!-- Supplier (Incoming Only) -->
        @if($isIncoming)
            <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <input class="form-control" name="supplier" value="{{ old('supplier', $record->supplier) }}">
            </div>

            <!-- Tanggal Kedaluwarsa (Incoming Only) -->
            <div class="col-md-6">
                <label class="form-label">Tanggal Kedaluwarsa</label>
                <input 
                    class="form-control" 
                    type="date" 
                    name="tanggal_expired" 
                    value="{{ old('tanggal_expired', optional($record->tanggal_expired)->format('Y-m-d')) }}"
                >
            </div>
        @endif

        <!-- Keterangan -->
        <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" name="keterangan">{{ old('keterangan', $record->keterangan) }}</textarea>
        </div>

        <!-- Submit Button -->
        <div>
            <button class="btn btn-primary">
                {{ $record->exists ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
            </button>
        </div>
    </form>
</x-app-layout>
