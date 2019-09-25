<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        \Illuminate\Database\Eloquent\Model::unguard();
        $this->call(UsersTableSeeder::class);
		$this->call(ReplysTableSeeder::class);
		$this->call(TopicsTableSeeder::class);
        $this->call(ReplysTableSeeder::class);
        \Illuminate\Database\Eloquent\Model::reguard();
    }
}
