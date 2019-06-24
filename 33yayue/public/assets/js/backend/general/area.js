define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template', 'editable'], function ($, undefined, Backend, Table, Form, Template, undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/area/index',
                    add_url: 'general/area/add',
                    edit_url: 'general/area/edit',
                    del_url: 'general/area/del',
                    dragsort_url: '',
                    multi_url: 'general/area/multi',
                    table: 'area',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'aid',
                sortName: 'weigh',
                escape: false,
                pagination:false,
                columns: [
                    [
                        {field: 'state', checkbox: true,},
                        {field: 'aid', title: 'ID'},
                        {field: 'title', title: __('Title'), align: 'left', formatter: Controller.api.formatter.title},
                        {field: 'domain_text', title: "域名"},
                        {field: 'provjuprice', title: "上月均价"},
                        {field: 'curjuprice', title: "本月均价"},
                        {field: 'weigh', title: "weigh",editable: true},

                        {field: 'status', title: '状态', searchList: {"1":'显示',"0":'隐藏'},formatter: Controller.api.formatter.toggle},
                        {field: 'ishot', title: '热门',searchList: {"1":'显示',"0":'隐藏'},formatter: Controller.api.formatter.toggle},
                        {
                            field: 'id',
                            title: '<a href="javascript:;" class="btn btn-success btn-xs btn-toggle"><i class="fa fa-chevron-up"></i></a>',
                            operate: false,
                            formatter: Controller.api.formatter.subnode
                        },
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
                    ]

                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            //当内容渲染完成后
            table.on('post-body.bs.table', function (e, settings, json, xhr) {
                //默认隐藏所有子节点
                //$("a.btn[data-id][data-pid][data-pid!=0]").closest("tr").hide();
                $(".btn-node-sub.disabled").closest("tr").show();

                //显示隐藏子节点
                $(".btn-node-sub").off("click").on("click", function (e) {
                    var status = $(this).data("shown") ? true : false;
                    $("a.btn[data-pid='" + $(this).data("id") + "']").each(function () {
                        $(this).closest("tr").toggle(!status);
                    });
                    $(this).data("shown", !status);
                    return false;
                });
                $(".btn-change[data-id],.btn-delone,.btn-dragsort").data("success", function (data, ret) {
                    Fast.api.refreshmenu();
                });

            });
            //批量删除后的回调
            $(".toolbar > .btn-del,.toolbar .btn-more~ul>li>a").data("success", function (e) {
                Fast.api.refreshmenu();
            });
            //展开隐藏一级
            $(document.body).on("click", ".btn-toggle", function (e) {
                $("a.btn[data-id][data-pid][data-pid!=0].disabled").closest("tr").hide();
                var that = this;
                var show = $("i", that).hasClass("fa-chevron-down");
                $("i", that).toggleClass("fa-chevron-down", !show);
                $("i", that).toggleClass("fa-chevron-up", show);
                $("a.btn[data-id][data-pid][data-pid!=0]").not('.disabled').closest("tr").toggle(show);
                $(".btn-node-sub[data-pid=0]").data("shown", show);
            });
            //展开隐藏全部
            $(document.body).on("click", ".btn-toggle-all", function (e) {
                var that = this;
                var show = $("i", that).hasClass("fa-plus");
                $("i", that).toggleClass("fa-plus", !show);
                $("i", that).toggleClass("fa-minus", show);
                $(".btn-node-sub.disabled").closest("tr").toggle(show);
                $(".btn-node-sub").data("shown", show);
            });
        },
        add: function () {
            $("#c-KP_Area").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode();
                  var text = citypicker.getVal();
                  var textarr = text.split('/');
                  var codearr = code.split('/');
                  $("#c-areaid").val(codearr[codearr.length-1]/10000);
                  if (codearr[codearr.length-2]) {
                    $("#c-areapid").val(codearr[codearr.length-2]/10000);
                  }
                  
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
                  $("#c-name").val(textarr[textarr.length-1]);
                    $.ajax({
                        url: 'general/area/pinyin',
                        type: 'post',
                        data: {str:textarr[textarr.length-1]},
                        success: function (ret) {
                            $("#c-domain").val(ret.quanpin);
                        }
                    });
            });
            Controller.api.bindevent();
        },
        edit: function () {
            $("#c-KP_Area").on("cp:updated", function() {
                  var citypicker = $(this).data("citypicker");
                  var code = citypicker.getCode();
                  var text = citypicker.getVal();
                  var textarr = text.split('/');
                  var codearr = code.split('/');
                  $("#c-areaid").val(codearr[codearr.length-1]/10000);
                  if (codearr[codearr.length-2]) {
                    $("#c-areapid").val(codearr[codearr.length-2]/10000);
                  }
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
                  $("#c-name").val(textarr[textarr.length-1]);
                    $.ajax({
                        url: 'general/area/pinyin',
                        type: 'post',
                        data: {str:textarr[textarr.length-1]},
                        success: function (ret) {
                            $("#c-domain").val(ret.quanpin);
                        }
                    });
            });
            Controller.api.bindevent();
        },
        api: {
          formatter: {
                title: function (value, row, index) {
                    return !row.ismenu || row.status == 'hidden' ? "<span class='text-muted'>" + value + "</span>" : value;
                },
                name: function (value, row, index) {
                    return !row.ismenu || row.status == 'hidden' ? "<span class='text-muted'>" + value + "</span>" : value;
                },
                icon: function (value, row, index) {
                    return '<span class="' + (!row.ismenu || row.status == 'hidden' ? 'text-muted' : '') + '"><i class="' + value + '"></i></span>';
                },
                subnode: function (value, row, index) {
                    return '<a href="javascript:;" title="' + __('Toggle sub menu') + '" data-id="' + row.id + '" data-pid="' + row.pid + '" class="btn btn-xs '
                        + (row.haschild == 1 || row.ismenu == 1 ? 'btn-success' : 'btn-default disabled') + ' btn-node-sub"><i class="fa fa-sitemap"></i></a>';
                },
                toggle: function (value, row, index) {
                    var color = typeof this.color !== 'undefined' ? this.color : 'success';
                    var yes = typeof this.yes !== 'undefined' ? this.yes : 1;
                    var no = typeof this.no !== 'undefined' ? this.no : 0;
                    return "<a href='javascript:;' data-toggle='tooltip' title='" + __('Click to toggle') + "' class='btn-change' data-id='"
                        + row.aid + "' data-params='" + this.field + "=" + (value == yes ? no : yes) + "'><i class='fa fa-toggle-on " + (value == yes ? 'text-' + color : 'fa-flip-horizontal text-gray') + " fa-2x'></i></a>";
                }
            },
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));

            }
        }
    };
    return Controller;
});