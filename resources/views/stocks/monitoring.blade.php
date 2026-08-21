<x-app-layout>
    <x-slot name="title">Pemantauan Stok</x-slot>

    <!-- Table Section -->
    <div class="card shadow mb-4">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Stok Minimum</th>
                        <th>Status</th>
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
                                <span class="badge badge-{{ $item->status === 'Aman' ? 'success' : ($item->status === 'Menipis' ? 'warning' : 'danger') }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $items->links() }}
    </div>
</x-app-layout>
