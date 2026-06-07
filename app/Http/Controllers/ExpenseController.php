<?php

namespace App\Http\Controllers;

use App\Models\MonthlyExpenseOverride;
use App\Models\RecurringExpense;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private FinanceService $finance) {}

    public function index()
    {
        $now = Carbon::now();
        $expenses = RecurringExpense::orderBy('category')->orderBy('name')->get();
        $totalMonthly = $expenses->sum(fn($e) => $e->getAmountForMonth($now->year, $now->month) ?? 0);

        return view('expenses.index', compact('expenses', 'totalMonthly', 'now'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:personnel,hebergement,infrastructure,marketing,locaux,autre',
            'amount' => 'required|numeric|min:0',
            'start_month' => 'required|date_format:Y-m',
            'end_month' => 'nullable|date_format:Y-m|after_or_equal:start_month',
            'notes' => 'nullable|string',
        ]);

        $data['start_month'] = $data['start_month'] . '-01';
        if (!empty($data['end_month'])) {
            $data['end_month'] = $data['end_month'] . '-01';
        }

        RecurringExpense::create($data);
        return redirect()->route('expenses.index')->with('success', 'Dépense récurrente créée.');
    }

    public function edit(RecurringExpense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, RecurringExpense $expense)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:personnel,hebergement,infrastructure,marketing,locaux,autre',
            'amount' => 'required|numeric|min:0',
            'start_month' => 'required|date_format:Y-m',
            'end_month' => 'nullable|date_format:Y-m|after_or_equal:start_month',
            'notes' => 'nullable|string',
        ]);

        $data['start_month'] = $data['start_month'] . '-01';
        if (!empty($data['end_month'])) {
            $data['end_month'] = $data['end_month'] . '-01';
        } else {
            $data['end_month'] = null;
        }

        $expense->update($data);
        return redirect()->route('expenses.index')->with('success', 'Dépense mise à jour.');
    }

    public function destroy(RecurringExpense $expense)
    {
        $expense->overrides()->delete();
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Dépense supprimée.');
    }

    public function override(int $year, int $month)
    {
        $expenses = RecurringExpense::all();
        $overrides = MonthlyExpenseOverride::where('year', $year)->where('month', $month)->get()->keyBy('recurring_expense_id');
        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return view('expenses.override', compact('expenses', 'overrides', 'year', 'month', 'monthName'));
    }

    public function storeOverride(Request $request, int $year, int $month)
    {
        $data = $request->validate([
            'overrides' => 'array',
            'overrides.*.enabled' => 'nullable|boolean',
            'overrides.*.amount' => 'nullable|numeric|min:0',
            'overrides.*.notes' => 'nullable|string',
        ]);

        $expenses = RecurringExpense::all();

        foreach ($expenses as $expense) {
            $enabled = !empty($request->input('overrides.' . $expense->id . '.enabled'));
            $amount = $request->input('overrides.' . $expense->id . '.amount');
            $notes = $request->input('overrides.' . $expense->id . '.notes');

            $isDefault = $expense->isActiveForMonth($year, $month);
            $defaultAmount = $expense->amount;

            // Only store override if it differs from default
            $amountValue = $enabled ? ($amount !== null && $amount !== '' ? (float) $amount : $defaultAmount) : null;

            MonthlyExpenseOverride::updateOrCreate(
                ['recurring_expense_id' => $expense->id, 'year' => $year, 'month' => $month],
                ['amount' => $amountValue, 'notes' => $notes]
            );
        }

        return redirect()->route('expenses.index')->with('success', 'Overrides du mois enregistrés.');
    }
}
