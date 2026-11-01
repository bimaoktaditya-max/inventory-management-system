<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kartu Stok Bulanan - Inventory</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
            background: #f4f6f9;
            color: #222;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px #00000010;
        }
        nav { margin: 20px 0; }
        label { display: block; margin-bottom: 6px; }
        select, input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px 16px;
            background: #1769aa;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }
        .filter {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }
        .summary div {
            padding: 15px;
            background: #f0f3f7;
            border-radius: 6px;
        }
        .summary strong {
            display: block;
            font-size: 22px;
            margin-top: 8px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th { background: #f0f3f7; }
        .in { color: #16733b; }
        .out { color: #b42318; }
        @media (max-width: 700px) {
            .filter, .summary { grid-template-columns: 1fr 1fr; }
            .card { overflow-x: auto; }
        }
    </style>
</head>
<body>
    <h1>Kartu Stok Bulanan</h1>
    <p>Laporan pergerakan stok barang berdasarkan bulan.</p>

    <nav>
        <a href="{{ route('items.index') }}">Master Barang</a>
        |
        <a href="{{ route('transactions.index') }}">Transaksi Stok</a>
        |
        <a href="{{ route('stock-cards.index') }}">Kartu Stok</a>
    </nav>

    <div class="card">
        <h2>Filter Laporan</h2>

        <form method="GET" action="{{ route('stock-cards.index') }}">
            <div class="filter">
                <div>
                    <label for="item_id">Pilih Barang</label>
                    <select name="item_id" id="item_id" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}"
                                {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->code }} - {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="month">Bulan</label>
                    <input type="month" name="month" id="month"
                        value="{{ $month }}" required>
                </div>
            </div>

            <button type="submit">Tampilkan Laporan</button>
        </form>
    </div>

    @if ($selectedItem)
        <div class="card">
            <h2>{{ $selectedItem->code }} - {{ $selectedItem->name }}</h2>
            <p>Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}</p>

            <div class="summary">
                <div>
                    Saldo Awal
                    <strong>{{ $openingStock }} {{ $selectedItem->unit }}</strong>
                </div>
                <div>
                    Barang Masuk
                    <strong class="in">+{{ $totalIn }}</strong>
                </div>
                <div>
                    Barang Keluar
                    <strong class="out">-{{ $totalOut }}</strong>
                </div>
                <div>
                    Saldo Akhir
                    <strong>{{ $closingStock }} {{ $selectedItem->unit }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Riwayat Pergerakan Stok</h2>

            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Jumlah</th>
                        <th>Referensi</th>
                        <th>Catatan</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date }}</td>
                            <td>{{ $transaction->type }}</td>
                            <td>{{ $transaction->quantity }} {{ $selectedItem->unit }}</td>
                            <td>{{ $transaction->reference ?? '-' }}</td>
                            <td>{{ $transaction->notes ?? '-' }}</td>
                            <td>{{ $transaction->user?->name ?? 'Belum tersedia' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Tidak ada transaksi pada bulan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>