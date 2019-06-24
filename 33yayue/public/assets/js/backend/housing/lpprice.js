define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            var idd = Config.id.name;
            Table.api.init({
                extend: {
                    index_url: 'housing/lpprice/index/ids/'+idd,
                    add_url: 'housing/lpprice/add',
                    edit_url: 'housing/lpprice/edit',
                    del_url: 'housing/lpprice/del',
                    multi_url: 'housing/lpprice/multi',
                    table: 'lpprice',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                searchFormVisible:true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'KP_JgTime', title: __('Kp_jgtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass:'datetimerange'},
                        {field: 'KP_Qiprice', title: __('Kp_qiprice'),formatter:function(value){
                              return '<span style="color:#1296db;font-size:15px;">'+value+'<span>';}, operate:false},
                        {field: 'KP_Juprice', title: __('Kp_juprice'),formatter:function(value){
                              return '<span style="color:#1296db;font-size:15px;">'+value+'<span>';}, operate:false},
                        {field: 'KP_ValidityStart', title: __('Kp_validitystart'), operate:false, addclass:'datetimerange', data: 'data-date-format="YYYY-MM-DD"'},
                        {field: 'KP_ValidityEnd', title: __('Kp_validityend'), operate:false, addclass:'datetimerange', data: 'data-date-format="YYYY-MM-DD"'},
                        {field: 'KP_AddUser', title: __('Kp_adduser'), operate:false},
                        {field: 'KP_AddTime', title: __('Kp_addtime'), operate:false, addclass:'datetimerange'},
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