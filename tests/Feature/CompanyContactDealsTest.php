<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Pipeline;
use App\Models\PipelineStage;
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
        $this->createDeal(['contact_id' => $otherContact->id]);
        $this->assertTrue($contact->deals->contains('id', $deal->id));
        $this->assertCount(1, $contact->deals);
    }

    public function test_contact_normalizes_phone_when_created(): void
    {
        $contact = Contact::create([
            'last_name' => 'Иванов',
            'first_name' => 'Иван',
            'phone' => '+7 999 123-45-67',
        ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'phone' => '+7 999 123-45-67',
            'phone_normalized' => '79991234567',
        ]);
    }

    public function test_contact_recalculates_normalized_phone_when_updated(): void
    {
        $contact = Contact::create([
            'last_name' => 'Иванов',
            'first_name' => 'Иван',
            'phone' => '+7 999 123-45-67',
        ]);

        $contact->update([
            'phone' => '+375 29 123-45-67',
        ]);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'phone' => '+375 29 123-45-67',
            'phone_normalized' => '375291234567',
        ]);
    }

    public function test_contact_has_null_normalized_phone_for_unsupported_number(): void
    {
        $contact = Contact::create([
            'last_name' => 'Иванов',
            'first_name' => 'Иван',
            'phone' => '+1 202 555 0123',
        ]);
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'phone' => '+1 202 555 0123',
            'phone_normalized' => null,
        ]);
    }

    private function createDeal(array $attributes = []): Deal
    {
        $pipeline = Pipeline::create(['name' => 'Test Pipeline']);
        $stage = PipelineStage::create([
            'pipeline_id' => $pipeline->id,
            'name' => 'Test Stage',
            'position' => 1,
        ]);

        return Deal::create(array_merge([
            'pipeline_id' => $pipeline->id,
            'pipeline_stage_id' => $stage->id,
            'title' => 'Test Deal',
            'amount' => 1000,
            'currency' => 'RUB',
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
