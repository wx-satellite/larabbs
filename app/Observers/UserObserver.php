<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function creating(User $user)
    {
        //
    }

    public function updating(User $user)
    {
        //
    }


    // 设置默认头像
    public function saving(User $user) {
        // 当头像为空时，进行设置
        if(empty($user->avatar)) {
            $user->avatar = "https://cdn.learnku.com/uploads/images/201710/30/1/TrJS40Ey5k.png";
        }
    }
}