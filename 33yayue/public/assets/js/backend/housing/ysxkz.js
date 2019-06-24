define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            var idd = Config.id.name;
            Table.api.init({
                extend: {
                    index_url: 'housing/ysxkz/index/ids/'+idd,
                    add_url: 'housing/ysxkz/add',
                    edit_url: 'housing/ysxkz/edit',
                    del_url: 'housing/ysxkz/del',
                    multi_url: 'housing/ysxkz/multi',
                    table: 'ysxkz',
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
                        {field: 'id', title: __('ID')},
                        {field: 'KP_Name', title: __('许可证名称')},
                        {field: 'KP_Times', title: __('发证时间')},
                        {field: 'KP_Bdld', title: __('绑定楼盘')},
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