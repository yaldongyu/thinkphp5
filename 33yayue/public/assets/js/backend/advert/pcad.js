define(['jquery','cookie', 'bootstrap', 'backend', 'table', 'form'], function ($,undefined, undefined, Backend, Table, Form) {



 
    var Controller = {
        index: function () {
            // 初始化表格参数配置
             if($.cookie('kptype')==0 || $.cookie('kptype')==1 || $.cookie('kptype')==2 || $.cookie('kptype')==3 || $.cookie('kptype')==4 || $.cookie('kptype')==8 || $.cookie('kptype')==9 ){
                $("#KP_TypeSession").val($.cookie('kptype'));  
            }
            $("#0").css("background","#FF8888");
            if($.cookie('kptype')==0   ){$(".fmenu").css("background","");    $("#0").css("background","#FF8888");  }
            if($.cookie('kptype')==1   ){$(".fmenu").css("background","");    $("#1").css("background","#FF8888");  }
            if($.cookie('kptype')==2   ){$(".fmenu").css("background","");    $("#2").css("background","#FF8888");  }
            if($.cookie('kptype')==3   ){$(".fmenu").css("background","");    $("#3").css("background","#FF8888");  }
            if($.cookie('kptype')==4   ){$(".fmenu").css("background","");    $("#4").css("background","#FF8888");  }
            if($.cookie('kptype')==8   ){$(".fmenu").css("background","");   $("#8").css("background","#FF8888");  }
            if($.cookie('kptype')==9   ){$(".fmenu").css("background","");    $("#9").css("background","#FF8888");  }
               
            Table.api.init({
                extend: {
                    index_url: 'advert/pcad/index',
                    add_url: 'advert/pcad/add',
                    edit_url: 'advert/pcad/edit',
                    del_url: 'advert/pcad/del',
                    multi_url: 'advert/pcad/multi',
                    table: 'pcgeneralad',
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
                sortName: 'weigh',
                search:false, 
                showExport: false,//关闭导出功能
                showToggle: true,//关闭显示方式功能
                showColumns: false,//关闭列开关功能
                searchFormVisible: false, //默认显示搜索条件Form
                commonSearch: false, //关闭搜索功能
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id') ,operate: false},
                        {field: 'area_text', title: '所属区域'},
                        {field: 'KP_Title', title: __('Kp_title')},
                        {field: 'KP_PicUrl', title: __('Kp_picurl'), formatter: Table.api.formatter.image, operate: false},
                        {field: 'KP_Link', title: __('Kp_link'),operate: false},
                        {field: 'KP_Switch', title: __('Kp_switch'), formatter: Table.api.formatter.toggle},
                        // {field: 'KP_AddUser', title: __('Kp_adduser')},
                        // {field: 'KP_AddTime', title: __('Kp_addtime'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'KP_EditUser', title: __('Kp_edituser')},
                        // {field: 'KP_EditTime', title: __('Kp_edittime'), operate:'RANGE', addclass:'datetimerange'},
                        // {field: 'KP_Content', title: __('Kp_content')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                 queryParams:function (params) {
                    
                    var keywords = $('#keywords').val();
                    var kptype = parseInt($("#KP_TypeSession").val());
                    var kpswitch = parseInt($("#KP_sw").val());
                    var cityid = parseInt($("#cityid").val());
                    if(cityid>0){
                        kpcity = cityid;
                    }else{
                        kpcity = 0;
                    }
                    params.filter = {};
                    if(kpswitch!=-1)params.filter.KP_Switch = kpswitch;
                    if(keywords!="")params.filter.KP_Title = keywords + "%"  ;
                    
                    params.filter.KP_Type = kptype;
                    params.op = {};
                    params.op.KP_Title = "like";
                    if(kptype==9){params.op.KP_Type = ">=";}else{params.op.KP_Type = "=";} 
                    params.op.KP_Switch = "=";
                    return {kpcity:kpcity,search:'',sort:params.sort,order:params.order,offset:params.offset,limit:params.limit,filter:JSON.stringify(params.filter),op:JSON.stringify(params.op)};
                
                }
            });

// 为表格绑定事件
            Form.events.citypicker($("form"));
            Table.api.bindevent(table);
            var searchForm = $("[role='form'].form-commonsearch");
            searchForm.submit(function () {
                table.bootstrapTable("refresh");
                 return false;
            });
            $("[type='reset']",searchForm).click(function () {
               // $("#site").val("");
               // $("#keywords").val("");
                $("#kp_city").citypicker('reset');
                $("#cityid").val(0);
                searchForm[0].reset();
                searchForm.trigger("submit");
                return false;
            });
            $(".fmenu").click(function (e) {   
                var kptype = e.target.id;        
                $.cookie('kptype', kptype, { expires: 10/24/3600 });
                if(kptype==5   ){ location.href="actad";return false;}
                if(  kptype==6 ){ location.href="actad";return false;}

              $(".fmenu").css("background","");
              $(event.target).css("background","#FF8888");
              //e.target.css("background","lightblue");
              searchForm[0].reset(); 
              $("#KP_TypeSession").val(e.target.id);  

               searchForm.trigger("submit");
               return false;
               });

        },
        add: function () {
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
           $("#c-KP_Type").val( $("#KP_TypeSession",window.parent.document).val());
           if ($("#KP_TypeSession",window.parent.document).val()==0) {
              $("#label").html('大小：1200 x 490');
           }
           Controller.api.bindevent();
        },
        edit: function () {
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
            if ($("#KP_TypeSession",window.parent.document).val()==0) {
              $("#label").html('大小：1200 x 490');
           }
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