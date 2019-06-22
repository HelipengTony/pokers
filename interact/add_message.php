<?php

error_reporting(E_ALL & ~E_NOTICE);
//引入composer
require '../vendor/autoload.php';
define('LAZER_DATA_PATH', dirname(dirname(__FILE__)) . '/data/');
use Lazer\Classes\Database as Lazer;

//数据库创建与判断
try {
    \Lazer\Classes\Helpers\Validate::table('messages')->exists();
} catch (\Lazer\Classes\LazerException $e) { //不存在则创建
    Lazer::create('messages', array(
        'id' => 'integer', //内容条段 id
        'speaker' => 'integer', //发送者
        'speaker_name' => 'string', //发送者名字,减少前端数据库请求
        'is_super' => 'integer', //发送者级别,减少前端数据库请求
        'belong_class' => 'integer', //主题对应班级
        'content' => 'string', //内容
        'thread' => 'integer', //班级下的主题 id
        'emoji' => 'string', //添加 emoji
        'img_url' => 'string', //类型为文本，但有图片附件
        'date' => 'integer', //发送时间
        'type' => 'string', //类型：文件 or 文本(+图片)
        'file_url' => 'string', //类型为文件时文件的 url
        'file_name' => 'string' //类型文文件时的文件名,用于判断展示图标
    ));
}

session_start();

//判断发送参数是否齐全，请求创建班级的用户是否为当前登录用户
if (!empty($_POST['speaker_name']) && !empty($_POST['thread']) && !empty($_POST['belong_class']) && !empty($_POST['speaker']) && !empty($_POST['type']) && ($_SESSION['logged_in_id'] == (int)$_POST['speaker'])) {

    //输入处理
    function input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    //获取参数
    $speaker = input($_POST['speaker']);
    $speaker_name = input($_POST['speaker_name']);
    $class = input($_POST['belong_class']);
    $content = input($_POST['content']);
    $thread = input($_POST['thread']);
    $type = input($_POST['type']);

    //判断发送的内容类型
    if ($type == 'text') {
        $img_url = input($_POST['img_url']);
        if (!empty($img_url)) {
            $status = 'img';
        } elseif (!empty($content)) {
            $status = 1;
        } else {
            $status = 0;
        }
    } elseif ($type == 'file') {
        $file_name = input($_POST['file_name']);
        $file_url = input($_POST['file_url']);
        if (!empty($file_name) && !empty($file_url)) {
            $status = 'file';
        } else {
            $status = 0;
        }
    } else {
        $status = 0;
    }

    if ($status) {



        //业务逻辑
        $array = Lazer::table('classes')->findAll()->asArray('id');
        if (!array_key_exists($class, $array)) {
            $status = 0;
            $code = 101;
            $mes = 'Class does not exist';
        } else {
            $array = Lazer::table('users')->limit(1)->where('id', '=', (int)$speaker)->andWhere('name', '=', (string)$speaker_name)->find()->asArray();
            if (!!$array) { //判断用户存在




                if ($array[0]['type'] == 2) { //教师发送者
                    $is_super = 1;
                } else { //学生发送者
                    $is_super = 0;
                }

                $array = Lazer::table('threads')->limit(1)->where('id', '=', (int)$thread)->andWhere('belong_class', '=', (int)$class)->find();
                if (!!$array->name) { //判断主题存在




                    if ($status == 'img') { //包含图片(不判断内容是否为空)

                        $this_id = Lazer::table('messages')->findAll()->count() + 1;
                        $row = Lazer::table('messages');
                        $row->id = (int)$this_id;
                        $row->speaker_name = $speaker_name;
                        $row->speaker = (int)$speaker;
                        $row->is_super = (int)$is_super;
                        $row->belong_class = (int)$class;
                        $row->content = $content;
                        $row->thread = (int)$thread;
                        $row->img_url = $img_url;
                        $row->date = time();
                        $row->type = 'text';
                        $row->save();

                        $status = 1;
                        $code = 131;
                        $mes = 'Successfully sent an image + text message';
                    } elseif ($status == 'file') { //文件上传(必须无内容)
                        if (empty($content)) {

                            $this_id = Lazer::table('messages')->findAll()->count() + 1;
                            $row = Lazer::table('messages');
                            $row->id = (int)$this_id;
                            $row->speaker_name = $speaker_name;
                            $row->speaker = (int)$speaker;
                            $row->is_super = (int)$is_super;
                            $row->belong_class = (int)$class;
                            $row->thread = (int)$thread;
                            $row->file_url = $file_url;
                            $row->file_name = $file_name;
                            $row->date = time();
                            $row->type = 'file';
                            $row->save();

                            $status = 1;
                            $code = 132;
                            $mes = 'Successfully sent a file message';
                        } else {
                            $status = 0;
                            $code = 121;
                            $mes = 'The content must be empty when uploading a file';
                        }
                    } else { //仅文字内容(必须判断内容为空)
                        if (!empty($content)) {

                            $this_id = Lazer::table('messages')->findAll()->count() + 1;
                            $row = Lazer::table('messages');
                            $row->id = (int)$this_id;
                            $row->speaker_name = $speaker_name;
                            $row->speaker = (int)$speaker;
                            $row->is_super = (int)$is_super;
                            $row->belong_class = (int)$class;
                            $row->thread = (int)$thread;
                            $row->content = $content;
                            $row->date = time();
                            $row->type = 'text';
                            $row->save();

                            $status = 1;
                            $code = 133;
                            $mes = 'Successfully sent a text message';
                        } else {
                            $status = 0;
                            $code = 120;
                            $mes = 'The content can not be empty';
                        }
                    }
                } else {
                    $status = 0;
                    $code = 122;
                    $mes = 'The thread does not exist';
                }
            } else {
                $status = 0;
                $code = 104;
                $mes = 'The speaker does not exist';
            }
        }
    } else {
        $status = 0;
        $code = 113;
        $mes = 'Illegal request';
    }
} else {
    $status = 0;
    $code = 103;
    $mes = 'Illegal request';
}

//输出 json
$return = array(
    'status' => $status,
    'code' => $code,
    'mes' => $mes
);
echo json_encode($return);
