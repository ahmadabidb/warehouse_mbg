<x-app-layout>
    <x-slot name="title">Catat Stok {{ $type === 'incoming' ? 'Masuk' : 'Keluar' }}</x-slot>

    <form 
        class="card border-0 shadow-sm p-4 row g-3 col-lg-8" 
        method="POST" 
        action="{{ route($type === 'incoming' ? 'stok-masuk.store' : 'stok-keluar.store') }}"
    >
        @csrf

        <!-- Tanggal -->
        <div class="col-md-6">
            <label class="form-label">Tanggal</label>
            <input 
                class="form-control" 
                type="date" 
                name="tanggal_{{ $type === 'incoming' ? 'masuk' : 'keluar' }}" 
                value="{{ now()->toDateString() }}" 
                required
            >
        </div>

        <!-- Bahan Baku -->
        <div class="col-md-6">
            <label class="form-label">Bahan Baku</label>
            <select class="form-select" name="bahan_baku_id">
                @foreach($items as $item)
                    <option value="{{ $item->id }}">
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
                required
            >
        </div>

        <!-- Supplier (Incoming Only) -->
        @if($type === 'incoming')
            <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <input class="form-control" name="supplier">
            </div>

            <!-- Tanggal Kedaluwarsa (Incoming Only) -->
            <div class="col-md-6">
                <label class="form-label">Tanggal Kedaluwarsa</label>
                <input class="form-control" type="date" name="tanggal_expired">
            </div>
        @endif

        <!-- Keterangan -->
        <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" name="keterangan"></textarea>
        </div>

        <!-- Submit Button -->
        <div>
            <button class="btn btn-primary">Simpan Transaksi</button>
        </div>
    </form>
</x-app-layout>
