	function post_comment(pid){			
		if(pid>0){
			repalyid=pid;
			comment_post_url=comment_base_url+"?pid="+pid;
		}
		layer.open({
		  type: 1,
			title:'发表评论',
		  skin: 'layui-layer-demo', //样式类名
		  area: ['320px', '281px'], //宽高
		  closeBtn: 0, //不显示关闭按钮
		  anim: 2,
		  shadeClose: true, //开启遮罩关闭
		  content: '<ul class="PostCommentBox"><ol><textarea placeholder="请输入评论内容"></textarea></ol><!--<li><button type="butter" onclick="post_comment1()">发表</button><button type="butter" onclick="layer.closeAll()">取消</button></li>--></ul>',
			btn:['确认','取消'],
			btn1:function(){
				post_comment1()
			}
		});
	}


function del_comment(id){
	layer.confirm('你确认要删除吗？',function(){
		$.get(comment_delete_url+"?ids="+id,function(res){
			if(res.code==0){
				layer.msg('删除成功');
				$("#comment_cnt_"+id).hide();
			}else{
				layer.alert(res.msg);
			}
		});
	});
}


function post_comment1(){
	if(cache_need_tncode==true){
		open_tncode(function(){
			cache_need_tncode = false;
			real_post1();
		});
	}else{
		real_post1();
	}	
}

	//引用评论
	function real_post1(){
		var contents=$('.PostCommentBox textarea').val();
		if(contents==''){
			layer.alert("请输入评论内容！");
		}else{
			$.post(
				comment_post_url,
				{content:contents},
				function(res,status){
					cache_need_tncode = needTncode;
					if(res.code==0){
						if(repalyid>0){
							$('.repalyinfs'+repalyid).html(res.data);
						}else{
							$('.ListComment').html(res.data);
							commentpage=1;
							//$('.ShowMoreComment').fadeIn();
						}
						layer.closeAll(); //疯狂模式，关闭所有层
						layer.msg('发表成功！');
						HiddenShowMoreComment();
					}else{
						layer.alert('评论发表失败:'+res.msg);
					}
				}
			);				
		}			
	}
	
function post_commentPc(){
	if(cache_need_tncode==true){
		open_tncode(function(){
			cache_need_tncode = false;
			real_post2();
		});
	}else{
		real_post2();
	}	
}

	function real_post2(){
		var contents=$('.PostCommentBox1 textarea').val();
		if(contents==''){
			layer.alert("请输入评论内容！");
		}else{
			$('.PostCommentBox1 textarea').val('评论发表中……');
			$('.PostCommentBox1 li').html('<span>发表中</span>');
			$.post(
				comment_post_url,
				{content:contents},
				function(res,status){
					cache_need_tncode = needTncode;
					$('.PostCommentBox1 textarea').val('');
					$('.PostCommentBox1 li').html('<button type="butter" onclick="post_commentPc()">发表</button>');
					if(res.code==0){
						$('.ListComment').html(res.data);
						commentpage=1;
						layer.msg('发表成功！');
						HiddenShowMoreComment();
					}else{
						layer.alert('评论发表失败:'+res.msg);
					}
				}
			);				
		}
	}

	function dingcomment(id){
			var agree=parseInt($('.agree'+id).html());
			$.get(comment_base_url+'?agree=1&id='+id+'&'+Math.random(),function(res){
				if(res.code==0){
					agree++;
					$('.agree'+id).html(agree);
					layer.msg('点赞成功！');
				}else{
					layer.alert('点赞失败:'+res.msg);
				}
			});
	}

	function ShowMoreComment(){
			commentpage++;
			$.get(comment_page_url+'?page='+commentpage+'&'+Math.random(),function(res){
				if(res.code==0){
					if(res.data==''){
						layer.msg('显示完了！');
						$('.ShowMoreComment').fadeOut();
					}else{
						res.data="<div class='pages"+commentpage+"'>"+res.data+"</div>";			
						$('.ListComment').append(res.data);
						$('.ListComment .pages'+commentpage).hide();
						$('.ListComment .pages'+commentpage).show(500);
					}
				}else{
					layer.msg(res.msg,{time:2500});
				}
			});
	}
	
	function HiddenShowMoreComment(){
			var Comments=$('.ListComment .lists').length;
			if(parseInt(Comments/comment_rows)<1){
				$('.ShowMoreComment').hide();
			}else{
				$('.ShowMoreComment').show();
			}
	}

	HiddenShowMoreComment();


function yz_comment(id,obj){	
	$.post(comment_yz_url,{id:id},function(res){
		if(res.code==0){
			if(obj.hasClass('notyz')){
				obj.removeClass('notyz');
			}else{
				obj.addClass('notyz');
			}
		}else{
			layer.alert(res.msg);
		}
	});
}