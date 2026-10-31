<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->trim()->toString();
        $companies = Company::query()->withCount(['contacts', 'deals'])->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('legal_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
            });
        })->orderBy('name')->paginate(15)->withQueryString();
        return view('orvanta.companies.index', compact('companies', 'search'));
    }

    public function create()
    {
        return view('orvanta.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validated();
        Company::create($validated);
        return redirect()->route('companies.index')->with('success', 'Компания успешно создана.');
    }

    public function show(Company $company)
    {
        $company->load(['contacts', 'deals']);
        return view('orvanta.companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('orvanta.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $validated = $request->validated();
        $company->update($validated);
        return redirect()->route('companies.show', $company)->with('success', 'Данные компании успешно обновлены.');
    }

    public function destroy(Company $company)
    {
        //
    }
}
