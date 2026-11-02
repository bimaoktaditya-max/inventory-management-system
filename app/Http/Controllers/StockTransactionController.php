<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransactionController extends Controller
{
    /**
     * Menampilkan formulir dan riwayat transaksi.
     */
    public function index()
    {
        $items = Item::orderBy('name')->get();

        $transactions = StockTransaction::with('item', 'user')
            ->latest('transaction_date')
            ->latest('id')
            ->get();

        return view('transactions.index', compact(
            'items',
            'transactions'
        ));
    }

    /**
     * Menyimpan transaksi barang masuk, keluar, atau stock opname.
     */
    public function store(Request $request)
    {
        // Validasi dasar transaksi.
        $rules = [
            'item_id' => ['required', 'exists:items,id'],
            'category' => [
                'required',
                'in:NORMAL,CORRECTION,STOCK_OPNAME'
            ],
            'transaction_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
            'idempotency_key' => ['required', 'uuid'],
        ];

        // Stock Opname menggunakan jumlah stok fisik.
        if ($request->category === 'STOCK_OPNAME') {
            $rules['physical_stock'] = [
                'required',
                'integer',
                'min:0'
            ];
        } else {
            // Transaksi normal dan koreksi menggunakan jenis dan jumlah.
            $rules['type'] = ['required', 'in:IN,OUT'];
            $rules['quantity'] = [
                'required',
                'integer',
                'min:1'
            ];
        }

        $validated = $request->validate($rules);

        // Proses penyimpanan dalam database transaction.
        $saved = DB::transaction(function () use ($validated) {

            // Cegah transaksi yang sama disimpan dua kali.
            $alreadySaved = StockTransaction::where(
                'idempotency_key',
                $validated['idempotency_key']
            )->exists();

            if ($alreadySaved) {
                return false;
            }

            // Ambil data barang.
            $item = Item::where('id', $validated['item_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Hitung total barang masuk.
            $stockIn = StockTransaction::where(
                'item_id',
                $item->id
            )
                ->where('type', 'IN')
                ->sum('quantity');

            // Hitung total barang keluar.
            $stockOut = StockTransaction::where(
                'item_id',
                $item->id
            )
                ->where('type', 'OUT')
                ->sum('quantity');

            // Hitung stok yang tersedia.
            $currentStock = $stockIn - $stockOut;

            // Proses Stock Opname.
            if ($validated['category'] === 'STOCK_OPNAME') {

                $physicalStock = (int) $validated['physical_stock'];

                // Selisih stok fisik dengan stok sistem.
                $difference = $physicalStock - $currentStock;

                // Jika sama, tidak perlu membuat transaksi.
                if ($difference === 0) {
                    return 'unchanged';
                }

                // Tentukan jenis transaksi berdasarkan selisih.
                $validated['type'] = $difference > 0 ? 'IN' : 'OUT';

                // Jumlah transaksi menggunakan nilai absolut selisih.
                $validated['quantity'] = abs($difference);

                // Buat catatan Stock Opname.
                $originalNotes = trim($validated['notes'] ?? '');

                $opnameNotes = 'Stock Opname. Stok sistem: '
                    . $currentStock
                    . ', stok fisik: '
                    . $physicalStock;

                $validated['notes'] = $originalNotes !== ''
                    ? $originalNotes . ' | ' . $opnameNotes
                    : $opnameNotes;

                // Hapus field khusus Stock Opname sebelum disimpan.
                unset($validated['physical_stock']);
            }

            // Pastikan stok tidak menjadi negatif.
            if (
                $validated['type'] === 'OUT' &&
                $validated['quantity'] > $currentStock
            ) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi. Stok tersedia: '
                        . $currentStock,
                ]);
            }

            // Catat user yang melakukan transaksi.
            $validated['user_id'] = auth()->id();

            // Simpan transaksi baru.
            StockTransaction::create($validated);

            return true;
        });

        // Respons jika transaksi sudah pernah disimpan.
        if ($saved === false) {
            return redirect()
                ->route('transactions.index')
                ->with(
                    'success',
                    'Transaksi ini sudah pernah diproses.'
                );
        }

        // Respons jika stok fisik sama dengan stok sistem.
        if ($saved === 'unchanged') {
            return redirect()
                ->route('transactions.index')
                ->with(
                    'success',
                    'Stok fisik sama dengan stok sistem. Tidak ada penyesuaian.'
                );
        }

        // Respons jika transaksi berhasil disimpan.
        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }
}
