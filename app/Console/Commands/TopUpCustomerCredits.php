<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class TopUpCustomerCredits extends Command
{
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
    protected $description = 'Tops up customer balance to a fixed amount';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(private ?int $days = null)
    {
        $this->days = $days ?? setting()->get('customer.credit_top_up.days', 0);

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->days <= 0) {
            $this->warn("Customer top-up skipped, no time range defined.");
            Log::info('Customer balance top-up skipped, no time range defined.', [
                'event.kind' => 'event',
            ]);

            return 0;
        }

        $date = now()->subDays($this->days);
        $count = $this->topUp($date);

        $this->line("Topped up $count customers, time range {$this->days} days.");

        return 0;
    }

    private function topUp(Carbon $date): int
    {
        $currencies = Currency::all();

        return Customer::whereDate('topped_up_at', '<=', $date)
            ->orWhereNull('topped_up_at')
            ->with('currencies')
            ->get()
            ->reduce(fn (int $carry, Customer $customer) => $carry + (int) $this->topUpCustomer($customer, $currencies), 0);
    }

    private function topUpCustomer(Customer $customer, Collection $currencies): bool
    {
        $customer->initializeBalances($currencies);

        $customer->currencies->each(function (Currency $currency) use ($customer) {
            $value = max($customer->getBalance($currency), $currency->top_up_amount);
            $customer->setBalance($currency->id, $value);
        });

        if ($customer->wasChanged()) { // TODO: Check if wasChanged considers many-to-many relationships
            $customer->topped_up_at = now();
            $customer->save();

            Log::info('Customer balance topped up.', [
                'event.kind' => 'event',
                'event.outcome' => 'success',
                'customer.name' => $customer->name,
                'customer.id_number' => $customer->id_number,
                'customer.phone' => $customer->phone,
                'customer.balance' => $customer->totalBalance(),
            ]);

            return true;
        }

        return false;
    }
}
