<script>
	layui.use(['upload', 'layer', 'element', 'form'], function () {
		var $ = layui.jquery;
		var upload = layui.upload;
		var element = layui.element;

		//普通图片上传
		$(".uploadPic").each(function (index, elem) {
			var uploadInst = upload.render({
				elem: $(elem)
				, url: '{{ route("api.upload") }}'
				, multiple: false
				, data: {"_token": "{{ csrf_token() }}"}
				, accept: 'images' // 上传文件类型 images（图片）、file（所有文件）、video（视频）、audio（音频)
				, choose: function (obj) {
					element.progress('progress', '0%');
					// layer.load(); //上传loading
				}
				, xhr: function (index, e) {
					var percent = e.loaded / e.total;//计算百分比
					percent = parseFloat(percent.toFixed(2));
					element.progress('progress', percent * 100 + '%');
					//element.progress('progress', percent); //配合 layui 进度条元素使用
				}
				, done: function (res) {
					if (res.code === 0) {
						layer.msg(res.msg, {icon: 1});
						// 更改进度条颜色
						$("#progress_bar").removeClass("layui-bg-blue")
						$("#progress_bar").addClass("layui-bg-green")

						$(elem).parent('.layui-upload').find('.layui-upload-box').html('<li><img src="' + res.url + '" class="img"/><p>上传成功</p></li>');
						$(elem).parent('.layui-upload').find('.layui-upload-input').val(res.url);
					} else {
						//更改进度条颜色
						$("#progress_bar").removeClass("layui-bg-blue")
						$("#progress_bar").addClass("layui-bg-red")
						layer.msg(res.msg, {icon: 2})
					}
				}
				, error: function (index, upload) {
					// 更改进度条颜色
					$("#progress_bar").removeClass("layui-bg-blue")
					$("#progress_bar").addClass("layui-bg-red")
					$(elem).parent('.layui-upload').find('.layui-upload-box').html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs upload-reload">重试</a>');
					$(elem).parent('.layui-upload').find('.upload-reload').on('click', function () {
						uploadInst.upload();
					});
					console.log(upload);
				}
			});
		})

		$("#uploadMultiplePic").each(function (index, elem) {

			var fileCount = 0;//控制文件数量
			var maxFileCount = 4;//文件上传最大数量
			var maxFileSize = 20;//文件上传最大大小

			//多图片上传
			var uploadListIns = upload.render({
				elem: $(elem)
				, url: '{{ route("api.upload") }}'
				, multiple: true
				, data: {"_token": "{{ csrf_token() }}"}
				, accept: 'file' // 上传文件类型 images（图片）、file（所有文件）、video（视频）、audio（音频)
				// , number: 4 // 上传最大数量
				, auto: false // 不自动上传
				, bindAction: '#imgBeginUpload'
				, before: function (obj) { //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
					// console.log('上传前回掉', obj)
					// $('#multiple-pic-preview').empty()
				},
				// , progress: function (n) {
				// 	console.log(n)
				// 	var percent = n + '%';//获取进度百分比
				// 	layer.load(2)
				// 	console.log(index)
				// 	// element.progress('images-progress-' + index + '', percent); //配合 layui 进度条元素使用
				// },
			    // 魔改版进度提示，可以实现多个progress显示进度条
				xhr: function (index, e) {
					var percent = e.loaded / e.total;//计算百分比
					percent = parseFloat(percent.toFixed(2));
					element.progress('progress-' + index + '', percent * 100 + '%');
					console.log(index + "-----" + percent * 100 + '%');
				}
				, choose: function (obj) {
					//预读本地文件示例，不支持ie8
					obj.preview(function (index, file, result) {
						var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
						fileCount++
						if (fileCount > maxFileCount) {
							fileCount = maxFileCount;
							layer.msg('文件数量不得超过' + maxFileCount + '个', {icon: 2});
							delete files[index]
							uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
							fileCount--
							return false;
						}
						//$('#multiple-pic-preview').append('<li><img src="' + result + '"class="layui-upload-img img" style="height: auto;width: 100%;"><p>等待上传</p> <div class="layui-progress" style="margin-top: 20px;" lay-filter="progress" lay-showPercent="true"><div class="layui-progress-bar layui-bg-green"lay-percent="100%"></div></div></li>')
						$('#multiple-pic-preview').append('<li id="images-' + index + '"><img src="' + result + '" alt="' + file.name + '"class="layui-upload-img img" style="height: auto;width: 100%;"><p id="images-p-' + index + '">等待上传<button class="test-delete layui-bg-red">删除</button></p><div class="layui-progress" style="margin-top: 22px;z-index: 999" lay-filter="progress-'+index+'" lay-showPercent="true"><div class="layui-progress-bar layui-bg-green"lay-percent="100%"></div></div></li>')
						// console.log(files)
						let del_btn = '#images-' + index
						//删除文件的监听
						$(del_btn).find('.test-delete').on('click', function () {
							console.log(index)
							delete files[index]; //删除对应的文件
							$(del_btn).remove();
							fileCount--
							uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
						});

					});
				}
				, done: function (res, index) {
					if (res.code === 0) {
						// layer.msg(res.msg, {icon: 1});
						$('#images-p-' + index + '').remove()
						$('#images-' + index + '').append('<p>上传成功</p>')
						$('#multiple-pic-preview').append('<input type="hidden" name="images[]" value="' + res.url + '">')

						let img_arr = []
						let images = $("input[name^='images']")
						images.each(function (i) {
							img_arr.push($(this).val())
						});
						delete files[index]
						console.log(img_arr)
						uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
					} else {
						layer.msg(res.msg, {icon: 2})
					}
					// layer.closeAll()
				}
				, error: function (index) {
					console.log(index)
					// layer.closeAll()
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
