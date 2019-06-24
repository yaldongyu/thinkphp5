define(['jquery', 'bootstrap', 'backend', 'table', 'form','selectpage'], function ($, undefined, Backend, Table, Form,selectpage) {
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'housing/haozhai/index',
                    add_url: 'housing/haozhai/add',
                    edit_url: 'housing/haozhai/edit',
                    del_url: 'housing/haozhai/del',
                    multi_url: 'housing/haozhai/multi',
                    table: 'haozhai',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                search:false,
                searchFormVisible: true,
                columns: [ 
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'KP_PicUrl', title: __('Kp_picurl'), formatter:Table.api.formatter.image,operate:false},
                        {field: 'lp.KP_LpName', title: '楼盘名称',operate:'LIKE'},                     
                        {field: 'KP_Ms', title: __('Kp_ms'),formatter: Table.api.formatter.maxems,operate:false},
                        {field: 'KP_Name', title: __('Kp_name'),operate:false},
                        {field: 'KP_Fcyear', title: __('Kp_fcyear'),operate:false},
                        {field: 'KP_Url', title: __('Kp_url'), formatter: Table.api.formatter.url,operate:false},
                        {field: 'KP_Tel', title: __('Kp_tel'),operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
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