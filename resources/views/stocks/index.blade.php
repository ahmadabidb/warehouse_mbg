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
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada transaksi.</td>
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
