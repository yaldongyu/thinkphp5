define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "姓名/电话...";};
            Table.api.init({
                extend: {
                    index_url: 'housing/unioncomment/index',
                    add_url: 'housing/unioncomment/add',
                    edit_url: 'housing/unioncomment/edit',
                    del_url: 'housing/unioncomment/del',
                    multi_url: 'housing/unioncomment/multi',
                    table: 'unioncomment',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                commonSearch: false,
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'zd_text', title: __('Kp_siteid'),operate:false},
                        {field: 'KP_KhName', title: __('Kp_khname'),operate:false},
                        {field: 'KP_KhPhone', title: __('Kp_khphone'),operate:false},
                        {field: 'KP_Content', title: __('Kp_content'),formatter: Table.api.formatter.maxems,operate:false},
                        {field: 'KP_Time', title: __('Kp_time'), operate:'RANGE', addclass:'datetimerange',operate:false},
                        {field: 'KP_ServiceName', title: __('Kp_servicename'),operate:false},
                        {field: 'KP_ServiceTel', title: __('Kp_servicetel'),operate:false},
                        {field: 'KP_Check', title: __('Kp_check'),formatter:function(value){
                              if (value == 1) {
                                  return '通过';
                              } else if (value == 2) {
                                  return '不通过';
                              }else{
                                  return '待审核';
                              }
                          },operate:false},
                        {field: 'KP_FlagHf', title: __('Kp_flaghf'),formatter:function(value){
                              if (value == 0) {
                                  return '待回复';
                              } else if (value == 1) {
                                  return '已回复';
                              }
                          },operate:false},
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