<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Список доступных пользователю компаний.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Company::class);
        $user = $request->user();
        $search = $request->string('search')->trim()->toString();
        $companies = Company::query()->visibleTo($user, 'companies.view')->withCount([
            'contacts' => fn ($query) => $query->visibleTo($user, 'contacts.view'),
            'deals' => fn ($query) => $query->visibleTo($user, 'deals.view')
        ])->when($search, function ($query) use ($search) {
        $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('legal_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
            });
        })->orderBy('name')->paginate(15)->withQueryString();
        return view('orvanta.companies.index', compact('companies', 'search'));
    }

    /**
     * Форма создания компании.
     */
    public function create()
    {
        $this->authorize('create', Company::class);
        return view('orvanta.companies.create');
    }

    /**
     * Сохранение новой компании.
     */
    public function store(StoreCompanyRequest $request)
    {
        $this->authorize('create', Company::class);
        $company = new Company($request->validated());
        $company->responsible()->associate($request->user());
        $company->save();
        return redirect()->route('companies.index')->with('success', 'Компания успешно создана.');
    }

    /**
     * Просмотр компании.
     */
    public function show(Request $request, Company $company)
    {
        $this->authorize('view', $company);
        $user = $request->user();
        $company->load([
            'contacts' => fn ($query) => $query->visibleTo($user, 'contacts.view'),
            'deals' => fn ($query) => $query->visibleTo($user, 'deals.view')
        ]);
        return view('orvanta.companies.show', compact('company'));
    }

    /**
     * Форма редактирования компании.
     */
    public function edit(Company $company)
    {
        $this->authorize('update', $company);
        return view('orvanta.companies.edit', compact('company'));
    }

    /**
     * Обновление компании.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $this->authorize('update', $company);
        $company->update($request->validated());
        return redirect()->route('companies.show', $company)->with('success', 'Данные компании успешно обновлены.');
    }

    /**
     * Удаление компании.
     */
    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Компания успешно удалена.');
    }
}
