define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'housing/personaltailor/index',
                    add_url: 'housing/personaltailor/add',
                    edit_url: 'housing/personaltailor/edit',
                    del_url: 'housing/personaltailor/del',
                    multi_url: 'housing/personaltailor/multi',
                    table: 'personaltailor',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                commonSearch:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'KP_Yxcity', title: __('Kp_yxcity')},
                        {field: 'KP_Fwlx', title: __('Kp_fwlx')},
                        {field: 'KP_Yxhx', title: __('Kp_yxhx')},
                        {field: 'KP_Zyarea', title: __('Kp_zyarea')},
                        {field: 'KP_Ysjw', title: __('Kp_ysjw')},
                        {field: 'KP_Zxxq', title: __('Kp_zxxq')},
                        {field: 'KP_Fjxq', title: __('Kp_fjxq')},
                        {field: 'KP_Name', title: __('Kp_name')},
                        {field: 'KP_Sex', title: __('Kp_sex')},
                        {field: 'KP_Tel', title: __('Kp_tel')},
                        {field: 'KP_AddTime', title: __('Kp_addtime')},
                        {field: 'KP_Content', title: __('Kp_content')},
                        {field: 'operate', title: __('Operate'), table: table,buttons: [
                                {
                                    name: 'edit',
                                    text: '跟进',
                                    title: '跟进',
                                    classname: 'btn btn-xs btn-success btn-editone',
                                    icon: 'fa fa-pencil',
                                    url:'housing/personaltailor/edit',
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
                                    }
                                }
                            ], events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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