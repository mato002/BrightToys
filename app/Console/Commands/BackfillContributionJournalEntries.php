<?php

namespace App\Console\Commands;

use App\Models\PartnerContribution;
use App\Services\AccountingRuleService;
use Illuminate\Console\Command;

class BackfillContributionJournalEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:backfill-contributions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill journal entries for approved contributions that were approved before the accounting integration was added';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backfill of contribution journal entries...');

        // Get all approved contributions
        $contributions = PartnerContribution::where('status', 'approved')
            ->get();

        $this->info("Found {$contributions->count()} approved contributions to process.");

        $accountingService = new AccountingRuleService();
        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($contributions as $contribution) {
            try {
                // This will check if entry exists and create if not
                $journalEntry = $accountingService->postContribution($contribution);
                
                if ($journalEntry) {
                    $processed++;
                    $this->line("✓ Processed contribution #{$contribution->id} - Created journal entry #{$journalEntry->id}");
                } else {
                    $skipped++;
                    $this->line("⊘ Skipped contribution #{$contribution->id} - Journal entry already exists");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("✗ Error processing contribution #{$contribution->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Backfill complete!");
        $this->info("Processed: {$processed}");
        $this->info("Skipped (already exists): {$skipped}");
        $this->info("Errors: {$errors}");

        return Command::SUCCESS;
    }
}
