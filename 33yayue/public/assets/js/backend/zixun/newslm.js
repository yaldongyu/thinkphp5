define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "ID/栏目名称";};
            Table.config.dragsortfield = 'KP_Px';
            Table.api.init({
                extend: {
                    index_url: 'zixun/newslm/index',
                    add_url: 'zixun/newslm/add',
                    edit_url: 'zixun/newslm/edit',
                    del_url: 'zixun/newslm/del',
                    dragsort_url: 'zixun/newslm/weigh',
                    multi_url: 'zixun/newslm/multi',
                    table: 'newslm',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'KP_Px',
                commonSearch:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title:'ID'},
                        {field: 'KP_Name', title: __('Kp_name')},
                        {field: 'KP_Cfml', title: __('Kp_cfml')},
                        {field: 'KP_AddTime', title: __('Kp_addtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'KP_Stutas', title: '开关', searchList: {"1":__('Yes'),"0":__('No')},formatter: Table.api.formatter.toggle},
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