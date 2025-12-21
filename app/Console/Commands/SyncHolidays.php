<?php

namespace App\Console\Commands;

use App\Services\HolidayService;
use Illuminate\Console\Command;

class SyncHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holidays:sync {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync public holidays from Nager.Date API';

    /**
     * Execute the console command.
     */
    public function handle(HolidayService $holidayService): int
    {
        $year = $this->argument('year');
        
        if ($year) {
            // Sync specific year
            $this->info("Syncing holidays for {$year}...");
            $count = $holidayService->syncHolidays((int) $year);
            $this->info("✓ Synced {$count} holidays for {$year}");
        } else {
            // Sync current year and next year
            $currentYear = now()->year;
            $nextYear = $currentYear + 1;
            
            $this->info("Syncing holidays for {$currentYear} and {$nextYear}...");
            
            $count1 = $holidayService->syncHolidays($currentYear);
            $this->info("✓ Synced {$count1} holidays for {$currentYear}");
            
            $count2 = $holidayService->syncHolidays($nextYear);
            $this->info("✓ Synced {$count2} holidays for {$nextYear}");
            
            $this->newLine();
            $this->info("Total: " . ($count1 + $count2) . " holidays synced.");
        }
        
        return Command::SUCCESS;
    }
}
