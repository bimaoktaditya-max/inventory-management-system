<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Stok - Inventory App</title>

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

        nav {
            margin: 20px 0;
        }

        nav a {
            color: #2563eb;
            text-decoration: none;
            margin: 0 5px;
        }

        nav a:hover {
            text-decoration: underline;
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

        .logout-button {
            background: #dc2626;
        }

        .logout-button:hover {
            background: #b91c1c;
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

        .table-wrapper {
            overflow-x: auto;
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

    <nav>
        <a href="{{ route('items.index') }}">Master Barang</a> |
        <a href="{{ route('transactions.index') }}">Transaksi Stok</a> |
        <a href="{{ route('stock-cards.index') }}">Kartu Stok</a>
    </nav>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-button">Logout</button>
    </form>

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

            <div id="type-field">
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
                    <label for="category">Kategori Transaksi</label>
                    <select name="category" id="category" required>
                        <option value="">Pilih kategori</option>
                        <option value="NORMAL" {{ old('category', 'NORMAL') == 'NORMAL' ? 'selected' : '' }}>
                            Transaksi Normal
                        </option>
                        <option value="CORRECTION" {{ old('category') == 'CORRECTION' ? 'selected' : '' }}>
                            Koreksi Stok
                        </option>
                        <option value="STOCK_OPNAME" {{ old('category') == 'STOCK_OPNAME' ? 'selected' : '' }}>
                            Stock Opname
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

                <div id="quantity-field">
    <label for="quantity">Jumlah Transaksi</label>
    <input
        type="number"
        name="quantity"
        id="quantity"
        min="1"
        value="{{ old('quantity') }}"
    >
</div>

<div id="physical-stock-field" style="display: none;">
    <label for="physical_stock">Jumlah Stok Fisik</label>
    <input
        type="number"
        name="physical_stock"
        id="physical_stock"
        min="0"
        value="{{ old('physical_stock') }}"
    >
    <small>Masukkan hasil penghitungan fisik di gudang.</small>
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

                <div class="full">
                    <label for="notes">Catatan</label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="3"
                        placeholder="Catatan tambahan atau alasan koreksi stok"
                    >{{ old('notes') }}</textarea>
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

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Supplier / Tujuan</th>
                        <th>Catatan</th>
                        <th>Dicatat Oleh</th>
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
                            <td>
                                @switch($transaction->category)
                                    @case('NORMAL')
                                        Normal
                                        @break

                                    @case('CORRECTION')
                                        Koreksi Stok
                                        @break

                                    @case('STOCK_OPNAME')
                                        Stock Opname
                                        @break

                                    @default
                                        -
                                @endswitch
                            </td>
                            <td>{{ $transaction->quantity }}</td>
                            <td>{{ $transaction->reference ?? '-' }}</td>
                            <td>{{ $transaction->notes ?? '-' }}</td>
                            <td>{{ $transaction->user->name ?? 'Belum tersedia' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                Belum ada transaksi stok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    const categorySelect = document.getElementById('category');

    const typeField = document.getElementById('type-field');
    const typeInput = document.getElementById('type');

    const quantityField = document.getElementById('quantity-field');
    const quantityInput = document.getElementById('quantity');

    const physicalStockField = document.getElementById('physical-stock-field');
    const physicalStockInput = document.getElementById('physical_stock');

    function updateTransactionForm() {
        const isStockOpname = categorySelect.value === 'STOCK_OPNAME';

        typeField.style.display = isStockOpname ? 'none' : 'block';
        typeInput.required = !isStockOpname;
        typeInput.disabled = isStockOpname;

        quantityField.style.display = isStockOpname ? 'none' : 'block';
        quantityInput.required = !isStockOpname;
        quantityInput.disabled = isStockOpname;

        physicalStockField.style.display = isStockOpname ? 'block' : 'none';
        physicalStockInput.required = isStockOpname;
    }

    categorySelect.addEventListener('change', updateTransactionForm);

    updateTransactionForm();
</script>
</body>
</html>
