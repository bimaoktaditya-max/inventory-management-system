<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Stok - Inventory App</title>
    <nav style="margin: 20px 0;">
    <a href="{{ route('items.index') }}">Master Barang</a>
    |
    <a href="{{ route('transactions.index') }}">Transaksi Stok</a>
    |
    <a href="{{ route('stock-cards.index') }}">Kartu Stok</a>
</nav>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        h1, h2 {
            margin-top: 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        .full {
            grid-column: 1 / -1;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1d4ed8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .error {
            color: #dc2626;
            font-size: 14px;
            margin-top: 5px;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #2563eb;
            text-decoration: none;
        }

        @media (max-width: 700px) {
            body {
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <a href="{{ route('items.index') }}" class="back">
        ← Kembali ke Master Barang
    </a>

    <h1>Transaksi Stok</h1>
    <p>Catat aktivitas barang masuk dan barang keluar.</p>

    <div class="card">
        <h2>Formulir Transaksi</h2>

        @if (session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            <input
        type="hidden"
        name="idempotency_key"
        value="{{ (string) \Illuminate\Support\Str::uuid() }}"
    >

    <div class="form-grid">

            <div class="form-grid">

                <div>
                    <label for="type">Jenis Transaksi</label>
                    <select name="type" id="type" required>
                        <option value="">Pilih transaksi</option>
                        <option value="IN" {{ old('type') == 'IN' ? 'selected' : '' }}>
                            Barang Masuk
                        </option>
                        <option value="OUT" {{ old('type') == 'OUT' ? 'selected' : '' }}>
                            Barang Keluar
                        </option>
                    </select>
                </div>

                <div>
                    <label for="transaction_date">Tanggal Transaksi</label>
                    <input
                        type="date"
                        name="transaction_date"
                        id="transaction_date"
                        value="{{ old('transaction_date', date('Y-m-d')) }}"
                        required
                    >
                </div>

                <div>
                    <label for="item_id">Nama Barang</label>
                    <select name="item_id" id="item_id" required>
                        <option value="">Pilih barang</option>

                        @foreach ($items as $item)
                            <option
                                value="{{ $item->id }}"
                                {{ old('item_id') == $item->id ? 'selected' : '' }}
                            >
                                {{ $item->code }} - {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantity">Jumlah</label>
                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        min="1"
                        value="{{ old('quantity') }}"
                        required
                    >
                </div>

                <div>
                    <label for="reference">Supplier / Tujuan</label>
                    <input
                        type="text"
                        name="reference"
                        id="reference"
                        value="{{ old('reference') }}"
                        placeholder="Nama supplier atau tujuan"
                    >
                </div>

                <div>
                    <label for="notes">Catatan</label>
                    <input
                        type="text"
                        name="notes"
                        id="notes"
                        value="{{ old('notes') }}"
                        placeholder="Catatan tambahan"
                    >
                </div>

                <div class="full">
                    <button type="submit">
                        Simpan Transaksi
                    </button>
                </div>

            </div>
        </form>
    </div>

    <div class="card">
        <h2>Riwayat Transaksi</h2>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Supplier / Tujuan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date }}</td>
                        <td>{{ $transaction->item->code }}</td>
                        <td>{{ $transaction->item->name }}</td>
                        <td>
                            {{ $transaction->type == 'IN' ? 'Masuk' : 'Keluar' }}
                        </td>
                        <td>{{ $transaction->quantity }}</td>
                        <td>{{ $transaction->reference ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            Belum ada transaksi stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
</body>
</html>