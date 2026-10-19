<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('order')->orderBy('bank_name')->get();

        return view('admin.bank-accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        return view('admin.bank-accounts.form', ['bankAccount' => new BankAccount]);
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.form', compact('bankAccount'));
    }

    public function store(Request $request)
    {
        return $this->save($request, new BankAccount);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        return $this->save($request, $bankAccount);
    }

    protected function save(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_type' => 'nullable|string|max:50',
            'account_number' => 'required|string|max:60',
            'holder_name' => 'required|string|max:120',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $bankAccount->fill($data);
        $bankAccount->is_active = $request->boolean('is_active');
        $bankAccount->save();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Cuenta bancaria guardada correctamente.');
    }

    public function confirmDelete(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.confirm-delete', compact('bankAccount'));
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Se eliminó la cuenta bancaria.');
    }
}
