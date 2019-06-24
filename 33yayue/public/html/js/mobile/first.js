// JavaScript Document

//团购倒计时
    var intDiff = parseInt(511228); //倒计时总秒数量
    function timer(intDiff) {
        window.setInterval(function () {
        var day = 0,
        hour = 0,
        minute = 0,
        second = 0; //时间默认值        
            if (intDiff > 0) {
                day = Math.floor(intDiff / (60 * 60 * 24));
                hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
            }
            if (minute <= 9) minute = '0' + minute;
            if (second <= 9) second = '0' + second;
            $('#day_show').html(day);
            $('#hour_show').html(hour);
            $('#minute_show').html(minute);
            $('#second_show').html(second);
			$('#day_show2').html(day);
            $('#hour_show2').html(hour);
            $('#minute_show2').html(minute);
            $('#second_show2').html(second);
			$('#day_show3').html(day);
            $('#hour_show3').html(hour);
            $('#minute_show3').html(minute);
            $('#second_show3').html(second);
			$('#day_show4').html(day);
            $('#hour_show4').html(hour);
            $('#minute_show4').html(minute);
            $('#second_show4').html(second);
            intDiff--;
        }, 1000);
    }
    $(function () {
        timer(intDiff);
    });

			//横向滚动
    var nav_w = $(".hx_nav_list li").first().width();
    //$(".sideline").width(nav_w);
    $(".hx_nav_list li").on('click', function () {
        nav_w = $(this).width();
        //$(".sideline").stop(true);
        //$(".sideline").animate({ left: $(this).position().left }, 300);
        //$(".sideline").animate({ width: nav_w });
        $(this).addClass("hx_nav_cur").siblings().removeClass("hx_nav_cur");

        $(this).find("a").addClass("on");
        $(this).siblings().find("a").removeClass("on");

        var fn_w = ($(".find_nav").width() - nav_w) / 2;
        var fnl_l;
        var fnl_x = parseInt($(this).position().left);
        if (fnl_x <= fn_w) {
            fnl_l = 0;
        } else if (fn_w - fnl_x <= flb_w - fl_w) {
            fnl_l = flb_w - fl_w;
        } else {
            fnl_l = fn_w - fnl_x;
        }
        $(".hx_nav_list").animate({
            "left": fnl_l
        }, 300);
        //sessionStorage.left = fnl_l;
        //var c_nav = $(this).find("a").text();
        //navName(c_nav);
    });
    var Yleft = $('.hx_nav_list ul li a.on').position().left;
    var Yleftlast = $('.hx_nav_list ul li').last().position().left;
    var Ylefteq2 = $('.hx_nav_list ul li').eq(-2).position().left;
    var Ylefteq3 = $('.hx_nav_list ul li').eq(-3).position().left;
    //console.log(Yleft);
    if (Yleft < 150) {
        $(".hx_nav_list").css("left", "0px");
    } else if (Yleft > Yleftlast) {
        $(".hx_nav_list").css("left", '-' + (Yleft - $(window).width() + 95) + "px");
    } else if (Yleft > Ylefteq2) {
        $(".hx_nav_list").css("left", '-' + (Yleft - ($(window).width() / 2) - 15) + "px");
    } else if (Yleft > Ylefteq3) {
        $(".hx_nav_list").css("left", '-' + (Yleft - ($(window).width() / 2) + 90) + "px");
    } else {
        $(".hx_nav_list").css("left", '-' + (Yleft - 90) + "px");
    };



    var fl_w = $(".hx_nav_list").width();
    var flb_w = $(".hx_two_gun").width();
    $(".hx_nav_list").on('touchstart', function (e) {
        var touch1 = e.originalEvent.targetTouches[0];
        x1 = touch1.pageX;
        y1 = touch1.pageY;
        ty_left = parseInt($(this).css("left"));
    });
    $(".hx_nav_list").on('touchmove', function (e) {
        var touch2 = e.originalEvent.targetTouches[0];
        var x2 = touch2.pageX;
        var y2 = touch2.pageY;
        if (ty_left + x2 - x1 >= 0) {
            $(this).css("left", 0);
        } else if (ty_left + x2 - x1 <= flb_w - fl_w) {
            $(this).css("left", flb_w - fl_w);
        } else {
            $(this).css("left", ty_left + x2 - x1);
        }
        if (Math.abs(y2 - y1) > 0) {
            e.preventDefault();
        }
    }); 