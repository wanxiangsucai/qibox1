<?php

return [    
    'title'=>'自定义表单demo',
	'form'=>[
		['text','test_title','这是自定义标题','辅助说明','默认标题'],
		['textarea','test_about','这是自定义多行文本','辅助说明','默认内容'],
		['ueditor','test_content','百度编辑器','辅助说明','默认内容'],
		['select','test_sel','','这是下拉框选择',['未知','男','女'],1], //1代表默认选中第二个
		['radio','test_type','字体类型(单选)','这是单选',['默认','粗体','斜体'],1], //1代表默认选中第二个
		['checkbox','test_types','配套','这是多选',['水','电','煤气'],'1,2'], //1,2代表默认选中第2个第3个
		['image','test_pic','单图'],
		['images','test_pics','组图'],
		['images2','test_imgs','带标题的组图'],
		['icon','test_logo','图标'],
		['color','test_color','颜色选择','说明','#333333'],
		['bmap','test_bmap','地图坐标','','113.288356,23.479845'],
		['links','test_links','多链接'],
		['file','test_file','单个文件','可以让用户上传视频文件'],
		['files','test_file','多个文件','可以让用户上附件'],
		['files2','test_file','多个文件带说明','可以让用户上附件'],
		['number','test_number','这是数字','辅助说明','88'],
		['time','test_time','这是时间','辅助说明','12:30'],
		['date','test_date','这是日期','辅助说明','12-02'],
		['datetime','test_datetime','这是完整日期','辅助说明','2020-12-02 12:15'],
	],

];