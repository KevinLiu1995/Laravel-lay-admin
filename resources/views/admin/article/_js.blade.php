
<script>
    layui.use(['upload','layer','element','form'],function () {
        var $ = layui.jquery;
        var upload = layui.upload;
	    var element = layui.element;

        //普通图片上传
        $(".uploadPic").each(function (index,elem) {
            upload.render({
                elem: $(elem)
                ,url: '{{ route("api.upload") }}'
                ,multiple: false
                ,data:{"_token":"{{ csrf_token() }}"}
	            ,accept: 'images' // 上传文件类型 images（图片）、file（所有文件）、video（视频）、audio（音频)
                ,choose: function(obj){
		            element.progress('progress', '0%');
		            // layer.load(); //上传loading
                }
	            ,progress: function(n){
		            var percent = n + '%';//获取进度百分比
	                // console.log(n)
		            element.progress('progress', percent); //配合 layui 进度条元素使用
	            }
                ,done: function(res){
                    //如果上传失败
                    if(res.code === 0){
                        layer.msg(res.msg,{icon:1});
                        // 更改进度条颜色
	                    $("#progress_bar").removeClass("layui-bg-blue")
	                    $("#progress_bar").addClass("layui-bg-green")

	                    $(elem).parent('.layui-upload').find('.layui-upload-box').html('<li><img src="'+res.url+'" class="img"/><p>上传成功</p></li>');
	                    $(elem).parent('.layui-upload').find('.layui-upload-input').val(res.url);
                    }else {
                    	//更改进度条颜色
                    	$("#progress_bar").removeClass("layui-bg-blue")
                    	$("#progress_bar").addClass("layui-bg-red")
                        layer.msg(res.msg,{icon:2})
                    }
                }
	            ,error: function (index,upload) {
                	// 更改进度条颜色
		            $("#progress_bar").removeClass("layui-bg-blue")
		            $("#progress_bar").addClass("layui-bg-red")
		            console.log(upload);
	            }
            });
        })

    })
</script>
<link href="/baidu-editor/themes/default/css/umeditor.min.css" type="text/css" rel="stylesheet">
<script src="/baidu-editor/third-party/jquery.min.js"></script>
<!-- 配置文件 -->
<script type="text/javascript" src="/baidu-editor/umeditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="/baidu-editor/umeditor.min.js"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
    var ue = UM.getEditor('container');
</script>
