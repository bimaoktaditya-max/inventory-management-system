<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockCardController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::orderBy('name')->get();

        $selectedItem = null;
        $month = $request->input('month', now()->format('Y-m'));

        if ($request->filled('item_id')) {
            $selectedItem = Item::findOrFail($request->item_id);
        }

        $openingStock = 0;
        $totalIn = 0;
        $totalOut = 0;
        $closingStock = 0;
        $transactions = collect();

        if ($selectedItem) {
            $startDate = Carbon::createFromFormat('Y-m', $month)
    ->startOfMonth()
    ->toDateString();

$endDate = Carbon::createFromFormat('Y-m', $month)
    ->endOfMonth()
    ->toDateString();
            $openingIn = StockTransaction::where('item_id', $selectedItem->id)
                ->where('type', 'IN')
                ->whereDate('transaction_date', '<', $startDate)
                ->sum('quantity');

            $openingOut = StockTransaction::where('item_id', $selectedItem->id)
                ->where('type', 'OUT')
                ->whereDate('transaction_date', '<', $startDate)
                ->sum('quantity');

            $openingStock = $openingIn - $openingOut;

            $totalIn = StockTransaction::where('item_id', $selectedItem->id)
    ->where('type', 'IN')
    ->whereDate('transaction_date', '>=', $startDate)
    ->whereDate('transaction_date', '<=', $endDate)
    ->sum('quantity');

    $totalOut = StockTransaction::where('item_id', $selectedItem->id)
    ->where('type', 'OUT')
    ->whereDate('transaction_date', '>=', $startDate)
    ->whereDate('transaction_date', '<=', $endDate)
    ->sum('quantity');

            $closingStock = $openingStock + $totalIn - $totalOut;

            $transactions = StockTransaction::with('user')
    ->where('item_id', $selectedItem->id)
    ->whereDate('transaction_date', '>=', $startDate)
    ->whereDate('transaction_date', '<=', $endDate)
    ->orderBy('transaction_date')
    ->orderBy('id')
    ->get();
        }

        return view('stock-cards.index', compact(
            'items',
            'selectedItem',
            'month',
            'openingStock',
            'totalIn',
            'totalOut',
            'closingStock',
            'transactions'
        ));
    }
}