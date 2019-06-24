define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            var id = Config.id.name;
            Table.api.init({
                extend: {
                    index_url: 'housing/lpcomment/index/ids/'+id,
                    add_url: 'housing/lpcomment/add',
                    edit_url: 'housing/lpcomment/edit',
                    del_url: 'housing/lpcomment/del',
                    multi_url: 'housing/lpcomment/multi',
                    table: 'lpcomment',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                commonSearch:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'lp.KP_LpName', title: '楼盘名称',operate:false},  
                        {field: 'KP_KhName', title: __('Kp_khname'),operate:false},
                        {field: 'KP_Phone', title: __('Kp_phone'),operate:false},
                        {field: 'KP_Score', title: __('Kp_score'),operate:false},
                        {field: 'KP_Content', title: __('Kp_content'),operate:'LIKE',formatter: Table.api.formatter.maxems},
                        {field: 'KP_Time', title: __('Kp_time'),operate:false, addclass:'datetimerange'},
                        {field: 'KP_Agree', title: __('Kp_agree'),operate:false},
                        {field: 'KP_Disapprove', title: __('Kp_disapprove'),operate:false},
                        {field: 'KP_Check', title: __('Kp_check'), searchList: {"0":'待审核',"1":'通过',"2":'不通过'},formatter:function(value){
                              if (value == 1) {
                                  return '通过';
                              } else if (value == 2) {
                                  return '不通过';
                              }else{
                                  return '待审核';
                              }
                          }},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams:function (params) {
                    var sh = parseInt($("#sh").val());
                    var keyword = $.trim($("#keyword").val());
                    params.filter = {};
                    if(sh!=-1)params.filter.KP_Check = sh;
                    params.op = {};
                    params.op.KP_Check = "=";
                    params.op.KP_FlagHf = "=";
                    return {search:keyword,sort:params.sort,order:params.order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
                }
            });

            var searchForm = $("[role='form'].form-commonsearch");
            searchForm.submit(function () {
                table.bootstrapTable("refresh");
                return false;
            });
            $("[type='reset']",searchForm).click(function () {
                //$("#zd").val(-1);
                //$("#lm").val(-1);
                $("span.placeholder",$("span.city-picker-span")).show();
                $("span.title",$("span.city-picker-span")).hide();
                searchForm[0].reset();
                searchForm.trigger("submit");
                return false;
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