<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware("auth");
    }



    // snake_case(class_basename($notification->type))
    // class_basename(App\Http\Controllers\NotificationsController)结果为：NotificationsController
    // snake_case(TopicReply)：topic_reply

    public function index($pageSize=15) {
        $notifications = Auth::user()->notifications()->paginate($pageSize);
        Auth::user()->markAsRead();
        return view("notifications.index",compact("notifications"));
    }
}
