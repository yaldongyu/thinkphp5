define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'housing/lp/index',
                    add_url: 'housing/lp/add',
                    edit_url: 'housing/lp/edit',
                    del_url: 'housing/lp/del',
                    multi_url: 'housing/lp/multi',
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
                sortName: 'KP_EditTime',
                commonSearch:false,
                paginationShowPageGo: true,
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'KP_Xszt', title: __('Kp_xszt'),formatter:function(value){
                              if (value == 0) {return '<span style="color:#4ebe1d">在售<span>';} else if (value == 1) {return '<span style="color:#d81e06">待售</span>';}else{return '<span style="color:#8a8a8a">售罄</span>'}},operate:false},
                        {field: 'KP_LpName', title: __('Kp_lpname'),operate:'LIKE'},
                        {field: 'KP_CityName', title:'所在区域'},
                        {field: 'KP_Qiprice', title:'起价'},
                        {field: 'KP_Juprice', title:'均价'},
                        {field: 'none', title:'项目简介',table: table, buttons: [
                            {name: 'name11', text: '简介', title: '项目简介', icon: 'fa fa-mouse-pointer', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/lpcontent/index', callback:function(data){}},
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'none', title: '价格',table: table, buttons: [
                            {name: 'name11', text: '价格', title: '价格趋势', icon: 'fa fa-mouse-pointer', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/lpprice/index', callback:function(data){}},
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'none', title:'户型',table: table, buttons: [
                            {name: 'name31', text: '楼盘户型', title: '户型', icon: 'fa fa-flash', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/lphxpic/index'}
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'none', title: '相册',table: table, buttons: [
                            {name: 'name31', text: '楼盘相册', title: '相册', icon: 'fa fa-flash', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/lpalbum/index'}
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'none', title: '问答',table: table, buttons: [
                            {name: 'name11', text: '问答', title: '咨询问答', icon: 'fa fa-mouse-pointer', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/lpreview/index', callback:function(data){}},
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'none', title:'活动',table: table, buttons: [
                            {name: 'name11', text: '活动', title: '楼盘活动', icon: 'fa fa-mouse-pointer', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/lpactivity/index', callback:function(data){}},
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'none', title:'点评',table: table, buttons: [
                            {name: 'name11', text: '点评', title: '客户点评', icon: 'fa fa-mouse-pointer', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/lpcomment/index', callback:function(data){}},
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'none', title:'预售许可证',table: table, buttons: [
                            {name: 'name11', text: '预售许可证', title: '预售许可证', icon: 'fa fa-mouse-pointer', classname: 'btn btn-xs btn-primary btn-dialog', url: 'housing/ysxkz/index', callback:function(data){}},
                        ], operate:false, formatter: Table.api.formatter.buttons},
                        {field: 'KP_Top', title: '置顶', searchList: {"1":__('Yes'),"0":__('No')},formatter: Table.api.formatter.toggle},
                        {field: 'KP_Yhz', title: '热销', searchList: {"1":__('Yes'),"0":__('No')},formatter: Table.api.formatter.toggle},
                        {field: 'KP_Jjlp', title: '降价', searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'KP_Gflm', title: '联盟', searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'KP_Tj', title: '推荐', searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'KP_Jz', title: '下架', searchList: {"1":__('Yes'),"0":__('No')}, formatter: Table.api.formatter.toggle},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                queryParams:function (params) {
                    /*var options = table.bootstrapTable('getOptions');
                    options.pageNumber = 1;
                    options.queryParams = function (params) {
                        return {
                            search: params.search,
                            sort: params.sort,
                            order: params.order,
                            filter: JSON.stringify({category_id: 1}),
                            op: JSON.stringify({category_id: '='}),
                            offset: params.offset,
                            limit: params.limit,
                        };
                    };*/
                    //var area_id = parseInt($("#area_id").val());
                    var cityid = parseInt($("#cityid").val());
                    var xsstatus = parseInt($("#xsstatus").val());
                    var jgupdate = parseInt($("#jgupdate").val());
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
                    if(cityid>0){
                        kpcity = cityid;
                    }else{
                        kpcity = 0;
                    }
                    if(jgupdate!=-1){
                        pflag = 4;
                        if (jgupdate==0) {
                            sort = 'lpprice.KP_EditTime';
                            order = "desc";
                        }else{
                            sort = 'lpprice.KP_EditTime';
                            order = "asc";
                        }
                        
                    }
                    if(xsstatus!=-1)params.filter.KP_Xszt = xsstatus;
                    if($("#flag1").is(':checked'))params.filter.KP_Yhz = 1;
                    if($("#flag2").is(':checked'))params.filter.KP_Jjlp = 1;
                    if($("#flag3").is(':checked'))params.filter.KP_Gflm = 1;
                    if($("#flag4").is(':checked'))params.filter.KP_Tj = 1;
                    if($("#flag5").is(':checked'))params.filter.KP_Jz = 1;
                    if($("#flag6").is(':checked'))params.filter.KP_Top = 1;
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
                    params.op.KP_Xszt = "=";
                    params.op.KP_Yhz = "=";
                    params.op.KP_Jjlp = "=";
                    params.op.KP_Gflm = "=";
                    params.op.KP_Tj = "=";
                    params.op.KP_Jz = "=";
                    params.op.KP_Top = "=";
                    params.op.KP_Tel = "=";
                    return {kpcity:kpcity,rprice:rprice,price:pflag,search:keyword,sort:sort,order:order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
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
            Form.events.citypicker($("form"));
        },
        add: function () {
             $("#c-KP_LpName").bind("input propertychange",function(event){
                $.ajax({
                    url: 'housing/lp/pinyin',
                    type: 'post',
                    data: {str: $("#c-KP_LpName").val()},
                    success: function (ret) {
                        $("#c-KP_Lppinyin").val(ret.quanpin);
                        $("#c-KP_Lppinyins").val(ret.jianpin);
                    }
                });
            });
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
            $("[data-toggle='addresspicker']").data("callback", function(res){
                $(this).val(res.lng+","+res.lat);
            });
            Controller.api.bindevent();
        },
        edit: function () { 
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
            $("#c-KP_LpName").bind("input propertychange",function(event){
                $.ajax({
                    url: 'housing/lp/pinyin',
                    type: 'post',
                    data: {str: $("#c-KP_LpName").val()},
                    success: function (ret) {
                        $("#c-KP_Lppinyin").val(ret.quanpin);
                        $("#c-KP_Lppinyins").val(ret.jianpin);
                    }
                });
            });
            $("[data-toggle='addresspicker']").data("callback", function(res){
                $(this).val(res.lng+","+res.lat);
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