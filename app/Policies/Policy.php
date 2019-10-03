<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }
    // 关于策略过滤器的使用：
    //      我们只需在策略中定义一个 before() 方法。before 方法会在策略中其它所有方法之前执行，这样提供了一种全局授权的方案。
    //  返回 true 是直接通过授权；
    //  返回 false，会拒绝用户所有的授权；
    //  如果返回的是 null，则通过其它的策略方法来决定授权通过与否。
    public function before($user, $ability)
	{
	    if($user->can("manage_contents")) {
	        return true;
        }
	}
}
