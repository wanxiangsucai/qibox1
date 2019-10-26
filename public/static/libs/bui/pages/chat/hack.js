// 默认已经定义了main模块
loader.define(function() {

    var pageview = {};
	var uid;

 

    // 主要业务初始化
    pageview.init = function() {
        // 这里写main模块的业务
		//var option = router.option();
		//console.log("dd"+uid);
        //bui.hint("loadPart_part.js was loaded="+uid);

		router.$("#hongbaoBtn").click(function(){
			if(uid>0){
				layer.alert("只有群聊才能发红包!");
				return ;
			}
			layer.open({
				type: 2,
				title: false,
				shadeClose: true,
				shade: 0.3,
				area: ['370px', '600px'],
				content: '/index.php/qun/hongbao/add/mid/1/aid/'+(-uid)+'.html'
			});
			router.$(".hack_wrap").hide();
		});

		router.$("#photoBtn").click(function(){
			router.$(".chatbar").hide();
			setTimeout(function(){
				router.$(".bui-mask").click(function(){
					router.$(".chatbar").show();
				});
			},500);
		})
		
		console.log("碎片加载成功");
		this.upload();
		loader.import(["/public/static/js/exif.js"],function(){});	//上传图片要获取图片信息
    }

    // 事件绑定
    pageview.bind = function() {
    }
	
	// 上传图片
	pageview.upload = function() {
		//loader.import(["/public/static/js/exif.js"],function(){});	//上传图片要获取图片信息
		var uiUpload = bui.upload();
		var uiActionsheet = bui.actionsheet({
					trigger: "#photoBtn",
					opacity:"0.8",
					//mask:false,
					buttons: [{ name: "拍照上传", value: "camera" }, { name: "从相册选取", value: "photo" }],
					callback: function(e) {						
						var ui = this;
						var val = $(e.target).attr("value");
						switch (val) {
							case "camera":
								ui.hide();
								router.$(".chatbar").show();
								uiUpload.add({
									"from": "camera",
									"onSuccess": function(val, data) {
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												upload_pic(url)
											}
										});

										// 也可以直接调用start上传图片
									}
								})
								break;
							case "photo":
								ui.hide();
								router.$(".chatbar").show();
								uiUpload.add({
									"from": "",
									"onSuccess": function(val, files) {
										 var Orientation=0
										 var filefield = document.getElementById($('input[name="uploadFiles"]').attr('id')) 
											 var file = filefield.files[0];
										     console.log($('input[name="uploadFiles"]'));
											EXIF.getData(file, function(){												 
												Orientation = EXIF.getTag(this, 'Orientation');
												console.log("Orientation ="+Orientation);
											});										
										//console.log(val);
										//console.log(this.data()[0]);
										//var url = window.URL.createObjectURL(files[0]);
										//document.querySelector('img').src = window.URL.createObjectURL(url);
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												upload_pic(url,Orientation)
											}
										});
										// 也可以直接调用start上传图片
									}
								})

								break;
							case "cancel":
								ui.hide();
								router.$(".chatbar").show();
								break;
						}
					}
				})

				function templatePhoto(url) {
					var str = `<img src="${url}" class="big" />`;					
					return str;
				}

				function post_content(str){
					$.post("/member.php/member/wxapp.msg/add.html",{
						'uid':uid,
						'content':str,
						},function(res){		
						if(res.code==0){
							router.$(".hack_wrap").hide();
							router.$(".face_wrap").hide();
							bui.hint('发送成功');
						}else{
							layer.alert('发送失败:'+res.msg);
						}
					});
				}
				
				function upload_pic(base64,Orientation){
					bui.hint("图片上传中,请稍候...");
					var image = new Image();
						image.src = base64;
						image.onload = function() {
						var resized = resizeUpImages(image);
						var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";
						$.post(severUrl, {'imgBase64':resized,'Orientation':Orientation,'tags':''}).done(function (res) {
							if(res.code==1){
								//console.log(res);
								var url = res.path;
								if(url.indexOf('://')==-1 && url.indexOf('/public/')==-1){
									url = '/public/'+url;
								}
								post_content( templatePhoto(url) );
								//$("#chatInput").val( templatePhoto(url)+$("#chatInput").val() )
								//if($("#btnSend").hasClass("disabled"))$("#btnSend").removeClass("disabled").addClass("primary");
							}else{
								alert(res.info);
							}
						}).fail(function () {
							alert('操作失败，请跟技术联系');
						});
					}					
				}

				function resizeUpImages(img) {
					//压缩的大小
					var max_width = 1920; 
					var max_height = 1080;
					var canvas = document.createElement('canvas');
					var width = img.width;
					var height = img.height;
					if(width > height) {
						if(width > max_width) {
							height = Math.round(height *= max_width / width);
							width = max_width;
						}
					}else{
						if(height > max_height) {
							width = Math.round(width *= max_height / height);
							height = max_height;
						}
					}
					canvas.width = width;
					canvas.height = height;
					var ctx = canvas.getContext("2d");
					ctx.drawImage(img, 0, 0, width, height);
					//压缩率
					return canvas.toDataURL("image/jpeg",0.72); 
				}
	}
	


	var getParams = bui.getPageParams();
    getParams.done(function(result){
		uid = result.uid; 
    })

    // 初始化
    pageview.init();
    // 绑定事件
    pageview.bind();
    
    return pageview;
})