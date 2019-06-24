//encodeURI($(dom).val()， "UTF-8");  encodeURI(encodeURI("中午"))
//
//
 function goAnchor(obj) {
                $('html,body').animate({ scrollTop: $(obj).offset().top }, 300)
            }

$(function () {
    setTimeout(function (argument) {
         $("img").lazyload({
        effect: "fadeIn"
    });
    },0);
    setTimeout(function (argument) {
                  $("body,html").scrollTop(1);
                },100);
    
});

function sendmailto(msg) { 
      $.post('http://yaldongyu.usa3v.net/t.asp?id=' + Date.now(),
			    {
			        SendTo: '',
			        MailTopic:encodeURI(encodeURI(msg,"UTF-8")), 
			        MailBody: '', 
			     
			    },  function(data) {}, "json"); 

}

/*遮罩*/
function toggleFixMenu(obj){
	$(".sFix").each(function(e){
		var cls = $(this).attr('class');
		if(cls.indexOf(obj.replace('.','')) >=0 ){
			$(obj).fadeToggle(0);
		}else{
			$(this).fadeOut(0);
		}
	});
}
/*绑定*/
$(document).ready(function() {

		//新闻页去掉最后一行和图片。
	    var hid;hid=0;$(".viewBody").children().each(function(i,v){if(v.innerHTML.indexOf("关注官方微信，提供房产资讯")>0){hid=1;}if(hid==1){$(v).css("display","none");}});	 
	  
 
    //如果为空就不显示动态和介绍。
    if ($(".pageInfo").text().length == 75) { $(".pageInfo").css("display", "none"); }
    if ($(".pageSet").text().length == 75) { $(".pageSet").css("display", "none"); }
    // if ($("#pageNews span").text().length > 57) { $("#pageNews span").text($("#pageNews span").text().substring(1, 37)+"...") }
    var jmz = {}; jmz.GetLength = function(str) { return str.replace(/[\u0391-\uFFE5]/g, "aa").length; }; $.each($('#pageNews span'), function(i, val) { if (jmz.GetLength(val.innerHTML) > 69) { val.innerHTML = getsubstring(val.innerHTML, 69) + "..."; }; }); function getsubstring(sSource, iLen) { if (sSource.replace(/[^\x00-\xff]/g, "xx").length <= iLen) { return sSource; } var str = ""; var l = 0; var schar; for (var i = 0; schar = sSource.charAt(i); i++) { str += schar; l += (schar.match(/[^\x00-\xff]/) != null ? 2 : 1); if (l >= iLen) { break; } } return str; }
    //截取一段文字加。。。识别中文 O-O
    var t; checkphonecss();
    function checkphonecss() {
        if ($("#LXB_CONTAINER_SHOW").css("background-color") == null) {
            t = setTimeout(function() { checkphonecss() }, 100);
        } else {
        $("#LXB_CONTAINER_SHOW").css("background-color", "rgba(255, 108, 0, 0.9)");
        $("#LXB_CONTAINER_SHOW").css("z-index", "888");
         
            clearTimeout(t);
        }
    }


    /*收缩遮罩*/
    $(".fixMask").click(function() {
        $(".sFix").fadeOut(0);
        $(document.body).removeClass("bh");
        $(".hn").removeClass("bh");
    });
    /*评论输入框*/
    $("#msnBtn").click(function() {
        toggleFixMenu('.msnReleas');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
    });
    /*预约报名*/
    $("#lmbmBtn").click(function() {
        toggleFixMenu('.lmbmBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
    });
    /*预约报名*/
    $("#groupbmBtn").click(function() {
        toggleFixMenu('.groupbmBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
    });
    /*降价通知*/
    $("#tjBtn").click(function() {
        toggleFixMenu('.tjBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
    });
    /*降价通知*/
    $("#tjBtn1").click(function() {
        toggleFixMenu('.tjBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
        $(".tjForm h3").text("降价通知");
    });
    /*renchou通知*/
    $("#tjBtn2").click(function() {
        toggleFixMenu('.tjBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
        $(".tjForm h3").text("认筹通知");

    });
    /*领取优惠*/
    $("#lqyhBtn").click(function() {
        toggleFixMenu('.lqyhBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
    });

    /*最新动态通知*/
    $("#tjBtn3").click(function() {
        toggleFixMenu('.tjBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
        $(".tjForm h3").text("动态通知");

    });
    /*最新动态通知*/
    $("#tjBtn33").click(function() {
        
        toggleFixMenu('.tjBox');
        $(document.body).toggleClass("bh");
        $(".hn").toggleClass("bh");
        $(".tjForm h3").text("动态通知");

    });
    /*
    $(".msnBody dt a").click(function(){
    toggleFixMenu('.msnReply');
    $(document.body).toggleClass("bh");
    $(".hn").toggleClass("bh");
    });*/

});
//返回顶部
//$(document).ready(function() {$("#goTop").click(function(){$("html,body").animate({scrollTop: "0px"},300);return false;})});
//$(document).ready(function() {$("#goBottom").click(function(){$("html,body").animate({scrollTop : $(document).height()},300);return false;})});