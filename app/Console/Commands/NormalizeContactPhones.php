<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\PhoneNormalizer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('contacts:normalize-phones')]
#[Description('Normalize phone numbers of existing contacts')]
class NormalizeContactPhones extends Command
{
    public function handle(PhoneNormalizer $normalizer): int
    {
        $processed = 0;
        $updated = 0;
        $unsupported = 0;
        Contact::query()->select(['id', 'phone', 'phone_normalized'])->chunkById(500, function ($contacts) use ($normalizer, &$processed, &$updated, &$unsupported): void {
            foreach ($contacts as $contact) {
                $normalized = $normalizer->normalize($contact->phone);
                $processed++;
                if ($normalized === null) {
                    $unsupported++;
                }
                if ($contact->phone_normalized === $normalized) {
                    continue;
                }

                $contact->phone_normalized = $normalized;
                $contact->timestamps = false;
                $contact->saveQuietly();

                $updated++;
            }
        });
        $this->info("Processed: {$processed}");
        $this->info("Updated: {$updated}");
        $this->info("Empty or unsupported: {$unsupported}");

        return self::SUCCESS;
    }
}
