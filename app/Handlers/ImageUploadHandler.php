<?php

namespace App\Handlers;



use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


// Handlers文件夹用来存放工具类（工具类指一些与业务逻辑相关性不强的类）


// 一般图片等资源是不需要纳入版本库的，本项目在avatar目录新建了一个.gitignore文件,用于忽略相关文件，内容如下：
//  *
//  !.gitignore
// 意思是忽略当前文件夹所有文件除了.gitignore之外


class ImageUploadHandler {


    // 只允许如下的后缀图片上传
    protected $allow_ext = ["png","jpg","gif","jpeg"];

    public function save($file, $folder, $file_prefix, $max_width=false) {


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

        // 如果限制了图片宽度，就进行裁剪
        if ($max_width && $extension != 'gif') {

            // 此类中封装的函数，用于裁剪图片
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }


        return [
            "path" => config("app.url")."/$folder_name/$filename"
        ];
    }


    // 裁剪：有一个原因是因为图片太大的话会拖慢页面的加载速度
    // 裁剪扩展包：composer require intervention/image   github：https://github.com/Intervention/image
    public function reduceSize($file_path, $max_width)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}