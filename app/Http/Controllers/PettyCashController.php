<?php

namespace App\Http\Controllers;

use App\PettyCash;
use App\PettyCashExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    public function index()
    {
        return view('accounting.petty_cash.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'opening_balance' => 'numeric|min:0',
        ]);

        $store_id = session('current_store_id', Auth::user()->store_id ?? 1);
        $date = $request->date;

        // Check if record already exists for this date and store
        $existing = PettyCash::where('date', $date)
            ->where('store_id', $store_id)
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Petty cash record already exists for this date'], 422);
        }

        // Get previous day's record to calculate carry over
        $previousDay = PettyCash::getPreviousDay($date, $store_id);
        $baseOpening = 0;

        // Add previous day's closing balance (if no debts)
        if ($previousDay) {
            if ($previousDay->debts > 0) {
                // If previous day had debts, they must be paid off first
                // The entered amount goes towards paying debts
                $debtPayment = min($request->opening_balance, $previousDay->debts);
                $remainingAmount = $request->opening_balance - $debtPayment;

                // Update previous day's debt
                $previousDay->debts -= $debtPayment;
                $previousDay->save();

                $baseOpening = $remainingAmount;
            } else {
                // No debts, carry over the closing balance
                $baseOpening = $previousDay->closing_balance + $request->opening_balance;
            }
        } else {
            // First day, just use entered amount
            $baseOpening = $request->opening_balance;
        }

        $pettyCash = PettyCash::create([
            'date' => $date,
            'opening_balance' => $baseOpening,
            'amount_received' => $request->opening_balance,
            'expenses_total' => 0,
            'closing_balance' => $baseOpening,
            'debts' => 0,
            'store_id' => $store_id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        return response()->json(['success' => 'Petty cash opening balance set successfully', 'data' => $pettyCash]);
    }

    public function addExpense(Request $request)
    {
        $request->validate([
            'petty_cash_id' => 'required|exists:petty_cash,id',
            'details' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $pettyCash = PettyCash::find($request->petty_cash_id);

        // Prevent adding expenses to past dates (only allow current day and future dates)
        $today = now()->toDateString();
        if ($pettyCash->date->toDateString() < $today) {
            return response()->json(['error' => 'Cannot add expenses to past dates. Expenses can only be added to current day or future dates.'], 422);
        }

        // Create expense
        PettyCashExpense::create([
            'petty_cash_id' => $request->petty_cash_id,
            'details' => $request->details,
            'amount' => $request->amount,
            'type' => 'expense',
            'created_by' => Auth::id()
        ]);

        // Update petty cash totals
        $pettyCash->expenses_total += $request->amount;
        $pettyCash->calculateClosingBalance();
        $pettyCash->updated_by = Auth::id();
        $pettyCash->save();

        return response()->json(['success' => 'Expense added successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'opening_balance' => 'sometimes|numeric|min:0',
            'expenses_total' => 'sometimes|numeric|min:0',
        ]);

        $pettyCash = PettyCash::findOrFail($id);
        $pettyCash->update($request->only(['opening_balance', 'expenses_total']));
        $pettyCash->calculateClosingBalance();
        $pettyCash->updated_by = Auth::id();
        $pettyCash->save();

        return response()->json(['success' => 'Petty cash updated successfully']);
    }

    public function destroy($id)
    {
        $pettyCash = PettyCash::findOrFail($id);
        $pettyCash->expenses()->delete(); // Delete related expenses
        $pettyCash->delete();

        return response()->json(['success' => 'Petty cash record deleted successfully']);
    }

    public function getExpenses($pettyCashId)
    {
        $expenses = PettyCashExpense::where('petty_cash_id', $pettyCashId)
            ->with('creator')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($expenses);
    }

    public function filterByDate(Request $request)
    {
        $store_id = session('current_store_id', Auth::user()->store_id ?? 1);

        $query = PettyCash::where('store_id', $store_id);

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $data = $records->map(function ($record) {
            return [
                'id' => $record->id,
                'date' => $record->date->format('Y-m-d'),
                'opening_balance' => $record->opening_balance,
                'amount_received' => $record->amount_received,
                'expenses_total' => $record->expenses_total,
                'closing_balance' => $record->closing_balance,
                'debts' => $record->debts
            ];
        });

        return response()->json($data);
    }

    public function getPreviousDayClosingBalance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $store_id = session('current_store_id', Auth::user()->store_id ?? 1);
        $date = $request->date;

        // Get previous day's record
        $previousDay = PettyCash::where('date', '<', $date)
            ->where('store_id', $store_id)
            ->orderBy('date', 'desc')
            ->first();

        if ($previousDay) {
            return response()->json([
                'closing_balance' => $previousDay->closing_balance,
                'date' => $previousDay->date->format('Y-m-d')
            ]);
        }

        return response()->json([
            'closing_balance' => 0,
            'date' => null
        ]);
    }
}
