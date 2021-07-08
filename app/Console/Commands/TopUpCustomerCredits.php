<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TopUpCustomerCredits extends Command
{
    private int $count = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:topup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tops up customer credits to a fixed amount';

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
        $days = setting()->get('customer.credit_top_up.days');
        if ($days > 0) {
            $date = now()->subDays($days);
            $this->topUp($date);

            $this->line("Topped up {$this->count} customers.");
            return 0;
        }
        $this->warn("Customer top-up skipped, no time range defined.");
        Log::info('Customer credit topped skipped, no time range defined.', [
            'event.kind' => 'event',
        ]);
        return 0;
    }

    private function topUp(Carbon $date): void
    {
        $startingCredit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
        $amount = setting()->get('customer.credit_top_up.amount', $startingCredit);
        $maximum = setting()->get('customer.credit_top_up.maximum', $startingCredit);

        $this->count = 0;
        Customer::whereDate('topped_up_at', '<=', $date)
            ->orWhereNull('topped_up_at')
            ->get()
            ->each(function ($customer) use ($amount, $maximum) {
                $customer->credit = min($customer->credit + $amount, $maximum);
                if ($customer->isDirty('credit')) {
                    $customer->topped_up_at = now();
                    $customer->save();

                    Log::info('Customer credit topped up.', [
                        'event.kind' => 'event',
                        'event.outcome' => 'success',
                        'customer.name' => $customer->name,
                        'customer.id_number' => $customer->id_number,
                        'customer.phone' => $customer->phone,
                        'customer.credit' => $customer->credit,
                    ]);

                    $this->count++;
                }
            });
    }
}
