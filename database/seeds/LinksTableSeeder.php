<?php

use Illuminate\Database\Seeder;

class LinksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $faker = app(\Faker\Generator::class);
        $factory = factory(\App\Models\Link::class);

        $data = $factory->times(6)->make();

        \App\Models\Link::query()->insert($data->toArray());
    }
}
