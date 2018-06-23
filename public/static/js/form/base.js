

jQuery(document).ready(function() {
	
	//ajax无刷新提交表单
	$("form.ajax_post").each(function(){
		var form = $(this);
		var havepost = true;
		form.submit(function(){
			var form_data = form.serialize();
			var url = form.attr('action');

			if(havepost==false){
				layer.alert('请不要重复提交');
			}
			var index = layer.load(1);
			havepost = false;
			$.post(url, form_data).success(function (res) {
				layer.close(index);
				if(res.code==1){
					layer.msg(res.msg);
					setTimeout(function(){
						window.location.href = res.url;
					},200);
				}else{
					layer.open({title: '提交失败',content:res.msg});
				}
			}).fail(function () {
				layer.open({title: '提示',content: '服务器发生错误'});
			});

			return false;
		});
	});

	//联动触显 不能跟layer表单事件一起用
    if (typeof(trigger_config)=='object' && typeof(trigger_config.triggers) != 'undefined') {
        // 先隐藏依赖项
        var field_hide_array   = trigger_config.field_hide.split(',') || [];
        var field_values_array = trigger_config.field_values.split(',') || [];
        for (var index in field_hide_array) {
            $('#form_group_'+field_hide_array[index]).hide();
        }

        var trigger_form = $('.form-trigger');

        $.each(trigger_config.triggers, function (trigger, content) {
            trigger_form.delegate('[name='+ trigger +']', 'change', function (event, init) {
                var trigger_item = $(this);
                var trigger_value   = trigger_item.val();

                $(content).each(function () {
                    var that = $(this);
                    var sel_values  = that[0].split(',') || [];
                    var targets_array = that[1].split(',') || [];

                    // 如果触发的元素是单选，且没有选中则设置值为空
                    if (trigger_item.attr('type') == 'radio' && trigger_item.is(':checked') == false) {
                        trigger_value = '';
                    }

                    if ($.inArray(trigger_value, sel_values) >= 0) {
                        // 符合指定的值，显示对应的表单项
                        for (var index in targets_array) {
                            // 如果不是该对象自身直接创建的属性（也就是该属//性是原型中的属性），则跳过显示
                            if (!targets_array.hasOwnProperty(index)) {
                                continue;
                            }
                            $('#form_group_'+targets_array[index]).show();
                        }
                    } else {
                        for (var item in targets_array) {
                            if (!targets_array.hasOwnProperty(item)) {
                                continue;
                            }
							$('#form_group_'+targets_array[item]).hide();
                        }
                    }
                });
            });

            // 有默认值时触发
            var trigger_value = '';
            if (trigger_form.find('[name='+ trigger +']').attr('type') == 'radio') {
                trigger_value = trigger_form.find('[name='+ trigger +']:checked').val() || '';
                if (trigger_value != '' && $.inArray(trigger_value, field_values_array) >= 0) {
                    var $radio_id = $('.form-trigger [name='+ trigger +']:checked').attr('id');
                    $('.form-trigger #'+$radio_id).trigger("change", ['1']);
                }
            } else {
                trigger_value = trigger_form.find('[name='+ trigger +']').val() || '';
                if (trigger_value != '' && $.inArray(trigger_value, field_values_array) >= 0) {
                    $('.form-trigger [name='+ trigger +']').trigger("change");
                }
            }
        });
    }

});