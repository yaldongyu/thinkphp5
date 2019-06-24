define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/zw/index',
                    add_url: 'auth/zw/add',
                    edit_url: 'auth/zw/edit',
                    del_url: 'auth/zw/del',
                    multi_url: 'auth/zw/multi',
                    table: 'zw',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'KP_ZwName', title: __('Kp_zwname')},
                        {field: 'KP_ZwLevel', title: __('Kp_zwlevel')},
                        {field: 'KP_ZwWage', title: __('Kp_zwwage'), operate:'BETWEEN'},
                        {field: 'KP_ZwFill', title: __('Kp_zwfill'), operate:'BETWEEN'},
                        {field: 'KP_ZwCommission', title: __('Kp_zwcommission'), operate:'BETWEEN'},
                        {field: 'KP_Px', title: __('Kp_px')},
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