<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyContactDealsTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_returns_its_related_deals(): void
    {
        $company = Company::create(['name' => 'Orvanta Test Company']);
        $otherCompany = Company::create(['name' => 'Other Test Company']);
        $deal = $this->createDeal(['company_id' => $company->id]);
        $this->createDeal(['company_id' => $otherCompany->id]);
        $this->assertTrue($company->deals->contains('id', $deal->id));
        $this->assertCount(1, $company->deals);
    }

    public function test_contact_returns_its_related_deals(): void
    {
        $contact = Contact::create(['last_name' => 'Иванов', 'first_name' => 'Иван']);
        $otherContact = Contact::create(['last_name' => 'Петров', 'first_name' => 'Пётр']);
        $deal = $this->createDeal(['contact_id' => $contact->id]);
        $this->createDeal(['contact_id' => $otherContact->id,]);
        $this->assertTrue($contact->deals->contains('id', $deal->id));
        $this->assertCount(1, $contact->deals);
    }

   
    private function createDeal(array $attributes = []): Deal
    {
        $pipeline = \App\Models\Pipeline::create(['name' => 'Test Pipeline']);
        $stage = \App\Models\PipelineStage::create([
            'pipeline_id' => $pipeline->id,
            'name' => 'Test Stage',
            'position' => 1
        ]);

        return Deal::create(array_merge([
            'pipeline_id' => $pipeline->id,
            'pipeline_stage_id' => $stage->id,
            'title' => 'Test Deal',
            'amount' => 1000,
            'currency' => 'RUB'
        ], $attributes));
    }

    public function test_deal_returns_its_related_company(): void
    {
        $company = Company::create(['name' => 'Orvanta Test Company']);
        $deal = $this->createDeal(['company_id' => $company->id]);
        $this->assertTrue($deal->company->is($company));
    }

    public function test_deal_returns_its_related_contact(): void
    {
        $contact = Contact::create(['last_name' => 'Иванов', 'first_name' => 'Иван']);
        $deal = $this->createDeal(['contact_id' => $contact->id]);
        $this->assertTrue($deal->contact->is($contact));
    }
}
