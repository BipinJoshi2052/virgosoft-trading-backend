<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function ($user) {
            Asset::create([
                'user_id' => $user->id,
                'symbol' => 'BTC',
                'amount' => 1000,
                'locked_amount' => 0,
            ]);

            Asset::create([
                'user_id' => $user->id,
                'symbol' => 'ETH',
                'amount' => 1000,
                'locked_amount' => 0,
            ]);
        });
    }
}
