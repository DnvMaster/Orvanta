<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    public function index(Request $request): View
    {
        $pipelines = Pipeline::query()->where('is_active', true)->with('stages')->orderByDesc('is_default')->orderBy('name')->get();
        $requestedPipelineId = $request->integer('pipeline');
        $selectedPipeline = $requestedPipelineId > 0 ? $pipelines->firstWhere('id', $requestedPipelineId) : null;
        $selectedPipeline ??= $pipelines->firstWhere('is_default', true) ?? $pipelines->first();
        $deals = $selectedPipeline ? Deal::query()->where('pipeline_id', $selectedPipeline->id)->with('stage', 'responsible')->orderByDesc('created_at')->get() : collect();
        return view('orvanta.deals.index', ['pipelines' => $pipelines, 'selectedPipeline' => $selectedPipeline, 'deals' => $deals]);
    }

    public function create(Request $request): View
    {
        $pipelines = Pipeline::query()->where('is_active', true)->with('stages')->orderByDesc('is_default')->orderBy('name')->get();
        $requestedPipelineId = $request->integer('pipeline');
        $selectedPipeline = $requestedPipelineId > 0 ? $pipelines->firstWhere('id', $requestedPipelineId) : null;
        $selectedPipeline ??= $pipelines->firstWhere('is_default', true) ?? $pipelines->first();
        $companies = Company::query()->orderBy('name')->get();
        $contacts = Contact::query()->with('company')->orderBy('last_name')->orderBy('first_name')->get();
        return view('orvanta.deals.create', ['pipelines' => $pipelines,'selectedPipeline' => $selectedPipeline,'companies' => $companies,'contacts' => $contacts]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pipeline_id' => ['required','integer', Rule::exists('pipelines', 'id')->where('is_active', true)],
            'pipeline_stage_id' => ['required','integer', Rule::exists('pipeline_stages', 'id')->where('pipeline_id', $request->input('pipeline_id'))->where('type', 'open')],
            'company_id' => ['nullable','integer', Rule::exists('companies', 'id')],
            'contact_id' => ['nullable','integer', Rule::exists('contacts', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'currency' => ['required', 'string', Rule::in(['BYB', 'RUB', 'EUR', 'USD'])],
            'description' => ['nullable', 'string', 'max:5000']
        ]);
        $validated['user_id'] = $request->user()->id;
        Deal::create($validated);
        return redirect()->route('deals.index', ['pipeline' => $validated['pipeline_id']])->with('success', 'Сделка успешно создана.');
    }

    public function show(Deal $deal): View
    {
        $deal->load('pipeline','stage','responsible','company','contact');
        return view('orvanta.deals.show', ['deal' => $deal]);
    }

    public function edit(Deal $deal): View
    {
        $deal->load('pipeline','stage','responsible','company','contact');
        $pipelines = Pipeline::query()->where('is_active', true)->with('stages')->orderByDesc('is_default')->orderBy('name')->get();
        $stages = PipelineStage::query()->where('pipeline_id', $deal->pipeline_id)->orderBy('position')->get();
        $users = User::query()->orderBy('name')->get();
        $companies = Company::query()->orderBy('name')->get();
        $contacts = Contact::query()->with('company')->orderBy('last_name')->orderBy('first_name')->get();
        return view('orvanta.deals.edit', ['deal' => $deal,'pipelines' => $pipelines,'stages' => $stages,'users' => $users,'companies' => $companies,'contacts' => $contacts]);
    }

    public function update(Request $request, Deal $deal): RedirectResponse
    {
        $validated = $request->validate([
            'pipeline_id' => ['required','integer', Rule::exists('pipelines', 'id')->where('is_active', true)],
            'pipeline_stage_id' => ['required','integer', Rule::exists('pipeline_stages', 'id')->where('pipeline_id', $request->input('pipeline_id'))],
            'company_id' => ['nullable','integer', Rule::exists('companies', 'id')],
            'contact_id' => ['nullable','integer', Rule::exists('contacts', 'id')],
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'currency' => ['required', 'string', Rule::in(['BYB', 'RUB', 'EUR', 'USD'])],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
        $deal->update($validated);
        return redirect()->route('deals.show', $deal)->with('success', 'Изменения сделки сохранены.');
    }
}
