<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CustomerCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup customer records';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = now()->subDays(30);
        $deletedCustomers = Customer::doesntHave('orders')
            ->select('name', 'id_number')
            ->whereDate('created_at', '<', $date)
            ->delete();

        $this->line("Deleted $deletedCustomers customers which had no orders registered and were older than $date.");

        if ($deletedCustomers > 0) {
            Log::info('Unused customer accounts deleted.', [
                'event.kind' => 'event',
                'event.outcome' => 'success',
                'count' => $deletedCustomers,
            ]);
        }

        return 0;
    }
}
