<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class YoutubeController extends Controller
{
    public $disk;

    //建構子
    public function __construct()
    {
        $this->disk = Storage::disk('public'); //備註: 如果不能刪除資料，有可能是 storage/video 資料夾權限並非 apache 可操作
    }

    //解構子
    public function __destruct()
    {
        unset( $this->disk );
    }

    //主頁
    public function index()
    {
        $arr_list = $this->_getFiles(); //檔案列表
        //echo "<pre>";print_r($arr_list);exit;
        return view('youtube.home',[
            'arr_list' => $arr_list
        ]);
    }

    //取得檔案列表
    private function _getFiles()
    {
        return $this->disk->allFiles();
        //return Storage::allFiles(public_path().'/storage');
//        $file_ary = [];
//        foreach (glob('storage/*') as $key => $val) {
//            $file_ary[] = str_replace("storage/","",$val);
//        }

//        return $file_ary;
    }

    //取得檔案列表(給 XML 用)
    public function getFiles()
    {
        //取得檔案列表
        $arr = $this->_getFiles();

        header('Content-Type: text/xml');
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml.= '<records>';
        for( $i = 0; $i < count($arr); $i++ )
        {
            $xml.= '<record ';
            $xml.= 'path="'.htmlspecialchars( $arr[$i] ).'" ';
            $xml.= '/>';
        }
        $xml.= '</records>';
        echo $xml;
        unset($arr);
    }

    //刪除檔案
    public function deleteFile(Request $request)
    {
        $path = $request->input('path');

        $flag = 0;
        if( $this->disk->exists( $path ) )
        //if( file_exists('storage/'.$path) )
        {
            if($this->disk->delete( $path )) $flag = 1;
            //if(unlink(public_path().'/storage/'.$path)) $flag = 1;
        }
        return $flag;
    }
}