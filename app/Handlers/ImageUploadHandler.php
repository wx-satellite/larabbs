<?php

namespace App\Handlers;



use Illuminate\Support\Str;


// Handlers文件夹用来存放工具类（工具类指一些与业务逻辑相关性不强的类）


// 一般图片等资源是不需要纳入版本库的，本项目在avatar目录新建了一个.gitignore文件,用于忽略相关文件，内容如下：
//  *
//  !.gitignore
// 意思是忽略当前文件夹所有文件除了.gitignore之外


class ImageUploadHandler {


    // 只允许如下的后缀图片上传
    protected $allow_ext = ["png","jpg","gif","jpeg"];

    public function save($file, $folder, $file_prefix) {


        // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
        // 文件夹切割能让查找效率更高。
        $folder_name = "uploads/images/$folder/".date("Ym/d", time());


        // public_path()获取的时public文件夹的物理路径
        $upload_path = public_path().'/'.$folder_name;

        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?:"png";


        // 拼接文件名，加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1_1493521050_7BVc9v9ujP.png
        $filename = $file_prefix.'_'.time().'_'.Str::random(10).'.'.$extension;

        if(!in_array($extension, $this->allow_ext)) {
            return false;
        }


        // 将文件挪到指定位置：第一个参数是目标位置的路径，第二个参数是新的文件名
        $file->move($upload_path, $filename);


        return [
            "path" => config("app.url")."/$folder_name/$filename"
        ];
    }
}