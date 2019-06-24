var ppids;
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'housing/brandlp/index',
                    add_url: 'housing/brandlp/add',
                    edit_url: 'housing/brandlp/edit',
                    del_url: 'housing/brandlp/del',
                    multi_url: 'housing/brandlp/multi',
                    table: 'brandlp',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: 'ID'},
                        {field: 'KP_Name', title: __('Kp_name')},
                        {field: 'KP_Logo', title: __('Kp_logo'), formatter:Table.api.formatter.image,operate:false},
                        {field: 'KP_Brand', title: '集团品牌',formatter: Table.api.formatter.maxems,operate:false},
                        {field: 'KP_Status', title:'状态',formatter: Table.api.formatter.toggle},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#c-KP_City").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
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

            //点击详情
            $('#addbrand').on("click", function () {
                var area = ['900px', '500px'];
                var options = {
                    shadeClose: false,
                    shade: [0.3, '#393D49'],
                    callback:function(value){
                        CallBackFun(value.id, value.name);//在回调函数里可以调用你的业务代码实现前端的各种逻辑和效果
                    }
                };
              //  Fast.api.open(url,msg,options);
                Backend.api.open('housing/brandlp/selectlp' ,"选择楼盘",options);
                Fast.api.layerfooter();
            });
            Controller.api.bindevent();
        },
        selectlp:function(){
          var seldata = new Array();
            Table.api.init({
                extend: {
                    index_url: 'housing/lp/index',
                    table: 'lp',
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
                onCheckAll:function(rows){
                   console.log(rows);       
                },
                onCheck:function(row){
                   if(seldata.length!=6){
                      seldata.push(row)
                   }else{
                      alert('最多选择六个');
                   }       
                },
                onUncheck:function(row){
                  seldata.splice($.inArray(row,seldata), 1);       
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'KP_Xszt', title: __('Kp_xszt'),formatter:function(value){
                              if (value == 0) {return '在售';} else if (value == 1) {return '待售';}else{return '售罄'}},operate:false},
                        {field: 'KP_LpName', title: __('Kp_lpname'),operate:'LIKE'},
                        {field: 'KP_CityName', title:'所在区域'},
                        {field: 'KP_Qiprice', title:'起价'},
                        {field: 'KP_Juprice', title:'均价'},
                    ]
                ],
                queryParams:function (params) {
                    var cityid = parseInt($("#cityid").val());

                    var keyword = '';
                    var rprice = '';
                    var sort = params.sort;
                    var order = params.order;
                    var pflag =$("#tiaojian").val();
                    if (pflag==0) {
                        keyword = $.trim($("#keyword").val());
                    }
                    $("#tiaojian").change(function(){ 
                        keyword = '';       
                        if ($(this).val()==1) {
                            pflag = 1;
                            $("#keyword").attr('placeholder','请输入电话号码查询')
                        }else if($(this).val()==2){
                            pflag = 2;
                            $("#keyword").attr('placeholder','请输入起价查找')    
                        }else if($(this).val()==3){
                            pflag = 3;
                            $("#keyword").attr('placeholder','请输入均价查找')
                        }else{
                            pflag = 0;
                            keyword = $.trim($("#keyword").val());
                            $("#keyword").attr('placeholder','请输入楼盘名称')    
                        }
                    });
                    
                    
                    params.filter = {};
                    if(cityid>0)params.filter.KP_City = cityid;
                    if($("#flag1").is(':checked'))params.filter.KP_Yhz = 1;
                    if($("#flag2").is(':checked'))params.filter.KP_Jjlp = 1;
                    if($("#flag3").is(':checked'))params.filter.KP_Gflm = 1;
                    if($("#flag4").is(':checked'))params.filter.KP_Tj = 1;
                    if($("#flag5").is(':checked'))params.filter.KP_Jz = 1;
                    if(pflag==1)params.filter.KP_Tel = $.trim($("#keyword").val());
                    if(pflag==2){
                        rprice = $.trim($("#keyword").val());
                        sort = 'lpprice.KP_Qiprice';
                        order = "asc";
                    }
                    if(pflag==3){
                        rprice = $.trim($("#keyword").val());
                        sort = 'lpprice.KP_Juprice';
                        order = "asc";
                    }
                    params.op = {};
                    params.op.KP_City = "=";
                    params.op.KP_Yhz = "=";
                    params.op.KP_Jjlp = "=";
                    params.op.KP_Gflm = "=";
                    params.op.KP_Tj = "=";
                    params.op.KP_Jz = "=";
                    params.op.KP_Tel = "=";
                    return {rprice:rprice,price:pflag,search:keyword,sort:sort,order:order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
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
                $("#kp_city").citypicker('reset');
                $("#city").val("");
                $("#cityid").val(0);
                $("span.placeholder",$("span.city-picker-span")).show();
                $("span.title",$("span.city-picker-span")).hide();
                $("#keyword").attr('placeholder','请输入楼盘名称') 
                searchForm[0].reset();
                searchForm.trigger("submit");
                return false;
            });

            $("#sure").on("click", function () {
              console.log(seldata);
              parent.$("#list").html('');
              var ids='';
              for (var i = seldata.length - 1; i >= 0; i--) {
                if (ids=='') {
                  ids = seldata[i]['id'];
                }else{
                  ids = ids+','+seldata[i]['id'];
                }
                parent.$("#list").prepend('<div class="box-header"><h3 class="box-title" style="font-size: 16px;">'+seldata[i]['KP_LpName']+'</h3><div class="box-tools pull-right"><a href="javascript:;" target="_blank" class="btn btn-box-tool"><i class="fa fa-trash"></i></a></div></div>');
              }
              parent.$("#KP_Brand").val(ids); 
            });
            Form.events.citypicker($("form"));
             Controller.api.bindevent();
        },
        edit: function () {
            $("#c-KP_City").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});