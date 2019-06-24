define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'housing/kftbm/index',
                    add_url: 'housing/kftbm/add',
                    edit_url: 'housing/kftbm/edit',
                    del_url: 'housing/kftbm/del',
                    multi_url: 'housing/kftbm/multi',
                    table: 'kftbm',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                commonSearch:false,
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'KP_Type', title: __('Kp_type')},
                        {field: 'KP_Name', title: __('Kp_name')},
                        {field: 'KP_Phone', title: __('Kp_phone')},
                        {field: 'KP_Message', title: __('Kp_message')},
                        {field: 'KP_Url', title: __('Kp_url'), formatter: Table.api.formatter.url},
                        {field: 'KP_Title', title: __('Kp_title')},
                        {field: 'KP_BmTime', title: __('Kp_bmtime'), operate:'RANGE', addclass:'datetimerange'}
                        
                    ]
                ],
                queryParams:function (params) {
                    var types = $("#bmtype").val();
                    var keyword = $.trim($("#keywords").val());
                    var st = $.trim($("#sKP_BmTime").val())?$.trim($("#sKP_BmTime").val()):'2000-01-01';
                    var et = $.trim($("#eKP_BmTime").val())?$.trim($("#eKP_BmTime").val()):'2050-01-01';
                    params.filter = {};
                    if(types!=-1)params.filter.KP_Type = types;
                    if (st!=''||et!='') params.filter.KP_BmTime = st+','+et;
                    params.op = {};
                    params.op.KP_Type = "=";
                    params.op.KP_BmTime = 'BETWEEN';
                    return {search:keyword,sort:params.sort,order:params.order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
                }
            });
            $("#bmtype").change(function(){
                table.bootstrapTable("refresh");
                return false;
            });    
            // 为表格绑定事件
            Table.api.bindevent(table);
            var searchForm = $("[role='form'].form-commonsearch");
            searchForm.submit(function () {
                table.bootstrapTable("refresh");
                return false;
            });
            $("[type='reset']",searchForm).click(function () {
                $("span.placeholder",$("span.city-picker-span")).show();
                $("span.title",$("span.city-picker-span")).hide();
                searchForm[0].reset();
                searchForm.trigger("submit");
                return false;
            });
            Form.events.datetimepicker($("form"));
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