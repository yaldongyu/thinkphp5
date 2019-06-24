define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sysattr/chengjiao/index',
                    add_url: 'sysattr/chengjiao/add',
                    edit_url: 'sysattr/chengjiao/edit',
                    del_url: 'sysattr/chengjiao/del',
                    multi_url: 'sysattr/chengjiao/multi',
                    table: 'chengjiao',
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
                        {field: 'KP_LpName', title: __('项目名称')},
                        {field: 'KP_Hx', title: __('户型')},
                        {field: 'KP_Price', title: __('成交价')},
                        {field: 'KP_Time', title: __('成交时间'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $(document).on("change", "#c-KP_LpID", function () {
                $('#lpid').val($(this).val());
                $('#c-KP_LpName').val($("#c-KP_LpID_text").val()); 
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $(document).on("change", "#c-KP_LpID", function () {
                $('#lpid').val($(this).val());
                $('#c-KP_LpName').val($("#c-KP_LpID_text").val()); 
            });
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