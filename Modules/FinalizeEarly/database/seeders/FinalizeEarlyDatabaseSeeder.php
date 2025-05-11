<?php

namespace Modules\FinalizeEarly\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class FinalizeEarlyDatabaseSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");

    }
}
