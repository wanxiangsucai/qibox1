$(function(){	
	var siteurl=$(".siteurl").html();
	var gosearch=siteurl+"/f/search.php?action=search";
	$(".subbutter input").click(function(){
		var keywords=$(".keywords input").val();
		if(keywords==""){
			alert("����д��Ҫ����������!");
			$(".keywords input").focus();
			return false;
		}
	});	
	$(".listtype span").click(function(){
		var words=$(this).html();
		switch(words){
			case "����":
			gosearch=siteurl+"/f/search.php?action=search";
			break;
			case "��Ʒ":
			gosearch=siteurl+"/shop/search.php?action=search";
			break;
			case "��Ѷ":
			gosearch=siteurl+"/news/search.php?action=search";
			break;
			case "�Ź�":
			gosearch=siteurl+"/tg/search.php?action=search";
			break;
			case "�̼�":
			gosearch=siteurl+"/hy/search.php?action=search";
			break;
			case "����":
			gosearch=siteurl+"/coupon/search.php?action=search";
			break;
			case "��Ƹ":
			gosearch=siteurl+"/hr/search.php?action=search";
			break;
			case "����":
			gosearch=siteurl+"/2shou/search.php?action=search";
			break;
			case "����":
			gosearch=siteurl+"/dianping/search.php?action=search";
			break;
		}
		$(".topsearch").attr("action", gosearch);
		$(".showtype").html(words);
		$(".showtype").removeClass("onselect");
		$(".listtype").removeClass("showselect");
		$(".listtype span").removeClass("ck");
		$(this).addClass("ck");
	});
	$(".MainContainer").mouseover(function(){
		$(".showtype").removeClass("onselect");
		$(".listtype").removeClass("showselect");								   
	});
	$(".listtype span").mouseover(function(){
		$(".listtype span").removeClass("on");
		$(this).addClass("on");
	});
	$(".listtype span").mouseout(function(){
		$(".listtype span").removeClass("on");
	});
	$(".showtype").click(function(){
		$(this).addClass("onselect");
		$(".listtype").addClass("showselect");
	});
	$(".keywords input").click(function(){
		$(this).val("");
	});
	$(".MainMenu ul li:eq(0)").addClass("first");
	$(".MainMenu ul li:eq(1)").addClass("second");
	$(".MainMenu ul li").mouseover(function(){
		$(".MainMenu ul li").removeClass("over");
		$(".MainMenu ul li").removeClass("next");
		$(this).addClass("over");
		var thenum=$(this).index()+1;
		$(".MainMenu ul li:eq("+thenum+")").addClass("next");
	});
	$(".MainMenu ul li").mouseout(function(){
		$(".MainMenu ul li").removeClass("over");
		$(".MainMenu ul li").removeClass("next");
	});
	$(".ShowMenu dl dt").click(function(){
		$(this).parent("dl").toggleClass("show");
	});
	$(".showall").click(function(){
		$(".ShowMenu dl").addClass("show");
	});
	$(".hideall").click(function(){
		$(".ShowMenu dl").removeClass("show");
	});
	$(".ShowMenu dl").addClass("show");
});

function SetWinHeight(obj){
	var win=obj;
	if (document.getElementById){
		if (win && !window.opera){
			if (win.contentDocument && win.contentDocument.body.offsetHeight)
				win.height = win.contentDocument.body.offsetHeight;
			else if(win.Document && win.Document.body.scrollHeight)
				win.height = win.Document.body.scrollHeight;
		}
	}
}