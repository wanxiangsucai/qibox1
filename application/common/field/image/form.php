<?php
function_exists('urls') || die('ERR');


$cuturl = iurl('index/image/cutimg');
$width = IN_WAP===true?'95%':'900px';
$height = IN_WAP===true?'100%':'800px';

$jscode = '';
if(fun('field@load_js',$field['type'])){	
	$serverurl = urls('index/attachment/upload','dir=images&from=base64&module='.request()->dispatch()['module'][0]);
	$jscode = <<<EOT

<style type="text/css">
.uploadImg ol{
	line-height: 35px;
	font-size: 16px;
}
.uploadImg li{
	width:80px;
	border:#DDD dotted 1px;
	text-align: center;
	padding: 15px 0;
	background: #FFF;
	font-size:32px;
	cursor: pointer;
	color: #999;
}
.uploadImg li:hover{
	border: #F60 dotted 1px;
	color: #F60;
}

.ListImgs:after{
	content: '';
	display: block;
	clear: both;
}
.ListImgs div{
	width:80px;
	float: left;
	position: relative;
	margin: 10px 10px 0 0;
}
.ListImgs div span{
	display: block;
	position: relative;
	background: #FFF;
	box-shadow: 0px 0px 1px #BBB;
}
.ListImgs div span:before{
	content: '';
	display: block;
	padding-top: 100%;
}
.ListImgs div span img{
	position:absolute;
	max-width:100%;
	max-height: 100%;
	left:50%;
	top: 50%;
	border:0;
  -webkit-transform: translate3D(-50%, -50%, 0);
      -ms-transform: translate3D(-50%, -50%, 0);
          transform: translate3D(-50%, -50%, 0);
} 
.ListImgs div em{
	position: absolute;
	width:25px;
	height: 25px;
	text-align: center;
	line-height: 25px;
	background:rgba(120,120,120,0.6);
	color: #FFF;
	right: 0px;
	top:0px;
	cursor: pointer;
}
.ListImgs div em.cut{
	left: 0px;
}
.ListImgs div em:hover{
	background:rgba(255,60,0,0.6);
}
</style>

<script type="text/javascript" src="__STATIC__/js/exif.js"></script>
<script type="text/javascript">
var severUrl = "$serverurl";

</script>

EOT;

}


return <<<EOT

$jscode
<script type="text/javascript">
jQuery(document).ready(function() {
	$(".uploadImge_{$name}").each(function(){
		var pics = [];
		var that = $(this);

		that.find(".upbtn").click(function(e){
			that.find('input[type="file"]').click();
		});

	//截图事件
	var add_cutimg = function(e){
		that.find('.cut').each(function () {
			var cthis = $(this);
			cthis.on('click',function(){
				var pic = cthis.parent().find("img").attr('src');
				var opt = cthis.data('options');
				layer.open({
					type: 2,
					title: '截图',
					area: ["{$width}", "{$height}"],
					scrollbar: false,
					content: '{$cuturl}?picurl='+pic+'&opt='+opt,
					end: function () {
						check_value();	//重新核对数据
					}
				});
			});
		});
	}
		//核对数据
		var check_value = function(){
				pics = [];	//重新设置值
				var obj = that.find(".ListImgs img");				
				obj.each(function(e){
					var img = $(this).attr("src");
					img = img.replace('/public/','');
					pics.push(img);
					that.find(".input_value").val( pics.join(',') );
				});
				if(obj.length==0){
					that.find(".input_value").val('');
				}
		};
		
		//删除图片
		var delpic = function(){
			that.find(".ListImgs em.del").click(function(e){
				//这里删除的图片没有真正从服务器删除
				$(this).parent().remove();
				check_value();					
			});
		};
		delpic();

		that.find('input[type="file"]').change(function(){
            uploadBtnChange($(this).get(0),that.find(".input_value"),pics,viewpics);
        });

		if(that.find(".input_value").val()!=''){
			pics = that.find(".input_value").val().split(',');
		}
		
		var viewpics = function(url,pic_array){
			var html = '';
			pic_array.forEach(function(f){
				var sear=new RegExp('http');
    			if(sear.test(f)){
        　　			html += '<div><span><img src="'+f+'"></span><em class="del"><i class="fa fa-remove"></i></em><em class="cut" data-options=""><i class="fa fa-cut"></i></em></div>';
    　　			}else{
    				html += '<div><span><img src="/public/'+f+'"></span><em class="del"><i class="fa fa-remove"></i></em><em data-options="" class="cut"><i class="fa fa-cut"></i></em></div>';
    			}
			});
			that.find(".ListImgs").html(html);
			delpic();
			add_cutimg();
		};
		viewpics('',pics);

		var uploadBtnChange = function (filefield,textObj,pics,callback){
            var scope = this;
			//var pics = [];
            if(window.File && window.FileReader && window.FileList && window.Blob){ 
                //获取上传file
                //var filefield = document.getElementById(fileName),
                file = filefield.files[0];
				var oj = filefield.files;
				for(var i=0;i<oj.length;i++){
					processfile(oj[i],textObj,pics,callback);
				}
            }else{
                alert("此浏览器不完全支持压缩上传图片");
            }
        };

        var processfile = function (file,textObj,pics,callback) {
			var Orientation = 0;
			var alltags = {};
			//获取图片的参数信息			
			EXIF.getData(file, function(){
				alltags = EXIF.pretty(this);
				//EXIF.getAllTags(this);
				Orientation = EXIF.getTag(this, 'Orientation');
			});			

            var reader = new FileReader();

			var nopress = false;	//不要压缩
			if(file.size<500000 || (file.name.substr(file.name.lastIndexOf(".")+1)).toLowerCase()=='gif'){
				reader.readAsDataURL(file);
				nopress = true;
			}

            reader.onload = function (event) {

				if(nopress==true){	//不要压缩
					$.post(severUrl, {'imgBase64':event.target.result,'Orientation':Orientation,'tags':alltags}).done(function (res) {
							 if(res.code==1){
								 //pics.push(res.path);	//组图
								 pics[0] = res.path;	//单图
								 textObj.val( pics.join(',') );
								 if(typeof callback == 'function'){
									callback(res.url,pics);
								 }
							 }
					}).fail(function () {
							alert('操作失败，请跟技术联系');
					});
					return ;
				}

                var blob = new Blob([event.target.result]); 
                window.URL = window.URL || window.webkitURL;
                var blobURL = window.URL.createObjectURL(blob); 
                var image = new Image();
                image.src = blobURL;
                image.onload = function() {
                    var resized = resizeUpImages(image , (file.name.substr(file.name.lastIndexOf(".")+1)).toLowerCase() );					
					if(resized){
						layer.load(1);
						$.post(severUrl, {'imgBase64':resized,'Orientation':Orientation,'tags':alltags}).done(function (res) {
							layer.closeAll();
							 if(res.code==1){
								 //pics.push(res.path);	//组图
								 pics[0] = res.path;	//单图
								 textObj.val( pics.join(',') );
								 if(typeof callback == 'function'){
									callback(res.url,pics);
								 }
							 }
						}).fail(function () {
							layer.closeAll();
							layer.alert('操作失败，请跟技术联系');
						});	
						
					}
                }
            };
            reader.readAsArrayBuffer(file);
        };

        var resizeUpImages = function (img,type) {
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
			if(type=='jpg'||type==''||type==undefined){
				type='jpeg';
			}
            //压缩率
            return canvas.toDataURL("image/"+type,0.72); 
        };

	});
});
</script>

<div class="uploadImge_{$name}">
		<ul class="uploadImg">
			<div style="display:none;">
				<input type="file" accept="image/*"/> 
				<input type="text" name="{$name}" value="{$info[$name]}" class="input_value" style="width:100%;" />				
			</div>			 
			<li class="upbtn"><i class="si si-camera"></i></li>
		</ul>
		<div class="ListImgs"></div>
</div>

EOT;
;