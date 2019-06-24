define(['jquery', 'bootstrap', 'backend', 'table', 'form','selectpage'], function ($, undefined, Backend, Table, Form,selectpage) {
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'zixun/video/index',
                    add_url: 'zixun/video/add',
                    edit_url: 'zixun/video/edit',
                    del_url: 'zixun/video/del',
                    multi_url: 'zixun/video/multi',
                    table: 'video',
                }
            });

            var table = $("#table");
            $("#kp_city").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode("county")||citypicker.getCode("district") || citypicker.getCode("city") || citypicker.getCode("province");
                  $("#cityid").val(code/10000);
                  
            }); 
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
                        {field: 'area_text', title: '所属区域'},
                        {field: 'KP_Title', title: __('Kp_title'), align: 'left',formatter:function(value, row, index){
                            var lpname = ""
                            if (row.lp.KP_LpName) {
                                lpname = '['+row.lp.KP_LpName+']'
                            }
                            return '<span style="color:#B48443;">'+lpname+'</span><a href="javascript:;" style="color:#444;" class="btn-detail"data-id="'+row.id+'" title="' + __('%s', value) + '" data-field="' + this.field + '" data-value="' + value + '">' + value ;
                        }},
                        {field: 'KP_Type_text', title: __('Kp_type')},
                        {field: 'KP_VideoUrl', title: __('Kp_videourl'), formatter: Table.api.formatter.url},
                        {field: 'KP_Cs', title: __('Kp_cs')},
                        {field: 'KP_AddUser', title: __('Kp_adduser')},
                        {field: 'KP_AddTime', title: __('Kp_addtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams:function (params) {
                    var lm = parseInt($("#lm").val());
                    var keyword = $.trim($("#keyword").val());
                    var cityid = parseInt($("#cityid").val());
                    if(cityid>0){
                        kpcity = cityid;
                    }else{
                        kpcity = 0;
                    }
                    params.filter = {};
                    if(lm!=-1)params.filter.KP_Type = lm;
                    params.op = {};
                    params.op.KP_Type = "=";
                    return {kpcity:kpcity,search:keyword,sort:params.sort,order:params.order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
                }
            });
            // 为表格绑定事件
            var searchForm = $("[role='form'].form-commonsearch");
            searchForm.submit(function () {
                table.bootstrapTable("refresh");
                return false;
            });
            $("[type='reset']",searchForm).click(function () {
                $("#kp_city").citypicker('reset');
                $("#cityid").val(0);
                $("span.placeholder",$("span.city-picker-span")).show();
                $("span.title",$("span.city-picker-span")).hide();
                searchForm[0].reset();
                searchForm.trigger("submit");
                return false;
            });
            // 为表格绑定事件
            Form.events.citypicker($("form"));
            Table.api.bindevent(table);
        },
        add: function () {
            $(document).on("change", "#c-KP_LpID", function () {
                $('#lpid').val($(this).val());              
            });
            $("#c-KP_City").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode();
                  var codearr = code.split('/');
                  $("#c-prov").val(codearr[0]/10000);
                  if (codearr.length>1) {
                     $("#c-city").val(codearr[1]/10000);
                    }else{
                        $("#c-city").val(0);
                    }
                      if (codearr.length>2) {
                         $("#c-dist").val(codearr[2]/10000);
                    }else{
                        $("#c-dist").val(0);
                    }
                    if (codearr.length>3) {
                         $("#c-county").val(codearr[3]/10000);
                    }else{
                        $("#c-county").val(0);
                    }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $(document).on("change", "#c-KP_LpID", function () {
                $('#lpid').val($(this).val());              
            });
            $("#c-KP_City").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode();
                  var codearr = code.split('/');
                  $("#c-prov").val(codearr[0]/10000);
                  if (codearr.length>1) {
                     $("#c-city").val(codearr[1]/10000);
                    }else{
                        $("#c-city").val(0);
                    }
                      if (codearr.length>2) {
                         $("#c-dist").val(codearr[2]/10000);
                    }else{
                        $("#c-dist").val(0);
                    }
                    if (codearr.length>3) {
                         $("#c-county").val(codearr[3]/10000);
                    }else{
                        $("#c-county").val(0);
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