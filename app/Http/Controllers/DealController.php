<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    /**
     * Список доступных пользователю сделок.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Deal::class);
        $user = $request->user();
        $pipelines = Pipeline::query()->where('is_active', true)->with('stages')->orderByDesc('is_default')->orderBy('name')->get();
        $requestedPipelineId = $request->integer('pipeline');
        $selectedPipeline = $requestedPipelineId > 0 ? $pipelines->firstWhere('id', $requestedPipelineId) : null;
        $selectedPipeline ??= $pipelines->firstWhere('is_default', true) ?? $pipelines->first();
        $deals = $selectedPipeline ? Deal::query()->visibleTo($user, 'deals.view')->where('pipeline_id', $selectedPipeline->id)->where(function ($query) use ($user) {
            $query->whereNull('company_id')->orWhereHas('company', fn ($companyQuery) => $companyQuery->visibleTo($user, 'companies.view'));
        })->where(function ($query) use ($user) {
            $query->whereNull('contact_id')->orWhereHas('contact', function ($contactQuery) use ($user) {
                $contactQuery->visibleTo($user, 'contacts.view')->where(function ($query) use ($user) {
                    $query->whereNull('company_id')->orWhereHas('company', fn ($companyQuery) => $companyQuery->visibleTo($user, 'companies.view'));
                });
            });
        })->with('stage', 'responsible')->orderByDesc('created_at')->get() : collect();
        return view('orvanta.deals.index', ['pipelines' => $pipelines,'selectedPipeline' => $selectedPipeline,'deals' => $deals]);
    }

    /**
     * Форма создания сделки.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Deal::class);
        $user = $request->user();
        $pipelines = Pipeline::query()->where('is_active', true)->with('stages')->orderByDesc('is_default')->orderBy('name')->get();
        $requestedPipelineId = $request->integer('pipeline');
        $selectedPipeline = $requestedPipelineId > 0 ? $pipelines->firstWhere('id', $requestedPipelineId) : null;
        $selectedPipeline ??= $pipelines->firstWhere('is_default', true) ?? $pipelines->first();
        $companies = Company::query()->visibleTo($user, 'companies.view')->orderBy('name')->get();
        $contacts = Contact::query()->visibleTo($user, 'contacts.view')->where(function ($query) use ($user) {
            $query->whereNull('company_id')->orWhereHas('company', fn ($companyQuery) => $companyQuery->visibleTo($user, 'companies.view'));
        })->with('company')->orderBy('last_name')->orderBy('first_name')->get();
        return view('orvanta.deals.create', ['pipelines' => $pipelines,'selectedPipeline' => $selectedPipeline,'companies' => $companies,'contacts' => $contacts]);
    }

    /**
     * Сохранение новой сделки.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Deal::class);
        $validated = $request->validate(['pipeline_id' => ['required','integer', Rule::exists('pipelines', 'id')->where('is_active', true)],
            'pipeline_stage_id' => ['required','integer', Rule::exists('pipeline_stages', 'id')->where('pipeline_id', $request->input('pipeline_id'))->where('type', 'open')],
            'company_id' => ['nullable','integer', Rule::exists('companies', 'id')],
            'contact_id' => ['nullable','integer', Rule::exists('contacts', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'currency' => ['required', 'string', Rule::in(['BYB', 'RUB', 'EUR', 'USD'])],
            'description' => ['nullable', 'string', 'max:5000']
        ]);
        $this->ensureRelatedRecordsAccessible($request, $validated);
        $deal = new Deal($validated);
        $deal->responsible()->associate($request->user());
        $deal->save();
        return redirect()->route('deals.index', ['pipeline' => $validated['pipeline_id']])->with('success', 'Сделка успешно создана.');
    }

    /**
     * Просмотр сделки.
     */
    public function show(Request $request, Deal $deal): View
    {
        $this->authorize('view', $deal);
        $deal->load('pipeline', 'stage', 'responsible', 'company', 'contact');
        return view('orvanta.deals.show', ['deal' => $deal]);
    }

    /**
     * Форма редактирования сделки.
     */
    public function edit(Request $request, Deal $deal): View
    {
        $this->authorize('update', $deal);
        $user = $request->user();
        $deal->load('pipeline', 'stage', 'responsible', 'company', 'contact');
        $pipelines = Pipeline::query()->where('is_active', true)->with('stages')->orderByDesc('is_default')->orderBy('name')->get();
        $stages = PipelineStage::query()->where('pipeline_id', $deal->pipeline_id)->orderBy('position')->get();
        $companies = Company::query()->visibleTo($user, 'companies.view')->orderBy('name')->get();
        $contacts = Contact::query()->visibleTo($user, 'contacts.view')->where(function ($query) use ($user) {
            $query->whereNull('company_id')->orWhereHas('company', fn ($companyQuery) => $companyQuery->visibleTo($user, 'companies.view'));
        })->with('company')->orderBy('last_name')->orderBy('first_name')->get();
        return view('orvanta.deals.edit', ['deal' => $deal,'pipelines' => $pipelines,'stages' => $stages,'companies' => $companies,'contacts' => $contacts]);
    }

    /**
     * Обновление сделки.
     */
    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $this->authorize('update', $deal);
        $validated = $request->validate(['pipeline_id' => ['required','integer', Rule::exists('pipelines', 'id')->where('is_active', true)],
            'pipeline_stage_id' => ['required','integer', Rule::exists('pipeline_stages', 'id')->where('pipeline_id', $request->input('pipeline_id'))],
            'company_id' => ['nullable','integer', Rule::exists('companies', 'id')],
            'contact_id' => ['nullable','integer', Rule::exists('contacts', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'currency' => ['required', 'string', Rule::in(['BYB', 'RUB', 'EUR', 'USD'])],
            'description' => ['nullable', 'string', 'max:5000']
        ]);
        $this->ensureRelatedRecordsAccessible($request, $validated);
        $deal->update($validated);
        return redirect()->route('deals.show', $deal)->with('success', 'Изменения сделки сохранены.');
    }

    /**
     * Проверяет доступ к выбранным компании и контакту.
     */
    private function ensureRelatedRecordsAccessible(Request $request, array $validated): void
    {
        $user = $request->user();
        $company = null;
        if (!empty($validated['company_id'])) {
            $company = Company::query()->findOrFail($validated['company_id']);
            abort_unless($user->can('view', $company), 403);
        }
        if (!empty($validated['contact_id'])) {
            $contact = Contact::query()->with('company')->findOrFail($validated['contact_id']);
            abort_unless($user->can('view', $contact), 403);
            if ($contact->company_id !== null && (int) $contact->company_id !== (int) ($company?->id ?? 0)) {
                abort(422, 'Выбранный контакт не принадлежит указанной компании.');
            }
        }
    }
}