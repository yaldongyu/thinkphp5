define(['jquery', 'bootstrap', 'backend', 'table', 'form','selectpage'], function ($, undefined, Backend, Table, Form,selectpage) {
 
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'zixun/news/index',
                    add_url: 'zixun/news/add',
                    edit_url: 'zixun/news/edit',
                    del_url: 'zixun/news/del',
                    multi_url: 'zixun/news/multi',
                    table: 'news',
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
                sortName: 'KP_EditTime',
                commonSearch:false,
                search:false, 
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'area_text', title: '所属区域'},
                        {field: 'KP_Title', title: __('Kp_title'), align: 'left',formatter: Table.api.formatter.maxemsurl},
                        {field: 'KP_Lm_Name', title: __('Kp_lmid')},
                        {field: 'KP_AddUser', title: __('Kp_adduser')},
                        {field: 'KP_AddTime', title: __('Kp_addtime')},
                        {field: 'KP_Zd', title: '置顶', searchList: {"1":__('Yes'),"0":__('No')},formatter: Table.api.formatter.toggle},
                        {field: 'KP_Ontop', title: '头条', searchList: {"1":__('Yes'),"0":__('No')},formatter: Table.api.formatter.toggle},
                        {field: 'KP_Tj', title: '推荐', searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'KP_Waphd', title: '幻灯片', searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
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
                    if(lm!=-1)params.filter.KP_Lmid = lm;
                    if($("#flag1").is(':checked'))params.filter.KP_Ontop = 1;
                    if($("#flag2").is(':checked'))params.filter.KP_Tj = 1;
                    if($("#flag3").is(':checked'))params.filter.KP_Waphd = 1;
                    if($("#flag4").is(':checked'))params.filter.KP_PicUrl = '';
                    params.op = {};
                    params.op.KP_Lmid = "=";
                    params.op.KP_Ontop = "=";
                    params.op.KP_Tj = "=";
                    params.op.KP_Waphd = "=";
                    params.op.KP_PicUrl = "=";
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
                //$("#zd").val(-1);
                //$("#lm").val(-1);
                $("#kp_city").citypicker('reset');
                $("#cityid").val(0);
                $("span.placeholder",$("span.city-picker-span")).show();
                $("span.title",$("span.city-picker-span")).hide();
                searchForm[0].reset();
                searchForm.trigger("submit");
                return false;
            });

            //点击详情
            $(document).on("click", ".btn-detail[data-id]", function () {
                var area = ['400px', '700px'];
                var options = {
                    shadeClose: false,
                    shade: [0.3, '#393D49'],
                    area: area,
                    callback:function(value){
                        CallBackFun(value.id, value.name);//在回调函数里可以调用你的业务代码实现前端的各种逻辑和效果
                    }
                };
              //  Fast.api.open(url,msg,options);
                var host = window.location.host;
                var mhost = host.replace('yfadmin', "m");
                Backend.api.open('http://'+mhost+'/news/detail/ids/' + $(this).data('id') ,'详情',options);
                Fast.api.layerfooter();
            });
            $(".btn-add").data("area", ["100%","100%"]);
            $(".btn-edit").data("area", ["100%","100%"]);
            //当内容渲染完成后
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                $(".btn-editone").data("area", ["100%","100%"]);
            });
            Form.events.citypicker($("form"));
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $(document).on("change", "#c-KP_LpID", function () {
                $('#lpid').val($(this).val());              
            });
            /*$('#c-KP_LpID').selectPage({
                showField : 'KP_LpName',
                keyField : 'id',
                multiple : false,
                pagination:true,
                pageSize:10,
                data:'housing/lp/selectpage',
                //选中项目后的回调处理
                //data：选中行的原始数据对象
                eAjaxSuccess: function (d) {
                        var result;
                        if (d) result = d;
                        else result = undefined;
                        return result;
                    },
                eSelect : function(data){
                    $('#lpid').val(data.id);
                }
            });*/
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
                Form.api.bindevent($("form[role=form]"),null,null,function () {
                    var ue = UE.getEditor('c-KP_Nr',null);
                    if (!Trim($('#c-KP_Description').val(),'g')) {
                        $("#KP_desc").val(subString(Trim(ue.getContentTxt(),'g'),240,false));
                    }else{
                        $("#KP_desc").val($('#c-KP_Description').val());
                    }
                    return true;
                });

                function subString(str, len, hasDot) {
                    var newLength = 0;
                    var newStr = "";
                    var chineseRegex = /[^\x00-\xff]/g;
                    var singleChar = "";
                    var strLength = str.replace(chineseRegex, "**").length;
                    for (var i = 0; i < strLength; i++) {
                        singleChar = str.charAt(i).toString();
                        if (singleChar.match(chineseRegex) != null) {
                            newLength += 2;
                        }
                        else {
                            newLength++;
                        }
                        if (newLength > len) {
                            break;
                        }
                        newStr += singleChar;
                    }

                    if (hasDot && strLength > len) {
                        newStr += "...";
                    }
                    return newStr;
                }

                 function Trim(str,is_global)
                  {
                   var result;
                   result = str.replace(/(^\s+)|(\s+$)/g,"");
                   if(is_global.toLowerCase()=="g")
                   {
                    result = result.replace(/\s/g,"");
                    }
                   return result;
                }
            }
        }
    };
    return Controller;
});