<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class TopUpCustmerCredits extends Command
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
        $days = setting()->get('customer.credit_topup.days');
        if ($days > 0) {
            $date = now()->subDays($days);

            $starting_credit = setting()->get('customer.starting_credit', config('shop.customer.starting_credit'));
            $amount = setting()->get('customer.credit_topup.amount', $starting_credit);
            $maximum = setting()->get('customer.credit_topup.maximum', $starting_credit);

            $this->count = 0;
            Customer::whereDate('topped_up_at', '<=', $date)->get()->each(function ($customer) use ($amount, $maximum) {
                $customer->credit = min($customer->credit + $amount, $maximum);
                if ($customer->isDirty('credit')) {
                    $customer->topped_up_at = now();
                    $customer->save();
                    $this->count++;
                }
            });

            $this->line("Topped up $this->count customers.");
        } else {
            $this->warn("Customer topup skipped, no time range defined.");
        }

        return 0;
    }
}
