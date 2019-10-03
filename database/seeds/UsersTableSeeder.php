<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(\Faker\Generator::class);

        // 头像假数据
        $avatars = [
            'https://cdn.learnku.com/uploads/images/201710/14/1/s5ehp11z6s.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/Lhd1SHqu86.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/LOnMrqbHJn.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/xAuDMxteQy.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png',
            'https://cdn.learnku.com/uploads/images/201710/14/1/NDnzMutoxX.png',
        ];

        // 根据工厂类返回User模型实例的Collection集合
        $users = factory(\App\Models\User::class)->times(10)->make()->each(function($user, $index)use($faker, $avatars){
            $user->avatar = $faker->randomElement($avatars);
        });

        // 直接toArray是不会显示password和remember_token的因为User模型的hidden属性中指明隐藏了
        $us = $users->makeVisible(["password","remember_token"])->toArray();


        \App\Models\User::query()->insert($us);


        $user = \App\Models\User::query()->first();
        $user->name = 'Wxsatellite';
        $user->email = '1453085314@qq.com';
        $user->avatar = 'https://cdn.learnku.com/uploads/images/201710/14/1/ZqM7iaP4CR.png';
        $user->password = bcrypt("11111111");
        $user->save();
        $user->assignRole("Founder");

        $user = \App\Models\User::query()->find(2);
        $user->assignRole("Maintainer");

    }
}
