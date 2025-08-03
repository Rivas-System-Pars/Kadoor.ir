<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UsersFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        try {
            DB::transaction(function () {
                $baseUrl = 'http://visitorykadoor.ir/send_customers_Visitory';
                $res = Http::withHeaders([
                    'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde',
                ])->asJson()->get($baseUrl);

                $firstJson = $res->json();
                $totalPages = $firstJson['pagination']['total_pages'] ?? 1;

                $allCustomers = collect();

                foreach (range(1, $totalPages) as $page) {
                    $response = Http::withHeaders([
                        'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde',
                    ])->asJson()->get("$baseUrl?page=$page");

                    $json = $response->json();
                    $customers = $json['customers'] ?? [];

                    if (!empty($customers)) {
                        $allCustomers = $allCustomers->merge($customers);
                    }
                }

                $customersWithMobile = $allCustomers->filter(function ($item) {
                    return !empty($item['FldMob']) && is_string($item['FldMob']);
                })->values();

                $count = 0;

                foreach ($customersWithMobile as $item) {
                    $created = User::updateOrCreate(
                        ['username' => $item['FldMob']],
                        [
                            'u_id' => $item['FldC_Ashkhas'],
                            'first_name' => $item['FldN_Ashkhas'],
                            'last_name' => $item['FldN_Ashkhas'],
                            'mobile' => $item['FldMob'],
                            'level' => 'user',
                            'password' => bcrypt($item['FldMob']),
                            'address_txt' => $item['FldAddress'] ?? null,
                        ]
                    );
                    $count++;
                }

                $this->info("✅ Synced {$count} users with mobile.");
            });
        } catch (\Throwable $e) {
            logger($e);
            $this->error('خطا در دریافت یا ذخیره کاربران: ' . $e->getMessage());
        }

        return 0;
    }
}
