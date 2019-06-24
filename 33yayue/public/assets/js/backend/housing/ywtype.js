define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'housing/ywtype/index',
                    add_url: 'housing/ywtype/add',
                    edit_url: 'housing/ywtype/edit',
                    del_url: 'housing/ywtype/del',
                    multi_url: 'housing/ywtype/multi',
                    table: 'ywtype',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'KP_Type', title: __('Kp_type'), searchList: {"0":'住宅类型',"1":'特色类型',"2":'价格范围',"3":'建筑类型',"3":'户型类型'},formatter:function(value){
                              if (value == 0) {
                                  return '住宅类型';
                              } else if (value == 1) {
                                  return '特色类型';
                              }
                               else if (value == 2) {
                                  return '价格范围';
                              }
                               else if (value == 3) {
                                  return '建筑类型';
                              }
                              else if (value == 4) {
                                  return '户型类型';
                              }
                          }},
                        {field: 'KP_Name', title: __('Kp_name'),operate:'LIKE'},
                        {field: 'KP_AddTime', title: '创建时间', operate:false, addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#tiaojian").change(function(){ 
                if ($(this).val()==2||$(this).val()==4) {
                    $(".KP_Content").show();
                }else{
                    $(".KP_Content").hide();
                }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#tiaojian").change(function(){ 
                if ($(this).val()==2||$(this).val()==4) {
                    $(".KP_Content").show();
                }else{
                    $(".KP_Content").hide();
                }
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