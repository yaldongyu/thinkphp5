define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            var  CjIDs = Config.id.name;
           // $("#c-KP_Type").val( $("#KP_TypeSession",window.parent.document).val());
            $("#CjIDs").val(CjIDs );
             
            Table.api.init({
                extend: {
                    index_url: 'sysattr/citycj/index/ids/'+CjIDs,
                    add_url: 'sysattr/citycj/add',
                    edit_url: 'sysattr/citycj/edit',
                    del_url: 'sysattr/citycj/del',
                    multi_url: 'sysattr/citycj/multi',
                    table: 'citycjdata',
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
                        {field: 'KP_CjID', title: __('Kp_cjid')},
                        {field: 'KP_City', title: __('Kp_city')},
                        {field: 'KP_Qyts', title: __('Kp_qyts')},
                        {field: 'KP_Area', title: __('Kp_area'), operate:'BETWEEN'},
                        {field: 'KP_JuPrice', title: __('Kp_juprice'), operate:'BETWEEN'},
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
            $("#c-KP_CjID").val( $("#CjIDs",window.parent.document).val());
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