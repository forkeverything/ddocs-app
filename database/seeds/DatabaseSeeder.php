<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $this->command->info('---> Seeding Dev Data');
        $this->call(DevSeeder::class);
        $this->command->info('Finished seeding!');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        Model::reguard();
    }
}
