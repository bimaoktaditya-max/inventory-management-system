<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

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

        return view('items.index', compact('items'));
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
}