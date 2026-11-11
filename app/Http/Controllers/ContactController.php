<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Services\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function search(Request $request, PhoneNormalizer $normalizer)
    {
        $this->authorize('viewAny', Contact::class);
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
        ]);
        $normalizedPhone = $normalizer->normalize($validated['phone']);
        if ($normalizedPhone === null) {
            return response()->json([
                'supported' => false,
                'matches' => [],
            ]);
        }
        $user = $request->user();
        $contacts = Contact::query()->visibleTo($user, 'contacts.view')->where(function ($query) use ($user) {
            $query->whereNull('company_id')->orWhereHas('company', function ($companyQuery) use ($user) {
                $companyQuery->visibleTo($user, 'companies.view');
            });
        })->where('phone_normalized', $normalizedPhone)->with('company:id,name')->orderBy('last_name')->limit(10)->get([
            'id', 'company_id', 'last_name', 'first_name', 'middle_name', 'phone',
        ]);

        return response()->json(['supported' => true,
            'matches' => $contacts->map(fn (Contact $contact) => [
                'id' => $contact->id,
                'company_id' => $contact->company_id,
                'last_name' => $contact->last_name,
                'first_name' => $contact->first_name,
                'middle_name' => $contact->middle_name,
                'phone' => $contact->phone,
                'company' => $contact->company?->name, ])->values(),
        ]
        );
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Contact::class);
        $user = $request->user();
        $search = $request->string('search')->trim()->toString();
        $contacts = Contact::query()->visibleTo($user, 'contacts.view')->where(function ($query) use ($user) {
            $query->whereNull('company_id')->orWhereHas('company', function ($companyQuery) use ($user) {
                $companyQuery->visibleTo($user, 'companies.view');
            });
        })->with('company')->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('last_name', 'like', "%{$search}%")->orWhere('first_name', 'like', "%{$search}%")->orWhere('middle_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
            });
        })->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString();

        return view('orvanta.contacts.index', compact('contacts', 'search'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Contact::class);
        $companies = Company::query()->visibleTo($request->user(), 'companies.view')->orderBy('name')->get(['id', 'name']);

        return view('orvanta.contacts.create', compact('companies'));
    }

    public function store(StoreContactRequest $request)
    {
        $data = $request->validated();
        $this->ensureCompanyIsAccessible($request, $data['company_id'] ?? null);
        $contact = new Contact($data);
        $contact->responsible()->associate($request->user());
        $contact->save();

        return redirect()->route('contacts.index')->with('success', 'Контакт успешно создан.');
    }

    public function quickStore(StoreContactRequest $request)
    {
        $data = $request->validated();
        $this->ensureCompanyIsAccessible($request, $data['company_id'] ?? null);

        $contact = new Contact($data);
        $contact->responsible()->associate($request->user());
        $contact->save();

        return response()->json([
            'message' => 'Контакт успешно создан.',
            'contact' => [
                'id' => $contact->id,
                'company_id' => $contact->company_id,
                'last_name' => $contact->last_name,
                'first_name' => $contact->first_name,
                'middle_name' => $contact->middle_name,
                'phone' => $contact->phone,
                'company' => $contact->company?->name,
            ],
        ], 201);
    }

    public function show(Contact $contact, Request $request)
    {
        $this->authorize('view', $contact);
        $user = $request->user();
        $contact->load(['company', 'responsible']);
        $deals = $contact->deals()->visibleTo($user, 'deals.view')->with(['company', 'contact.company', 'responsible', 'pipeline', 'stage'])->get()->filter(fn (Deal $deal) => $user->can('view', $deal))->values();
        $contact->setRelation('deals', $deals);

        return view('orvanta.contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        $this->authorize('update', $contact);
        $user = request()->user();
        $companies = Company::query()->visibleTo($user, 'companies.view')->orderBy('name')->get(['id', 'name']);

        return view('orvanta.contacts.edit', compact('contact', 'companies'));
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $this->authorize('update', $contact);
        $data = $request->validated();
        $this->ensureCompanyIsAccessible($request, $data['company_id'] ?? null);
        $contact->update($data);

        return redirect()->route('contacts.show', $contact)->with('success', 'Данные контакта успешно обновлены.');
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Контакт успешно удалён.');
    }

    private function ensureCompanyIsAccessible(Request $request, mixed $companyId): void
    {
        if ($companyId === null) {
            return;
        }

        $company = Company::query()->find($companyId);
        if ($company === null || ! $request->user()->can('view', $company)) {
            throw ValidationException::withMessages(['company_id' => 'Выбранная компания недоступна.']);
        }
    }
}
