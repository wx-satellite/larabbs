<?php

use Illuminate\Database\Seeder;
use App\Models\Topic;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = app(\Faker\Generator::class);

        // 用户id数组
        $uids = \App\Models\User::query()->get()->pluck("id")->toArray();

        // 分类数组
        $cids = \App\Models\Category::query()->get()->pluck("id")->toArray();


        // Topic模型实例的Collection集合
        $topics = factory(Topic::class)->times(100)->make()->each(function($topic,$index)use($faker,$uids,$cids){
            $topic->user_id = $faker->randomElement($uids);
            $topic->category_id = $faker->randomElement($cids);
        });


        Topic::query()->insert($topics->toArray());
    }

}

