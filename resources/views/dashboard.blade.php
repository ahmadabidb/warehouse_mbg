<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="mb-4">
        <h3 class="mb-1">Selamat datang, {{ auth()->user()->name }}</h3>
        <p class="text-muted">Ringkasan persediaan Dapur Umum MBG Genengan</p>
    </div>

    <!-- Kartu Ringkasan Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total Bahan Baku</small>
                    <h4 class="text-primary mb-0 mt-2">{{ $totalItems }} <span style="font-size: 0.7em;">item</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Masuk Bulan Ini</small>
                    <h4 class="text-info mb-0 mt-2">{{ $incomingMonth }} <span style="font-size: 0.7em;">transaksi</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Keluar Bulan Ini</small>
                    <h4 class="text-warning mb-0 mt-2">{{ $outgoingMonth }} <span style="font-size: 0.7em;">transaksi</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Stok Menipis</small>
                    <h4 class="text-danger mb-0 mt-2">{{ $lowItems }} <span style="font-size: 0.7em;">item</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Stok Habis</small>
                    <h4 class="text-dark mb-0 mt-2">{{ $outItems }} <span style="font-size: 0.7em;">item</span></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Bahan Baku dengan Stok Tertinggi -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Top 5 Stok Terbanyak</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Bahan Baku</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topStocks as $item)
                                    <tr>
                                        <td>{{ $item->nama_bahan }}</td>
                                        <td><span class="badge bg-secondary">{{ $item->category->nama_kategori }}</span></td>
                                        <td class="text-end">
                                            <strong>{{ number_format($item->stok, 2) }}</strong> {{ $item->satuan }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada data bahan baku</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Stok per Kategori -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Stok per Kategori</h5>
                    <canvas id="doughnutChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Arus Stok -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Transaksi 12 Bulan Terakhir</h5>
                    <canvas id="lineChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Line Chart - Transaksi 12 Bulan
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [
                    {
                        label: 'Stok Masuk',
                        data: @json($incoming),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.3
                    },
                    {
                        label: 'Stok Keluar',
                        data: @json($outgoing),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Doughnut Chart - Stok per Kategori
        new Chart(document.getElementById('doughnutChart'), {
            type: 'doughnut',
            data: {
                labels: @json($categoryLabels),
                datasets: [
                    {
                        data: @json($categoryStocks),
                        backgroundColor: [
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d',
                            '#0dcaf0', '#fd7e14', '#d63384', '#20c997', '#6f42c1'
                        ]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>
