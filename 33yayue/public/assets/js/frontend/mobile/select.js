//竖直切换
$(function() {

$.each($('.F_wd_top_con2_r_borb1  a'), function(i, val) {
    var a = location.search.substring(1); b = a.split("&");
    for (var i = 0; i < b.length; i++) {

        if (val.getAttribute("value") == b[i]) {
            var c = b[i];
            if (c.split("=")[0] == "ct") { $("#place").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "tp") { $("#price").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "hx") { $("#fit").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "ts") { $("#more").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "lx") { $("#lplx").html("<i><span>" + val.text + "</span></i>"); }

        }

    }
    ;
});


$.each($('.F_wd_top_con2  a'), function(i, val) {
    var a = location.search.substring(1); b = a.split("&");
    for (var i = 0; i < b.length; i++) {

        if (val.getAttribute("value") == b[i]) {
            var c = b[i];
            if (c.split("=")[0] == "ct") { $("#place").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "tp") { $("#price").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "hx") { $("#fit").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "ts") { $("#more").html("<i><span>" + val.text + "</span></i>"); }
            if (c.split("=")[0] == "lx") { $("#lplx").html("<i><span>" + val.text + "</span></i>"); }

        }

    }
    ;
});







    window.onload = function() {
        var $li = $('.sy li');
        var $ul = $('.content .by');
        $li.click(function() {
            var $this = $(this);
            var $t = $this.index();
            $li.removeClass();
            $this.addClass('current');
            $ul.css('display', 'none');
            $ul.eq($t).css('display', 'block');
        })
    }
});

$(function() {
    $(".F_wd_top_con2_r_borb1").click(function() {
        $(this).addClass("F_wd_top_con2_r_borb2").siblings().removeClass("F_wd_top_con2_r_borb2");
    })
    
    

})








$(document).ready(function() {

var wH = $(window).height();
//$(".Z_con2_2").css("height", wH / 2 - 89);

$(document).scroll(function() {
    var topp = $(document).scrollTop();
    console.log(topp);
    if (topp > 20) {
        $(".tabSX").css("top", 44);
    } else {
        $(".tabSX").css("top", 82);
    }
})




$(".input").click(function() {
    $(".tabSX").attr("style", "");
})

$("#place").click(function() {
    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        $("body").removeClass("open");
        $(".w_barrier").hide();

    }
    else {
        $("body").addClass("open");
        $(this).addClass("active").siblings().removeClass("active");
        $("#city").attr("style", "display:block");
        $("#wapxfsy_D02_34").attr("style", "display:none;");
        $("#PRI").attr("style", "display:none;");
        $("#wapxfsy_D02_04").attr("style", "display:none");
        $("#wapxfsy_D02_06").attr("style", "display:none");
        $(".w_barrier").show();

    }


})
$("#price").click(function() {

    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        $("body").removeClass("open");
        $(".w_barrier").hide();
    }
    else {
        $("body").addClass("open");
        $(this).addClass("active").siblings().removeClass("active");

        $("#city").attr("style", "display:none;");
        $("#wapxfsy_D02_34").attr("style", "display:none;");
        $("#PRI").attr("style", "display:block;");
        $("#wapxfsy_D02_04").attr("style", "display:none");
        $("#wapxfsy_D02_06").attr("style", "display:none");
        $(".w_barrier").show();
    }
})
$("#fit").click(function() {
    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        $("body").removeClass("open");
        $(".w_barrier").hide();
    }
    else {
        $("body").addClass("open");
        $(this).addClass("active").siblings().removeClass("active");



        $("#city").attr("style", "display:none;");
        $("#wapxfsy_D02_34").attr("style", "display:block;");
        $("#characterChioce").attr("style", "display:block;width:100%;");
        $("#PRI").attr("style", "display:none;");
        $("#wapxfsy_D02_06").attr("style", "display:none");
        $("#wapxfsy_D02_04").attr("style", "display:none");
        $(".w_barrier").show();
    }



})
$("#more").click(function() {

    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        $("body").removeClass("open");
        $(".w_barrier").hide();
    }
    else {
        $("body").addClass("open");
        $(this).addClass("active").siblings().removeClass("active");

        $("#city").attr("style", "display:none;");
        $("#wapxfsy_D02_04").attr("style", "display:block");
        $("#PRI").attr("style", "display:none;");
        $("#wapxfsy_D02_06").attr("style", "display:none");
        $("#wapxfsy_D02_34").attr("style", "display:none;");
        $(".w_barrier").show();
    }

})

$("#lplx").click(function() {

    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
        $("body").removeClass("open");
        $(".w_barrier").hide();
    }
    else {
        $("body").addClass("open");
        $(this).addClass("active").siblings().removeClass("active");

        $("#city").attr("style", "display:none;");
        $("#wapxfsy_D02_06").attr("style", "display:block");
        $("#PRI").attr("style", "display:none;");
        $("#wapxfsy_D02_04").attr("style", "display:none");
        $("#wapxfsy_D02_34").attr("style", "display:none;");
        $(".w_barrier").show();
    }

})





$("#unit_price").click(function() {//单价按钮
    $(this).attr("class", "active").siblings().removeAttr("class");
    $("#priceChioceDj").attr("style", "display:block;");
    $("#priceChioceZj").attr("style", "display:none;");

})
$("#total_price").click(function() {//总价按钮
    $(this).attr("class", "active").siblings().removeAttr("class");
    $("#priceChioceZj").attr("style", "display:block;");
    $("#priceChioceDj").attr("style", "display:none;");
})
	/*这里跟二手房和租房下面的打电话用的dd标签冲突了
	$("dd").click(function(){//dd按钮
		$(this).attr("class","active").siblings().removeAttr("class");
	})
	*/
	/*$(".amu").click(function(){//a menu
		$(this).attr("class","active amu").siblings().attr("class","amu");
	})*/
	
});
/*function fn(action,value)//筛选参数拼接
{
	 url= decodeURI($("#sbt").attr("href"));
	 r = url.match(/\/(\w+)\//);
	 parames = parse_url(url);
	 parames[action]= value;
	 var newURL=r[0];
	 for(var tmp in parames){
		if(parames[tmp]!=0&&parames[tmp]!='不限')
			newURL=newURL+tmp+"_"+parames[tmp]+"-";
	}
	//newURL=newURL.substring(0,newURL.length-1);
	newURL=newURL.replace(/-$/g, "/")
	$("#sbt").attr("href",newURL); 
}*/
function fn(action,value,obj)//筛选参数拼接
{
	 $(obj).attr("class","active amu "+action).siblings().attr("class","amu "+action);
	 var chose_item_obj = $(obj).parents(".chose-item");
	 $(chose_item_obj).find('a').attr("class","amu "+action);
	 $(obj).attr("class","active amu "+action);
	 
	 url= decodeURI($("#sbt").attr("href"));
	 r = url.match(/\/(\w+)\//);
	 parames = parse_url(url);
	 parames[action]= value;
	 var newURL=r[0];
	 for(var tmp in parames){
		if(parames[tmp]!=0&&parames[tmp]!='不限'){
			
			newURL=newURL+tmp+"_"+parames[tmp]+"-";
			
		}
	}
	//newURL=newURL.substring(0,newURL.length-1);
	
	var selected_x_obj = $(".selected_x");
	aobj = $(selected_x_obj).find('#'+action);
	
	if($(aobj).attr("id")==action)
	{
		spanObj = $(aobj).parent('span');
		//
		txt = $(obj).text();
		
		$(spanObj).html('<a href="javascript:void(0);" onClick="bntClose('+"'"+action+"'"+',0,this)" id='+action+'>'+txt+' x</a>');
		
	}
	else
	{
		spanObj = $(aobj).parent('span');
		//
		txt = $(obj).text();
		
		$(selected_x_obj).append('<span><a href="javascript:void(0);" onClick="bntClose('+"'"+action+"'"+',0,this)" id='+action+'>'+txt+' x</a></span>');
	}
	
	newURL=newURL.replace(/-$/g, "/")
	$("#sbt").attr("href",newURL); 
	
	
}
function parse_url(url)
{ //定义函数 正则获取参数
	var pattern = /(\w+)_([\w|\u4e00-\u9fa5]+)/ig;//定义正则表达式
	var parames = {};//定义数组
	url.replace(pattern, function(a, b, c){
	parames[b] = c;
	})
	return parames;
};
function rest()//筛选参数拼接
{
	$(".amu").attr("class","amu");
	 url= decodeURI($("#sbt").attr("href"));
	 r = url.match(/\/(\w+)\//);
	 parames = parse_url(url);
	 parames['te']= 0;
	 parames['ft']= 0;
	 parames['op']= 0;
	 parames['ss']= 0;
	 parames['ha']= 0;
	 parames['ty']= 0;
	 parames['yh']= 0;
	 parames['ft']= 0;
	 parames['fc']= 0;
	 
	 var newURL=r[0];
	 for(var tmp in parames){
		if(parames[tmp]!=0&&parames[tmp]!='不限')
			newURL=newURL+tmp+"_"+parames[tmp]+"-";
	}
	//newURL=newURL.substring(0,newURL.length-1);
	newURL=newURL.replace(/-$/g, "/")
	$("#sbt").attr("href",newURL); 
	$(".selected_x").empty();
}
function bntClose(action,value,obj)//
{
	
	
	$("."+action).attr("class",action+" amu");
	 url= decodeURI($("#sbt").attr("href"));
	 r = url.match(/\/(\w+)\//);
	 parames = parse_url(url);
	 
	 parames[action]= value;
	 var newURL=r[0];
	 for(var tmp in parames){
		if(parames[tmp]!=0&&parames[tmp]!='不限')
			newURL=newURL+tmp+"_"+parames[tmp]+"-";
	}
	//newURL=newURL.substring(0,newURL.length-1);
	newURL=newURL.replace(/-$/g, "/")
	$("#sbt").attr("href",newURL); 
	span = $(obj).parent('span');
	$(span).remove();
}