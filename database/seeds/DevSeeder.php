<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DevSeeder extends Seeder
{
    /**
     * List of table names that we'll be seeding to.
     * @var array
     */
    protected $tables = [
        'users',
        'checklists',
        'files'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables()
             ->seedMikeAccount()
             ->seedChecklists()
             ->seedFiles();
    }

    /**
     * Clear all tables ready to start fresh.
     *
     * @return $this
     */
    protected function truncateTables()
    {
        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }
        return $this;
    }

    /**
     * Create Mike's account for dev purposes.
     *
     * @return $this
     */
    protected function seedMikeAccount()
    {
        $this->user = User::create([
            'name' => 'Mike',
            'email' => 'mail@wumike.com',
            'password' => bcrypt('password')
        ]);

        return $this;
    }

    /**
     * Make checklists for Mike.
     *
     * @return $this
     */
    protected function seedChecklists()
    {

        // 1 checklist that's by mike to mike
        $checklist = factory(\App\Checklist::class)->create();
        \App\Recipient::create([
            'email' => 'mail@wumike.com',
            'checklist_id' => $checklist->id
        ]);

        // and 5 by mike - but to random emails
        $randomRecipientLists = factory(\App\Checklist::class, 5)->create(['user_id' => $this->user->id]);
        foreach($randomRecipientLists as $checklist) {
            factory(\App\Recipient::class, 5)->create([
                'checklist_id' => $checklist->id
            ]);
        }

        return $this;
    }

    /**
     * Seed files per Checklist
     *
     * @return $this
     */
    protected function seedFiles()
    {
        foreach ($this->user->checklists as $checklist) {
            factory(\App\FileRequest::class, mt_rand(0, 100))->create(['checklist_id' => $checklist->id]);
        }

        return $this;
    }
}
