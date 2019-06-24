define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            var id = Config.id.name;
            Table.api.init({
                extend: {
                    index_url: 'housing/lpcontent/index/ids/'+id,
                    add_url: 'housing/lpcontent/add',
                    edit_url: 'housing/lpcontent/edit',
                    del_url: 'housing/lpcontent/del',
                    multi_url: 'housing/lpcontent/multi',
                    table: 'lpcontent',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search:false,
                searchFormVisible: true,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'KP_Lm', title: __('Kp_lm'),searchList: {"0":'项目介绍',"1":'周边配套',"2":'楼盘特点'}},
                        {field: 'KP_AddUser', title: __('Kp_adduser'),operate:false},
                        {field: 'KP_AddTime', title: __('Kp_addtime'),operate:false, addclass:'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
             
            /*if(Config.row.KP_Lm ==1 ){
                //如果是走遍配套就显示
                // setTimeout(function(argument) {
                //       console.log($(".zbpt").length );
                    $(".zbpt").css("display","block");
                    $(".lpnr").css("display","none");
                    
                    for (var i = 0; i < $(".zbptdata .pageSet_tb_right").length; i++) {
                        $("#c-KP_"+ (i+1)).val($(".zbptdata .pageSet_tb_right").eq(i).text());

                    }
                    
                // },50); 
            };

            $(".btn-success").click(function(argument) {
                var html_built='<ul class="pageSet_tb">';
                var arr= "医院,银行,商场,公园,酒店,学校,路线,娱乐".split(",")
                for (var i = 0; i < arr.length; i++) {
                    var h = $("#c-KP_"+ (i+1)).val().length>0?"inline-block":"none"
                    html_built += '<li style="display:'+h+'"><div class="pageSet_tb_left" >'+arr[i]+'：</div><div class="pageSet_tb_right">';
                    html_built +=   $("#c-KP_"+ (i+1)).val() + '</div></li>' ; 
                }
                html_built +=   '</ul>' ; 
                $('[name="row[KP_Lpnr]"]').val(  html_built);
                
            })*/
               
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});