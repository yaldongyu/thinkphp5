define(['jquery', 'bootstrap', 'backend', 'table', 'form','selectpage','zoomify.min'], function ($, undefined, Backend, Table, Form,selectpage,undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'groudbuy/lptg/index',
                    add_url: 'groudbuy/lptg/add',
                    edit_url: 'groudbuy/lptg/edit',
                    del_url: 'groudbuy/lptg/del',
                    multi_url: 'groudbuy/lptg/multi',
                    table: 'lptg',
                }
            });




            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "团购标题/楼盘名称..";};
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
                sortName: 'weigh',
                showExport: false,//关闭导出功能
                showToggle: true,//关闭显示方式功能
                showColumns: false,//关闭列开关功能
                searchFormVisible: false, //默认显示搜索条件Form
                commonSearch: false, //关闭搜索功能
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id') ,operate: false},
                        {field: 'area_text', title: '所属区域'},                     
                        {field: 'lp.KP_LpName', title: __('Kp_lpid')},
                        {field: 'KP_PicUrl', title: __('Kp_picurl'), formatter: Table.api.formatter.image, operate: false},
                        {field: 'KP_Title', title: __('Kp_title'),formatter: Table.api.formatter.maxems,operate:false},
                        {field: 'KP_Yhby', title: __('Kp_yhby'),formatter: Table.api.formatter.maxems,operate:false},
                        {field: 'KP_Yhby2', title: __('Kp_yhby2'),formatter: Table.api.formatter.maxems,operate:false},
                        {field: 'KP_Yhxq', title: __('Kp_yhxq'),operate: false,visible: false},
                        {field: 'KP_EndTime', title: __('Kp_endtime'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'KP_Htnr', title: __('Kp_htnr'),visible: false},
                        {field: 'KP_Htzt', title: __('Kp_htzt'),searchList: {"0": __('no'),"1": __('yes')}, formatter: Table.api.formatter.toggle1},
                        {field: 'KP_AddUser', title: __('Kp_adduser'),visible: false},
                        {field: 'KP_AddTime', title: __('Kp_addtime'),visible: false, operate:'RANGE', addclass:'datetimerange'},
                        {field: 'KP_EditUser', title: __('Kp_edituser'),visible: false},
                        {field: 'KP_EditTime', title: __('Kp_edittime'),visible: false, operate:'RANGE', addclass:'datetimerange'},
                        {field: 'KP_Content', title: __('Kp_content'),visible: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams:function (params) {
                    var keyword = $.trim($("#keyword").val());
                    var cityid = parseInt($("#cityid").val());
                    if(cityid>0){
                        kpcity = cityid;
                    }else{
                        kpcity = 0;
                    }
                    params.filter = {};
                    params.op = {};
                    return {kpcity:kpcity,search:keyword,sort:params.sort,order:params.order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
                }
                
            });
            $(".btn-add").data("area", ["100%","100%"]);
            $(".btn-edit").data("area", ["100%","100%"]);
            //当内容渲染完成后
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-editone").data("area", ["100%","100%"]);
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
            var com;
            /*$(document).on("change", "#c-KP_LpID", function () {
                $('#lpid').val($(this).val()); 
                $.ajax({
                        url: 'groudbuy/lptg/getCity',
                        type: 'post',
                        data: {id: $(this).val()},
                        success: function (ret) {
                          $("#c-prov").val(ret.KP_Provice);
                          $("#c-city").val(ret.KP_Citys);
                          $("#c-dist").val(ret.KP_District);
                          $("#c-county").val(ret.KP_County);
                        }
                    });             
            });*/
            $("#c-KP_City").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode("district") || citypicker.getCode("city") || citypicker.getCode("province");
                  $("#c-Cityid").val(code/10000);
                  var codes = citypicker.getCode();
                  var codearr = codes.split('/');
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

            /*$(document).on("change", "#c-KP_LpID", function () {
                $('#lpid').val($(this).val()); 
                $.ajax({
                        url: 'groudbuy/lptg/getCity',
                        type: 'post',
                        data: {id: $(this).val()},
                        success: function (ret) {
                          $("#c-prov").val(ret.KP_Provice);
                          $("#c-city").val(ret.KP_Citys);
                          $("#c-dist").val(ret.KP_District);
                          $("#c-county").val(ret.KP_County);
                        }
                    });             
            });*/

            $("#c-KP_City").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode("district") || citypicker.getCode("city") || citypicker.getCode("province");
                  $("#c-Cityid").val(code/10000);
                  var codes = citypicker.getCode();
                  var codearr = codes.split('/');
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
            /*$("#c-KP_City").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode();
                  var codearr = code.split('/');
                  $("#c-prov").val(codearr[0]/10000);
                  $("#c-city").val(codearr[1]/10000);
                  $("#c-dist").val(codearr[2]/10000);
                  $("#c-county").val(codearr[3]/10000);
            });*/
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                thumb: function (value, row, index) {
                    
                         
                        return '<a href="' + row.fullurl + '" target="_blank"><img src="' + row.fullurl      + '" alt="" style="max-height:90px;max-width:120px"></a>';
                     
                },
                url: function (value, row, index) {
                    return '<a href="' + row.fullurl + '" target="_blank" class="label bg-green">' + value + '</a>';
                },
            }
        }
    };
    return Controller;



    
   
 });