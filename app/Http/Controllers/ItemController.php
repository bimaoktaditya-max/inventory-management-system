<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::orderBy('name')->get();

        foreach ($items as $item) {
            $stockIn = StockTransaction::where('item_id', $item->id)
                ->where('type', 'IN')
                ->sum('quantity');

            $stockOut = StockTransaction::where('item_id', $item->id)
                ->where('type', 'OUT')
                ->sum('quantity');

            $item->current_stock = $stockIn - $stockOut;
        }

        $editingItem = null;

if (request()->filled('edit')) {
    $editingItem = Item::findOrFail(request('edit'));
}

return view('items.index', compact('items', 'editingItem'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:items,code',
            ],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ]);

        Item::create($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

        public function update(Request $request, Item $item)
    {
        $hasTransactions = StockTransaction::where('item_id', $item->id)->exists();

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'code')->ignore($item->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ]);

        if ($hasTransactions && $validated['code'] !== $item->code) {
            return back()->withErrors([
                'code' => 'Kode barang tidak dapat diubah karena sudah memiliki riwayat transaksi.',
            ]);
        }

        $item->update($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $hasTransactions = StockTransaction::where('item_id', $item->id)->exists();

        if ($hasTransactions) {
            return redirect()
                ->route('items.index')
                ->with('error', 'Barang tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}