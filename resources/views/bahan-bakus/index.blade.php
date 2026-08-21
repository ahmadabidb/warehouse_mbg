<x-app-layout>
    <x-slot name="title">Bahan Baku</x-slot>

    <!-- Filter Section -->
    <div class="d-flex justify-content-between mb-3">
        <form class="d-flex gap-2" method="GET">
            <input 
                class="form-control" 
                name="q" 
                value="{{ request('q') }}" 
                placeholder="Cari bahan"
            >
            <select class="form-select" name="kategori_id">
                <option value="">Semua kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('kategori_id') == $c->id)>
                        {{ $c->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <button class="btn btn-outline-secondary">Filter</button>
        </form>
        @can('bahan_baku.manage')
            <a class="btn btn-primary" href="{{ route('bahan-bakus.create') }}">
                + Tambah Bahan
            </a>
        @endcan
    </div>

    <!-- Table Section -->
    <div class="card border-0 shadow-sm table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Minimum</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->kode_bahan }}</td>
                        <td>{{ $item->nama_bahan }}</td>
                        <td>{{ $item->category->nama_kategori }}</td>
                        <td>{{ $item->stok }} {{ $item->satuan }}</td>
                        <td>{{ $item->stok_minimum }}</td>
                        <td>
                            @can('bahan_baku.manage')
                                <a 
                                    class="btn btn-sm btn-outline-primary" 
                                    href="{{ route('bahan-bakus.edit', $item) }}"
                                >
                                    Edit
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('bahan-bakus.destroy', $item) }}">
                                    @csrf 
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $items->links() }}
    </div>
</x-app-layout>
