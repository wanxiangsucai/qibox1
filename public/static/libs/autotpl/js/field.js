

jQuery(function () {
    // 字段定义列表
    var $field_define_list = {
        text: "varchar(128) NOT NULL",
        textarea: "text NOT NULL",
        static: "varchar(128) NOT NULL",
        password: "varchar(128) NOT NULL",
        checkbox: "varchar(32) NOT NULL",
        radio: "varchar(32) NOT NULL",
        date: "int(11) UNSIGNED NOT NULL",
        time: "int(11) UNSIGNED NOT NULL",
        datetime: "int(11) UNSIGNED NOT NULL",
        hidden: "varchar(32) NOT NULL",
        switch: "varchar(16) NOT NULL",
        array: "varchar(255) NOT NULL",
        select: "varchar(32) NOT NULL",
        linkage: "varchar(32) NOT NULL",
        linkages: "varchar(32) NOT NULL",
        image: "varchar(60) NOT NULL",
        images: "text NOT NULL",
		images2: "text NOT NULL",
        file: "varchar(128) NOT NULL",
        files: "varchar(255) NOT NULL",
        ueditor: "text NOT NULL",
        wangeditor: "text NOT NULL",
        editormd: "text NOT NULL",
        ckeditor: "text NOT NULL",
        summernote: "text NOT NULL",
        icon: "varchar(64) NOT NULL",
        tags: "varchar(128) NOT NULL",
        number: "int(11) UNSIGNED NOT NULL",
        money: "decimal(10, 2 ) UNSIGNED NOT NULL",
        bmap: "varchar(32) NOT NULL",
        colorpicker: "varchar(32) NOT NULL",
        jcrop: "varchar(128) NOT NULL",
        masked: "varchar(64) NOT NULL",
        range: "varchar(128) NOT NULL"
    };
    // 选择自动类型，自动填写字段定义
    var $field_define = jQuery('input[name=field_type]');
    jQuery('select[name=type]').change(function () {
        $field_define.val($field_define_list[$(this).val()] || '');
    });
});