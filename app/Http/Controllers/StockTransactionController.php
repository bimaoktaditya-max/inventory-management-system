<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransactionController extends Controller
{
    public function index()
    {
        $items = Item::orderBy('name')->get();

        $transactions = StockTransaction::with('item')
            ->latest('transaction_date')
            ->latest('id')
            ->get();

        return view('transactions.index', compact(
            'items',
            'transactions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'type' => ['required', 'in:IN,OUT'],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
            'idempotency_key' => ['required', 'uuid'],
        ]);

        $saved = DB::transaction(function () use ($validated) {

            $alreadySaved = StockTransaction::where(
                'idempotency_key',
                $validated['idempotency_key']
            )->exists();

            if ($alreadySaved) {
                return false;
            }

            $item = Item::where('id', $validated['item_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $stockIn = StockTransaction::where('item_id', $item->id)
                ->where('type', 'IN')
                ->sum('quantity');

            $stockOut = StockTransaction::where('item_id', $item->id)
                ->where('type', 'OUT')
                ->sum('quantity');

            $currentStock = $stockIn - $stockOut;

            if (
                $validated['type'] === 'OUT' &&
                $validated['quantity'] > $currentStock
            ) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi. Stok tersedia: '
                        . $currentStock,
                ]);
            }

            $validated['user_id'] = auth()->id();

            StockTransaction::create($validated);

            return true;
        });

        if ($saved === false) {
            return redirect()
                ->route('transactions.index')
                ->with('success', 'Transaksi ini sudah pernah diproses.');
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }
}