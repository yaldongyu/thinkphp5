// JavaScript Document
		/*新房详情导航条展开隐藏*/
$(function () {
	$(".fix_bg").hide();
	$(window).scroll(function () {
		if ($(document).scrollTop() >= 100) {
			$(".fix_bg").addClass("fixnav").slideDown();
		} else {
			$(".fix_bg").hide();
		}
	})
})



// $(window).scroll(function (){
//     var st = $(this).scrollTop();
//     if(st >50){
//         $('.y_lpindexnav').animate({top:"50px"},100,function(){});
//         $('.y_lphome_nav').fadeOut();
//     }else{
//         $('.y_lpindexnav').animate({top:"-20px"},50,function(){});
//         $('.y_lphome_nav').fadeIn();
//     }
// });


  //问答提交
    function Ask_Submit() {
        var Ask_Phone = $.trim($("#Ask_Phone").val());
        var Ask_Content = $.trim($("#Ask_Content").val());
        var T_LpID = $.trim($("#lpid").val());
        var T_Title = ''; //标题

        if (Ask_Phone == '' || !$.IsTelephone(Ask_Phone, 1)) {
            Tip_Control("手机号码有误！", 1500);
            return false;
        }
        if (Ask_Content == '') {
            Tip_Control("请输入内容", 2000);
            return false;
        }
 
        $.post("/house/wenda",
            {
                sType: 'Add',
                T_LpID: T_LpID,
                Ask_Phone: Ask_Phone,
                T_Title: T_Title,
                Ask_Content: Ask_Content
            },
            function (data) {
                if (data.info != 'Yes') {
                    Tip_Control(data.info, 1500); 
                    return false;
                } else {
                    Tip_Control('您已成功提问。', 1500);
                    $(".am-share").removeClass("am-modal-active");  
                    setTimeout(function(){
                        $(".sharebg-active").removeClass("sharebg-active"); 
                        $(".sharebg").remove(); 
                    },300); 
                    $("#Ask_Phone").val("");$("#Ask_Content").val("");
        

                     
                }
            }, "json").error(function () {  return false; });
    }

//我要提问弹出层
	function toshare(){
		$(".am-share").addClass("am-modal-active");	
		if($(".sharebg").length>0){
			$(".sharebg").addClass("sharebg-active");
		}else{
			$("body").append('<div class="sharebg"></div>');
			$(".sharebg").addClass("sharebg-active");
		}
		$(".wd_close,.sharebg-active").click(function(){
			$(".am-share").removeClass("am-modal-active");	
			setTimeout(function(){
				$(".sharebg-active").removeClass("sharebg-active");	
				$(".sharebg").remove();	
			},300);
		})
	}	





