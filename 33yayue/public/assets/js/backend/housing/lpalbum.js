define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            $("#up").on('click', function () {
                Fast.api.open("housing/lpalbum/add", '添加', {
                callback: function (data) {
                    window.location.reload();
                }
                });

            });
           $("#delbtn").on('click', function () {
                var cks=document.getElementsByName("BoxList"); 
                var str=""; 
                //拼接所有的图书id 
                for(var i=0;i<cks.length;i++){ 
                    if(cks[i].checked){
                     str+=cks[i].value+","; 
                    } 
                }

                Layer.confirm('确定删除？',
                        {icon: 3, title: __('Warning'), shadeClose: true},
                        function (index) {
                            Fast.api.ajax({
                               url:'housing/lpalbum/del/ids/'+str
                            }, function(data, ret){
                               //成功的回调
                               window.location.reload();
                               return false;
                            }, function(data, ret){
                               //失败的回调
                               
                               return false;
                            });
                            Layer.close(index);
                        }
                    );

            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
               Form.api.bindevent($("form[role=form]"), function(data, ret){
                    //这里是表单提交处理成功后的回调函数，接收来自php的返回数据
                    Fast.api.close(data);//这里是重点
                }, function(data, ret){
                    Toastr.success("失败");
                });
            }
        }
    };
    return Controller;
});