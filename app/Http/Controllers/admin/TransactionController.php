<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\VendorCommissions;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['vendor', 'product'])->orderBy('created_at', 'desc');

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $transactions = $query->paginate(20);
        $vendors = VendorCommissions::select('id', 'name')->get();

        return view('admin.transactions.index', compact('transactions', 'vendors'));
    }

    public function settle($id)
    {
        $transaction = Transaction::with('vendor')->findOrFail($id);

        if ($transaction->vendor->category_code !== 'internal') {
            return back()->with('error', 'Only internal vendors require manual settlement.');
        }

        $transaction->status = 'settled';
        $transaction->save();

        return back()->with('success', "Settlement marked as complete for transaction ID #{$transaction->id}");
    }


    public function refund($id)
    {
        $transaction = Transaction::findOrFail($id);

        if ($transaction->status === 'refunded') {
            return back()->with('error', 'Transaction already refunded.');
        }

        $transaction->status = 'refunded';
        $transaction->save();

        // TODO: integrate gateway refund API call here

        return back()->with('success', "Refund processed for transaction #{$transaction->id}");
    }
}
