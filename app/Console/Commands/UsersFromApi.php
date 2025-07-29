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
			DB::transaction(function (){
				$users = collect([]);
				$response = Http::withHeaders([
					'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde',
				])->asForm()->post('https://webcomapi.ir/api/Store/GetUsers', [
					'Page' => 1,
				])->json();
				$users->push($response['users']);
				if (array_key_exists('countUsers', $response) && $response['countUsers'] > 100) {
					foreach (range(2, ceil($response['countUsers'] / 100)) as $i) {
						$users->push(Http::withHeaders([
							'apiKey' => '7a2a1be2*d422*4d70*8b61*affdde',
						])->asForm()->post('https://webcomapi.ir/api/Store/GetUsers', [
							'Page' => $i,
						])->json()['users']);
					}
				}
				$users = $users->flatten(1)->whereNotIn('fldMob',User::query()->whereNotNull('mobile')->pluck('mobile')->filter()->toArray());
				if($users->count()){
					foreach ($users as $userItem) {
						DB::table('users')->updateOrInsert([
							'mobile' => $userItem['fldMob'],
						], [
							'u_id' => $userItem['fldId'],
							'first_name' => $userItem['fldName'],
							'last_name' => $userItem['fldName'],
							'mobile' => $userItem['fldMob'],
							'username' => $userItem['fldMob'],
							'level' => 'user',
							'password' => bcrypt($userItem['fldMob']),
						]);
					}
				}
			});
        } catch (\Throwable $e) {
            logger($e);
        }
        return 0;
    }
}
