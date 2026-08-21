<x-app-layout>
    <x-slot name="title">Laporan Stock Opname</x-slot>

    <!-- Export Buttons -->
    <div class="mb-3">
        <a class="btn btn-danger" href="{{ route('reports.export', ['opname', 'pdf']) }}">
            PDF
        </a>
        <a class="btn btn-success" href="{{ route('reports.export', ['opname', 'excel']) }}">
            Excel
        </a>
    </div>

    <!-- Table Section -->
    <div class="card border-0 shadow-sm table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Stok Awal</th>
                    <th>Masuk</th>
                    <th>Keluar</th>
                    <th>Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $row)
                    <tr>
                        <td>{{ $row['item']->nama_bahan }}</td>
                        <td>{{ $row['opening'] }}</td>
                        <td>{{ $row['incoming'] }}</td>
                        <td>{{ $row['outgoing'] }}</td>
                        <td>{{ $row['closing'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
