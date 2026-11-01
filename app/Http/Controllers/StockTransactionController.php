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

        $transactions = StockTransaction::with('item', 'user')
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
        $rules = [
            'item_id' => ['required', 'exists:items,id'],
            'category' => ['required', 'in:NORMAL,CORRECTION,STOCK_OPNAME'],
            'transaction_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
            'idempotency_key' => ['required', 'uuid'],
        ];

        if ($request->category === 'STOCK_OPNAME') {
            $rules['physical_stock'] = ['required', 'integer', 'min:0'];
        } else {
            $rules['type'] = ['required', 'in:IN,OUT'];
            $rules['quantity'] = ['required', 'integer', 'min:1'];
        }

        $validated = $request->validate($rules);

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

            if ($validated['category'] === 'STOCK_OPNAME') {

                $physicalStock = (int) $validated['physical_stock'];

                $difference = $physicalStock - $currentStock;

                if ($difference === 0) {
                    return 'unchanged';
                }

                $validated['type'] = $difference > 0 ? 'IN' : 'OUT';
                $validated['quantity'] = abs($difference);

                $validated['notes'] = trim(
                    ($validated['notes'] ?? '')
                    . ' | Stock Opname. Stok sistem: '
                    . $currentStock
                    . ', stok fisik: '
                    . $physicalStock
                );

                unset($validated['physical_stock']);
            }

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

        if ($saved === 'unchanged') {
            return redirect()
                ->route('transactions.index')
                ->with('success', 'Stok fisik sama dengan stok sistem. Tidak ada penyesuaian.');
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }
}
