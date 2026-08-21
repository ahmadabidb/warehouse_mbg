<x-app-layout>
    <x-slot name="title">Laporan Stok {{ $type === 'incoming' ? 'Masuk' : 'Keluar' }}</x-slot>

    <!-- Filter Section -->
    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-3">
            <input 
                class="form-control" 
                type="date" 
                name="start_date" 
                value="{{ request('start_date') }}"
            >
        </div>
        <div class="col-md-3">
            <input 
                class="form-control" 
                type="date" 
                name="end_date" 
                value="{{ request('end_date') }}"
            >
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-secondary">Filter</button>
        </div>
        <div class="col-auto">
            <a 
                class="btn btn-danger" 
                href="{{ route('reports.export', [$type, 'pdf']) }}?{{ http_build_query(request()->query()) }}"
            >
                PDF
            </a>
            <a 
                class="btn btn-success" 
                href="{{ route('reports.export', [$type, 'excel']) }}?{{ http_build_query(request()->query()) }}"
            >
                Excel
            </a>
        </div>
    </form>

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
                @foreach($records as $r)
                    <tr>
                        <td>{{ $r->nomor_transaksi }}</td>
                        <td>{{ ($type === 'incoming' ? $r->tanggal_masuk : $r->tanggal_keluar)->format('d/m/Y') }}</td>
                        <td>{{ $r->bahanBaku->nama_bahan }}</td>
                        <td>{{ $r->jumlah }}</td>
                        <td>{{ $r->user->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
