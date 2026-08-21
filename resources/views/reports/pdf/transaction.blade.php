<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            padding: 24px 28px 28px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: top;
            padding: 0;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin: 0;
        }
        .meta {
            margin: 8px 0 14px;
            font-size: 10px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th {
            background: #0f766e;
            color: #ffffff;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #0f766e;
        }
        td {
            padding: 7px 6px;
            border: 1px solid #e2e8f0;
        }
        tr:nth-child(even) td {
            background: #f8fafc;
        }
        .footer {
            margin-top: 12px;
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <table class="header-table">
        <tr>
            <td>
                <p class="title">{{ $title }}</p>
                <p class="subtitle">Sistem Informasi Gudang Dapur</p>
            </td>
            <td style="text-align: right; width: 120px;">
                <img src="{{ public_path('images/logo-bgn.png') }}" alt="Logo BGN" style="width: 100px; height: auto;">
            </td>
        </tr>
    </table>

    <div class="meta">
        <div><strong>Periode:</strong> {{ $period }}</div>
        <div><strong>Tanggal Cetak:</strong> {{ $generatedAt }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 38%;">Bahan</th>
                <th style="width: 12%;">Jumlah</th>
                <th style="width: 22%;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $record)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ ($type === 'incoming' ? $record->tanggal_masuk : $record->tanggal_keluar)->format('d/m/Y') }}</td>
                    <td>{{ $record->bahanBaku->nama_bahan }}</td>
                    <td>{{ $record->jumlah }}</td>
                    <td>{{ $record->user->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Dicetak otomatis oleh sistem gudang dapur BGN.</div>
</div>
</body>
</html>
