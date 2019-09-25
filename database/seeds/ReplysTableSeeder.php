<?php

use Illuminate\Database\Seeder;
use App\Models\Reply;

class ReplysTableSeeder extends Seeder
{
    public function run()
    {
        $uids = \App\Models\User::query()->pluck("id")->toArray();

        $topics = \App\Models\Topic::query()->pluck("id")->toArray();

        $faker = app(\Faker\Generator::class);

        $replies = factory(Reply::class)->times(1000)->make()->each(function($reply)use($uids, $topics, $faker){
            $reply->user_id = $faker->randomElement($uids);
            $reply->topic_id = $faker->randomElement($topics);
        });

        Reply::query()->insert($replies->toArray());
    }

}

