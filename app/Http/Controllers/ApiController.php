<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Intervention\Image\Facades\Image;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class ApiController extends Controller
{

	//图片上传
	public function upload(Request $request)
	{
		//返回信息json
		$data = ['code' => 1, 'msg' => '上传失败', 'data' => ''];
		$file = $request->file('file');

		//上传文件最大大小,单位M
		$maxSize = 20;
		// 文件质量0～1，1代表不压缩
		$quality = 1;
		//支持的上传图片类型
		$allowed_extensions = ['png', 'jpg', 'gif', 'jpeg'];
		// 上传文件夹名
		$folder_name = 'uploads/images/' . date('Ym/d', time());
		// 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
		$upload_path = public_path() . '/' . $folder_name;
		// 文件后缀
		$extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
		// 拼接文件名
		$filename = time() . '_' . Str::random(10) . '.' . $extension;

		//检查文件是否上传完成
		if ($file->isValid()) {
			//检测图片类型
			$ext = $file->getClientOriginalExtension();
			if (!in_array(strtolower($ext), $allowed_extensions, true)) {
				$data['msg'] = '请上传' . implode(',', $allowed_extensions) . '格式的图片';
				return response()->json($data);
			}
			//检测图片大小
			if ($file->getSize() > $maxSize * 1024 * 1024) {
				$data['msg'] = '图片大小限制' . $maxSize . 'M';
				return response()->json($data);
			}
			// 动态生成图片质量
			if ($file->getSize() > 10*1024*1024){
				$quality = 0.3;
			}else if ($file->getSize() > 5*1024*1024){
				$quality  = 0.5;
			}else if ($file->getSize() > 8*1024*1024){
				$quality = 0.8;
			}else{
				$quality = 1;
			}
		} else {
			$data['msg'] = $file->getErrorMessage();
			return response()->json($data);
		}



		if (env('UPLOAD_DRIVER','local') === 'qiniu') {
			$res = self::qiniuUpload($file,$filename);
			if ($res){
				$data = [
					'code' => 0,
					'msg' => '上传成功',
					'data' => $filename,
					'url' => $res
				];
			}
		} else {
			// 本地上传，将图片移动目标存储路径中
			$file = Image::make($file);
			$file->encode($extension,$quality);
			if (!file_exists($upload_path)){
				if (!mkdir($upload_path, 0777) && !is_dir($upload_path)) {
					$data = ['code' => 1, 'msg' => '上传失败,文件夹创建失败', 'data' => ''];
				}
			}
			$res = $file->save($upload_path.'/'.$filename);
			if ($res){
				$data = [
					'code' => 0,
					'msg' => '上传成功',
					'data' => $filename,
					'url' => config('app.url') . "/$folder_name/$filename"
				];
			}
		}

		return response()->json($data);
	}

	/*
   * 七牛上传图片
   */
	public static function qiniuUpload($file,$filename)
	{
		$auth = new Auth(env('QINIU_AK'), env('QINIU_SK'));
		//生成上传图片的token
		$token = $auth->uploadToken(env('QINIU_BUCKET'));
		$uploadMgr = new UploadManager();
		list($ret, $err) = $uploadMgr->putFile($token, $filename, $file);
		if ($ret) {
			//这里返回的是一个bucket的域名,在前面添加http://后就可以正常看到图片
			return env('QINIU_DOMAIN') . '/' . $filename;
		}
		return null;
	}

}
