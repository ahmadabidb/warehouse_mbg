<x-app-layout>
    <x-slot name="title">
        {{ $type === 'incoming' ? 'Stok Masuk' : 'Stok Keluar' }}
    </x-slot>

    <!-- Action Button -->
    <div class="d-flex justify-content-end mb-3">
        <a 
            class="btn btn-primary" 
            href="{{ route($type === 'incoming' ? 'stok-masuk.create' : 'stok-keluar.create') }}"
        >
            + Catat Stok {{ $type === 'incoming' ? 'Masuk' : 'Keluar' }}
        </a>
    </div>

    <!-- Table Section -->
    <div class="card border-0 shadow-sm table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Tanggal</th>
                    <th>Bahan</th>
                    <th>Jumlah</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $r)
                    <tr>
                        <td>{{ $r->nomor_transaksi }}</td>
                        <td>
                            {{ ($type === 'incoming' ? $r->tanggal_masuk : $r->tanggal_keluar)->format('d/m/Y') }}
                        </td>
                        <td>{{ $r->bahanBaku->nama_bahan }}</td>
                        <td>{{ $r->jumlah }} {{ $r->bahanBaku->satuan }}</td>
                        <td>{{ $r->user->name }}</td>
                        <td>
                            @php
                                $isIncoming = $type === 'incoming';
                                $editRoute  = $isIncoming ? 'stok-masuk.edit' : 'stok-keluar.edit';
                                $destroyRoute = $isIncoming ? 'stok-masuk.destroy' : 'stok-keluar.destroy';
                            @endphp
                            @can($isIncoming ? 'stok_masuk.manage' : 'stok_keluar.manage')
                                <a
                                    class="btn btn-sm btn-outline-primary"
                                    href="{{ route($editRoute, $r) }}"
                                >
                                    Edit
                                </a>
                                <form
                                    class="d-inline"
                                    method="POST"
                                    action="{{ route($destroyRoute, $r) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus transaksi {{ $isIncoming ? 'stok masuk' : 'stok keluar' }} ini? Stok bahan baku akan dikoreksi otomatis.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $records->links() }}
    </div>
</x-app-layout>
