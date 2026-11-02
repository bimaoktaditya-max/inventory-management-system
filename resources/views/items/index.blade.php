<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Master Barang - Inventory</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
            background: #f4f6f9;
            color: #222;
        }

        h1 { margin-bottom: 8px; }

        .card {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px #00000010;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 10px;
            padding: 10px 16px;
            background: #1769aa;
            color: white;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover { opacity: 0.85; }

        .btn-delete { background: #b42318; }

        .btn-cancel {
            display: inline-block;
            margin: 10px 0 0 10px;
            color: #555;
            text-decoration: none;
        }

        nav { margin: 20px 0; }

        nav a {
            color: #1769aa;
            text-decoration: none;
            margin-right: 5px;
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

        th { background: #f0f3f7; }

        .success { color: #16733b; }
        .error { color: #b42318; }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .safe {
            color: #16733b;
            background: #e8f5ed;
        }

        .low {
            color: #b42318;
            background: #fdecec;
        }

        .action-form {
            display: inline;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
            .card { overflow-x: auto; }
        }
    </style>
</head>

<body>

    <h1>Master Barang</h1>
    <p>Kelola data barang gudang.</p>

    <!-- Navigasi -->
    <nav>
        <a href="{{ route('items.index') }}">Master Barang</a> |
        <a href="{{ route('transactions.index') }}">Transaksi Stok</a> |
        <a href="{{ route('stock-cards.index') }}">Kartu Stok</a>
    </nav>

    <!-- Logout -->
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <!-- Notifikasi -->
    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <div class="error">
            <strong>Periksa kembali input berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Edit Barang -->
    @if (isset($editingItem) && $editingItem)
        <div class="card">
            <h2>Edit Barang</h2>

            <form action="{{ route('items.update', $editingItem) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="grid">
                    <div>
                        <label for="edit_code">Kode Barang</label>
                        <input
                            id="edit_code"
                            type="text"
                            name="code"
                            value="{{ old('code', $editingItem->code) }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="edit_name">Nama Barang</label>
                        <input
                            id="edit_name"
                            type="text"
                            name="name"
                            value="{{ old('name', $editingItem->name) }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="edit_unit">Satuan</label>
                        <input
                            id="edit_unit"
                            type="text"
                            name="unit"
                            value="{{ old('unit', $editingItem->unit) }}"
                            required
                        >
                    </div>

                    <div>
                        <label for="edit_min_stock">Stok Minimum</label>
                        <input
                            id="edit_min_stock"
                            type="number"
                            name="min_stock"
                            value="{{ old('min_stock', $editingItem->min_stock) }}"
                            min="0"
                            required
                        >
                    </div>
                </div>

                <button type="submit">Simpan Perubahan</button>

                <a class="btn-cancel"
                   href="{{ route('items.index') }}">
                    Batal
                </a>
            </form>
        </div>
    @endif

    <!-- Form Tambah Barang -->
    <div class="card">
        <h2>Tambah Barang</h2>

        <form action="{{ route('items.store') }}" method="POST">
            @csrf

            <div class="grid">
                <div>
                    <label for="code">Kode Barang</label>
                    <input
                        id="code"
                        name="code"
                        value="{{ old('code') }}"
                        required
                    >
                </div>

                <div>
                    <label for="name">Nama Barang</label>
                    <input
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div>
                    <label for="unit">Satuan</label>
                    <input
                        id="unit"
                        name="unit"
                        placeholder="Contoh: pcs"
                        value="{{ old('unit') }}"
                        required
                    >
                </div>

                <div>
                    <label for="min_stock">Stok Minimum</label>
                    <input
                        id="min_stock"
                        name="min_stock"
                        type="number"
                        min="0"
                        value="{{ old('min_stock', 0) }}"
                        required
                    >
                </div>
            </div>

            <button type="submit">Simpan Barang</button>
        </form>
    </div>

    <!-- Daftar Barang -->
    <div class="card">
        <h2>Daftar Barang</h2>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Stok Minimum</th>
                        <th>Stok Saat Ini</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->min_stock }}</td>
                            <td>{{ $item->current_stock }}</td>

                            <td>
                                @if ($item->current_stock <= $item->min_stock)
                                    <span class="status low">
                                        Stok Menipis
                                    </span>
                                @else
                                    <span class="status safe">
                                        Aman
                                    </span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('items.index', ['edit' => $item->id]) }}">
                                    <button type="button">Edit</button>
                                </a>

                                <form
                                    class="action-form"
                                    action="{{ route('items.destroy', $item) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus barang ini?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn-delete">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Belum ada barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
