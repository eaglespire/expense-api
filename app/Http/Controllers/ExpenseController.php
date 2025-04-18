<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExpenseController extends Controller
{
    public function index(Request $request) {
        $user = $request->user();
        $cacheKey = "expenses_company_{$user->company_id}_page_" . $request->get('page', 1);

        $expenses = Cache::remember($cacheKey, now()->addMinutes(5), function() use ($user, $request) {
            return Expense::with('user')
                ->where('company_id', $user->company_id)
                ->when($request->search, function($query, $search) {
                    return $query->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                })
                ->paginate(10);
        });

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'amount'   => 'required|numeric',
            'category' => 'required|string|max:100',
        ]);

        // Get the logged-in user
        $user = $request->user();

        // Assign the expense to the user company and themselves
        $data['company_id'] = $user->company_id;
        $data['user_id']    = $user->id;

        // Create the expense
        $expense = Expense::create($data);
        return response()->json($expense, 201);
    }

    public function update(Request $request, $id)
    {

        $expense = Expense::findOrFail($id);
        $user = $request->user();

        // Verify that the expense belongs to the same company as the user
        if ($expense->company_id !== $user->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Check user role
        if (!in_array($user->role, ['Admin', 'Manager'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        $data = $request->validate([
            'title'    => 'sometimes|string|max:255',
            'amount'   => 'sometimes|numeric',
            'category' => 'sometimes|string|max:100',
        ]);


        $expense->update($data);
        return response()->json($expense, 200);
    }

    public function destroy(Request $request, $id)
    {

        $expense = Expense::findOrFail($id);
        $user = $request->user();


        if ($expense->company_id !== $user->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        if ($user->role !== 'Admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $expense->delete();

        return response()->json(['message' => 'Expense deleted'], 200);
    }

}
