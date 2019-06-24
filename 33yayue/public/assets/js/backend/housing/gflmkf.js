define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'housing/gflmkf/index',
                    add_url: 'housing/gflmkf/add',
                    edit_url: 'housing/gflmkf/edit',
                    del_url: 'housing/gflmkf/del',
                    multi_url: 'housing/gflmkf/multi',
                    table: 'gflmkf',
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
                        {field: 'zd_text', title: __('Kp_siteid')},
                        {field: 'KP_Name', title: __('Kp_name')},
                        {field: 'KP_Pic', title: __('Kp_pic'), formatter:Table.api.formatter.image},
                        {field: 'KP_Tel', title: __('Kp_tel')},
                        {field: 'KP_Area', title: __('Kp_area')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams:function (params) {
                    //var area_id = parseInt($("#area_id").val());
                    var keywords = $.trim($('#keywords').val());
                    var siteid = parseInt($("#site").val());
                    params.filter = {};
                    if(siteid!=-1)params.filter.KP_SiteID = siteid;
                    params.op = {};
                    params.op.KP_SiteID = "=";
                    return {search:keywords,sort:params.sort,order:params.order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
                }
            });

            /*$("#area_info").on("cp:updated", function() {
                var citypicker = $(this).data("citypicker");
                var code = citypicker.getCode("district") || citypicker.getCode("city") || citypicker.getCode("province");
                $("#area_id").val(code);
            });*/
            // 为表格绑定事件
            Table.api.bindevent(table);
            var searchForm = $("[role='form'].form-commonsearch");
            searchForm.submit(function () {
                table.bootstrapTable("refresh");
                return false;
            });
            $("[type='reset']",searchForm).click(function () {
                $("#area_info").val("");
                $("#area_id").val(0);
                $("span.placeholder",$("span.city-picker-span")).show();
                $("span.title",$("span.city-picker-span")).hide();
                searchForm[0].reset();
                searchForm.trigger("submit");
                return false;
            });
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