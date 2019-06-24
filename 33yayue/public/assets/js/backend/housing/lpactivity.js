define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            var id = Config.id.name; 
            Table.api.init({
                extend: {
                    index_url: 'housing/lpactivity/index/ids/'+id,
                    add_url: 'housing/lpactivity/add',
                    edit_url: 'housing/lpactivity/edit',
                    del_url: 'housing/lpactivity/del',
                    multi_url: 'housing/lpactivity/multi',
                    table: 'lpactivity',
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
                        {field: 'KP_LpID', title: __('Kp_lpid'),operate:false},
                        {field: 'lp.KP_LpName', title: '楼盘名称',operate:false},
                        {field: 'KP_Info', title: __('Kp_info'),operate:"LIKE",formatter: Table.api.formatter.maxems},
                        {field: 'KP_AddUser', title: __('Kp_adduser'),operate:false},
                        {field: 'KP_AddTime', title: __('Kp_addtime'),operate:false, addclass:'datetimerange'},
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