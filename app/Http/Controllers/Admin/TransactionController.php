<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller {
    public function index(Request $request) {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Transaction::query()->latest();

        if ($status && in_array($status, ['completed', 'pending', 'refunded', 'failed'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->search($search);
        }

        $transactions = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_volume'    => Transaction::where('status', 'completed')->sum('amount'),
            'total_count'     => Transaction::count(),
            'completed_count' => Transaction::where('status', 'completed')->count(),
            'pending_count'   => Transaction::where('status', 'pending')->count(),
            'refunded_count'  => Transaction::where('status', 'refunded')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'metrics', 'status', 'search'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'customer_name'   => 'required|string|max:150',
            'customer_email'  => 'nullable|email|max:150',
            'amount'          => 'required|numeric|min:0.01|max:999999.99',
            'currency'        => 'nullable|string|size:3',
            'payment_method'  => 'required|string|max:50',
            'status'          => 'required|in:completed,pending,refunded,failed',
            'description'     => 'nullable|string|max:255',
        ]);

        $reference = 'TRX-' . strtoupper(now()->format('ymd')) . '-' . strtoupper(Str::random(6));

        $transaction = Transaction::create([
            'reference'      => $reference,
            'customer_name'  => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'amount'         => $validated['amount'],
            'currency'       => strtoupper($validated['currency'] ?? 'USD'),
            'payment_method' => $validated['payment_method'],
            'status'         => $validated['status'],
            'description'    => $validated['description'] ?? 'Editorial patron subscription',
        ]);

        ActivityLog::record('created', "Recorded financial transaction {$transaction->reference} ({$transaction->formatted_amount})", $transaction);

        return back()->with('success', "Transaction {$transaction->reference} recorded successfully.");
    }

    public function update(Request $request, Transaction $transaction) {
        $validated = $request->validate([
            'status'      => 'required|in:completed,pending,refunded,failed',
            'description' => 'nullable|string|max:255',
        ]);

        $oldStatus = $transaction->status;
        $transaction->update($validated);

        ActivityLog::record('updated', "Updated transaction {$transaction->reference} status from {$oldStatus} to {$transaction->status}", $transaction);

        return back()->with('success', "Transaction {$transaction->reference} updated successfully.");
    }

    public function destroy(Transaction $transaction) {
        $ref = $transaction->reference;
        $transaction->delete();

        ActivityLog::record('deleted', "Deleted transaction record {$ref}", null);

        return back()->with('success', "Transaction record {$ref} deleted.");
    }
}
