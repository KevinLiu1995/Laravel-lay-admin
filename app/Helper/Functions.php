<?php
/**
 * Created By PhpStorm
 * Author: Kevin
 * Date: 2020/1/7 16:58
 * Email: 863129201@qq.com
 */

namespace App\Helper;
use Illuminate\Contracts\Pagination\Paginator;

//对返回给前端前对数据进行格式处理
function responseSuccess($data = [], $message = '操作成功', $code = 200)
{
	$res = [
		'code' => $code,
		'msg' => $message,
		'data' => $data
	];

	//分页特殊处理
	if ($data instanceof Paginator) {
		$data = $data->toArray();
		$page = [
			'current_page' => $data['current_page'],
			'last_page' => $data['last_page'],
			'per_page' => $data['per_page'],
			'total' => $data['total']
		];

		$res['data'] = $data['data'];
		$res['pages'] = $page;
	}
	return response()->json($res)->setStatusCode(200);
}
