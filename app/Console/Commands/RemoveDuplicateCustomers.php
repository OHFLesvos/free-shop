<?php

namespace App\Console\Commands;

use App\Models\Customer;
use Illuminate\Console\Command;

class RemoveDuplicateCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customers:remove_duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes duplicate customer accounts';

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
        $duplicatedIdNumbers = Customer::select('id_number', 'name')
            ->selectRaw('COUNT(id_number) as cnt')
            ->groupBy('id_number', 'name')
            ->havingRaw('COUNT(id_number) > 1')
            ->get();

        $this->table(
            ['ID Number', 'Name', 'Count'],
            $duplicatedIdNumbers->toArray()
        );
        foreach ($duplicatedIdNumbers->pluck('id_number') as $idNumber) {
            $customers = Customer::where('id_number', $idNumber)
                ->orderBy('created_at', 'asc')
                ->get();
            $master = $customers->pop();
            foreach ($customers as $customer) {
                $customer->orders()->update(['customer_id' => $master->id]);
                $customer->delete();
            }
        }

        $remainingDuplicatedIdNumbers = Customer::select('id_number')
            ->selectRaw('COUNT(id_number) as cnt')
            ->groupBy('id_number')
            ->havingRaw('COUNT(id_number) > 1')
            ->get();

        $this->table(
            ['ID Number', 'Count'],
            $remainingDuplicatedIdNumbers->toArray()
        );

        return 0;
    }
}
