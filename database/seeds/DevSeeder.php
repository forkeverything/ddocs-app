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
        ->seedChecklistAndFiles();
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

    protected function seedChecklistAndFiles()
    {
        $checklist = factory(\App\Checklist::class)->create(['user_id' => $this->user->id]);
        factory(\App\File::class, 100)->create(['checklist_id' => $checklist->id]);
    }
}
