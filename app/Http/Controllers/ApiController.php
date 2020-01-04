<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{

    //图片上传
    public function upload(Request $request)
    {
		//返回信息json
		$data = ['code'=>1, 'msg'=>'上传失败', 'data'=>''];
		$file = $request->file('file');

        //上传文件最大大小,单位M
        $maxSize = 1000;
        //支持的上传图片类型
        $allowed_extensions = ['png', 'jpg', 'gif' ,'jpeg'];
        // 上传文件夹名
		$folder_name = 'uploads/images/' . date('Ym/d', time());
		// 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
		$upload_path = public_path() . '/' . $folder_name;
		// 文件后缀
		$extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
		// 拼接文件名
		$filename = time() . '_' . Str::random(10) . '.' . $extension;

        //检查文件是否上传完成
        if ($file->isValid()){
            //检测图片类型
            $ext = $file->getClientOriginalExtension();
            if (!in_array(strtolower($ext), $allowed_extensions, true)){
                $data['msg'] = '请上传' .implode(',',$allowed_extensions). '格式的图片';
                return response()->json($data);
            }
            //检测图片大小
            if ($file->getSize() > $maxSize*1024*1024){
                $data['msg'] = '图片大小限制' .$maxSize. 'M';
                return response()->json($data);
            }
        }else{
            $data['msg'] = $file->getErrorMessage();
            return response()->json($data);
        }

		// 将图片移动到我们的目标存储路径中
		$file->move($upload_path, $filename);

		return [
			'code'  => 0,
			'msg'   => '上传成功',
			'data'  => $filename,
			'url' => config('app.url') . "/$folder_name/$filename"
		];
    }
}
