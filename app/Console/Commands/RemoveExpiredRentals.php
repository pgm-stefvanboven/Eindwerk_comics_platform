<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Collection;
use Illuminate\Support\Carbon;

class RemoveExpiredRentals extends Command
{
    protected $signature = 'rentals:remove-expired';
    protected $description = 'Remove expired rented comics from user collections';

    public function handle()
    {
        $expiredRentals = Collection::where('rental_end_date', '<=', Carbon::now())->get();

        foreach ($expiredRentals as $rental) {
            $rental->delete();
            $this->info('Removed rented comic ID: ' . $rental->comic_id);
        }
    }
}