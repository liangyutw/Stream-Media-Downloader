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
    public function deleteFile($path = null)
    {
        //echo $path;exit;
        $flag = 0;
        if( $this->disk->exists( $path ) )
        {
            if($this->disk->delete( $path )) $flag = 1;
        }
        return $flag;
    }

    //刪除 ts 檔案
    public function deleteTsFile($del_ts_cnt = 0)
    {
        if ($del_ts_cnt == 2) {
            return 0;
        }

        $flag = 0;
        foreach (glob(public_path().'/storage/*.ts') as $key => $val) {
            $result = unlink($val);
            $flag = 1;
        }
        //print_r(glob(public_path().'/storage/*.ts'));exit;
        return $flag;
    }

    public function updateFileName(Request $Request)
    {
        //print_r($Request->all());exit;
        $result = rename(public_path().'/storage/'.$Request->all()['old_name'], public_path().'/storage/'.$Request->all()['new_name']);
        return ($result == true) ? $Request->all()['new_name'] : 0;
    }


    //刪除 轉換重制 檔案
    public function deleteRebuildFile($del_file = null)
    {
        if (is_null($del_file)) {
            return 0;
        }

        $result = [];

        $arr = $this->_getFiles();
        if (in_array($del_file, $arr)) {
            $result['source'] = 1;
        }
        if (in_array('rebuild_'.$del_file, $arr)) {
            $result['rebuild'] = 1;
        }

        $flag = 0;
        if ($result['source'] == 1 and $result['rebuild'] == 1) {
            unlink(public_path().'/storage/'.$del_file);
            $flag = 1;
        }
//        print_r(glob(public_path().'/storage/rebuild_*.mp4'));exit;
        return $flag;
    }
}