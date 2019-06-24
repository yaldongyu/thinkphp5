define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
 
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysattr/housecj/index',
                    add_url: 'sysattr/housecj/add',
                    edit_url: 'sysattr/housecj/edit',
                    del_url: 'sysattr/housecj/del',
                    multi_url: 'sysattr/housecj/multi',
                    table: 'housecjdata',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false, 
                showExport: false,//关闭导出功能
                showToggle: true,//关闭显示方式功能
                showColumns: false,//关闭列开关功能
                searchFormVisible: false, //默认显示搜索条件Form
                commonSearch: false, //关闭搜索功能
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'KP_Type', title: __('Kp_type')},
                        {field: 'KP_PicUrl', title: __('Kp_picurl'),   formatter: Table.api.formatter.image, operate: false},
                        {field: 'none', title:__('Kp_qysj'),table: table, buttons: [
                            {name: 'name11', text: __('Kp_qysj'), title: __('Kp_qysj'), icon: 'fa  ', 
                            classname: 'btn btn-xs btn-primary btn-dialog', url: 'sysattr/citycj/index', 
                            callback:function(data){}},], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'KP_AddUser', title: __('Kp_adduser')},
                        {field: 'KP_AddTime', title: __('Kp_addtime'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'KP_EditUser', title: __('Kp_edituser')},
                        // {field: 'KP_EditTime', title: __('Kp_edittime'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'KP_Content', title: __('Kp_content')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});