define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'jstree'], function ($, undefined, Backend, Table, Form, undefined) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/admin/index',
                    add_url: 'auth/admin/add',
                    edit_url: 'auth/admin/edit',
                    del_url: 'auth/admin/del',
                    multi_url: 'auth/admin/multi',
                    table: 'admin',
                }
            }); 

            var table = $("#table");
            //在表格内容渲染完成后回调的事件
            table.on('post-body.bs.table', function (e, json) {
                $("tbody tr[data-index]", this).each(function () {
                    if (parseInt($("td:eq(1)", this).text()) == Config.admin.id) {
                        $("input[type=checkbox]", this).prop("disabled", true);
                    }
                });
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'groups_text', title: __('Group'), operate:false, formatter: Table.api.formatter.label},
                        {field: 'Bm_text', title: '部门'},
                        {field: 'nickname', title: '名称'},
                        {field: 'KP_Loginid', title:'工号'},
                        {field: 'zw_text', title:'职务'},
                        {field: 'KP_ZaiZhi', title:'是否在职',formatter:function(value){
                            if (value == 1) {
                                  return '离职';
                              } else{
                                  return '在职';
                              }
                        }},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,  formatter: function (value, row, index) {
                                if(row.id == Config.admin.id){
                                    return '';
                                }
                                return Table.api.formatter.operate.call(this, value, row, index);
                            }}
                    ]
                ]
            });
            $.ajax({
                        url: "auth/admin/roletree",
                        type: 'post',
                        dataType: 'json',
                        data: {},
                        success: function (ret) {
                            if (ret.hasOwnProperty("code")) {
                                var data = ret.hasOwnProperty("data") && ret.data != "" ? ret.data : "";
                                if (ret.code === 1) {
                                    //销毁已有的节点树
                                    $("#treeview").jstree("destroy");
                                    Controller.api.rendertree(data);
                                } else {
                                    Backend.api.toastr.error(ret.data);
                                }
                            }
                        }, error: function (e) {
                            Backend.api.toastr.error(e.message);
                        }
                    });
            // 展开节点
            $("#treeview").on("loaded.jstree", function (event, data) {
                // 展开所有节点
                //$('#jstree').jstree('open_all');
                // 展开指定节点
                //data.instance.open_node(1);     // 单个节点 （1 是顶层的id）
                data.instance.open_node([1, 10]); // 多个节点 (展开多个几点只有在一次性装载后所有节点后才可行）
            });

            // 所有节点都加载完后
            $("#treeview").on("ready.jstree", function (event, data) {
                //alert('all ok');
                data.instance.open_node(1); // 展开root节点
                //// 隐藏根节点 http://stackoverflow.com/questions/10429876/how-to-hide-root-node-from-jstree
                $("#1_anchor").css("visibility", "hidden");
                $("li#1").css("position", "relative")
                $("li#1").css("top", "-20px")
                $("li#1").css("left", "-20px")
                $(".jstree-last .jstree-icon").first().hide();
            });
            
            // 获得点击节点的id
            $('#treeview').on("changed.jstree", function (e, data) {
                var id = data.node.id;
                var filed = '';
                if (data.node.text=='公司') {
                    id = '';
                }else{
                    filed = JSON.stringify({KP_Bmid: id})
                }
                
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    return {
                        search: params.search,
                        sort: params.sort,
                        order: params.order,
                        filter: filed,
                        op: JSON.stringify({KP_Bmid: '='}),
                        offset: params.offset,
                        limit: params.limit,
                    };
                };
                table.bootstrapTable('refresh', {});
                            $.ajax({
                                'url': '',
                                'data': { 'id': data.node.id }
                            });
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
            },
            rendertree: function (content) {
                $("#treeview")
                        .on('redraw.jstree', function (e) {
                            $(".layer-footer").attr("domrefresh", Math.random());
                        })
                        .jstree({
                            "themes": {"stripes": true,'icons':true},
                            "checkbox": {
                                "keep_selected_style": true,
                            },
                            "types": {

                            },
                            "plugins" : [ "search", "state", "types", "wholerow" ],
                            "core": {
                                'check_callback': true,
                                "data": content
                            }
                        });
                }
                
        }

    };
    return Controller;
});