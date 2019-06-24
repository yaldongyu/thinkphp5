define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.config.dragsortfield = 'KP_Px';
            Table.api.init({
                extend: {
                    index_url: 'sysattr/links/index',
                    add_url: 'sysattr/links/add',
                    edit_url: 'sysattr/links/edit',
                    del_url: 'sysattr/links/del',
                    dragsort_url: 'sysattr/links/weigh',
                    multi_url: 'sysattr/links/multi',
                    table: 'links',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'KP_Px',
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
                        {field: 'KP_Title', title: __('Kp_title')},
                        {field: 'KP_Url', title: __('Kp_url') },
                        {field: 'KP_Sh', title: __('Kp_sh')},
                        // {field: 'KP_Px', title: __('Kp_px')},
                        {field: 'KP_AddUser', title: __('Kp_adduser')},
                        {field: 'KP_AddTime', title: __('Kp_addtime'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'KP_EditUser', title: __('Kp_edituser')},
                        // {field: 'KP_EditTime', title: __('Kp_edittime'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'KP_Note', title: __('Kp_note')},
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