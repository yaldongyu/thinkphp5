//全局控制
//回车搜索楼盘
$(function () {
    $("#Sokey").focus(function () {
        document.onkeydown = function (e) {
            var ev = document.all ? window.event : e;
            if (ev.keyCode == 13) { //Ctrl + Enter
                e.preventDefault(); //阻止浏览器默认事件
                SearchClick(); //处理事件
            }
        }
    });
})

//兼容ie浏览器，添加placeholder属性
$(function () {
    if (!('placeholder' in document.createElement('input'))) {

        $('input[placeholder],textarea[placeholder]').each(function () {
            var that = $(this),
      text = that.attr('placeholder');
            if (that.val() === "") {
                that.val(text).addClass('placeholder');
                that.val(text).css("color", "#999");
            }
            that.focus(function () {
                if (that.val() === text) {
                    that.val("").removeClass('placeholder');
                }
            })
      .blur(function () {
          if (that.val() === "") {
              that.val(text).addClass('placeholder');
          }
      })
      .closest('form').submit(function () {
          if (that.val() === text) {
              that.val('');
          }
      });
        });
    }
})

//顶部二维码显示
$(function () {
    $(".Erweima").mousemove(function () {
        $(this).children("span").css("display", "block");
    }).mouseout(function () {
        $(this).children("span").css("display", "none");
    });
})

//模拟导航下拉列表
function NavigationSelect_Show(the) {
    $(the).children("p").css("display", "block");
    $(the).children(".Title").children("span").css({ "background": "url(Images/icon-select-up.png)no-repeat", "background-size": "14px 14px", "background-position": "center center" });
    $(the).children(".Title").addClass("MouseOn");
    $(the).mouseout(function () {
        $(the).children("p").css("display", "none");
        $(the).children(".Title").children("span").css({ "background": "url(Images/icon-select-down.png)no-repeat", "background-size": "14px 14px", "background-position": "center center" });
        $(the).children(".Select").children("span").css({ "background": "url(Images/icon-select-down-blue.png)no-repeat", "background-size": "14px 14px", "background-position": "center center" });
        $(the).children(".Title").removeClass("MouseOn");
    });
}

//全站头部楼盘搜索
function SearchClick() {
    var Sokey = $.trim($("#Sokey").val());
    if (Sokey == "请输入楼盘名称..") {
        Sokey = "";
    }

    var url = "Sokey=" + $.uri(Sokey);
    window.location = "http://www.3yayue.com/house/?" + url;
}
function goMap() {
    window.open("http://www.3yayue.com/map/", "_blank");
}

//搜索编码
function Yhz_SearchEncode(Yhz_SearchNane, Yhz_SearchText) {
    var Yhz_SearchEncodeText = encodeURIComponent(Yhz_SearchText);
    window.open("http://www.3yayue.com/house/?" + Yhz_SearchNane + "=" + Yhz_SearchEncodeText);
}

//顶部二维码显示
$(function () {
    $(".TopColumn_Right a").mouseenter(function () {
        $(".TopColumn_Right .EwmShow").css("display", "block");
    }).mouseleave(function () {
        $(".TopColumn_Right .EwmShow").css("display", "none");
    });
});

//全站顶部城市切换
function Jdf_CityToggle() {
    $(".Jdf_CitySelect").css("display", "block");
    $(".Jdf_City_Btn").addClass("City_Btn_On");

    var Dzs_City_Btn_W = $('.Jdf_City_Btn').width();
    var Dzs_City_Btn_H = $('.Jdf_City_Btn').height();
    var Dzs_City_Btn_X = $('.Jdf_City_Btn').offset().left;
    var Dzs_City_Btn_Y = $('.Jdf_City_Btn').offset().top;
    var Dzs_City_Select_X = $('.Jdf_CitySelect').offset().left;
    var Dzs_City_Select_Y = $('.Jdf_CitySelect').offset().top;
    var Dzs_City_Select_W = $('.Jdf_CitySelect').width();
    var Dzs_City_Select_H = $('.Jdf_CitySelect').height();
    $(window).mousemove(function (e) {
        var Dzs_Mouse_X = e.pageX;
        var Dzs_Mouse_Y = e.pageY;
        if (!(Dzs_Mouse_X - Dzs_City_Btn_X >= 0 && Dzs_Mouse_X - Dzs_City_Btn_X <= Dzs_City_Btn_W && Dzs_Mouse_Y - Dzs_City_Btn_Y >= 0 && Dzs_Mouse_Y - Dzs_City_Btn_Y <= Dzs_City_Btn_H)
         && !(Dzs_Mouse_X - Dzs_City_Select_X >= 0 && Dzs_Mouse_X - Dzs_City_Select_X <= Dzs_City_Select_W && Dzs_Mouse_Y - Dzs_City_Select_Y >= 0 && Dzs_Mouse_Y - Dzs_City_Select_Y <= Dzs_City_Select_H)) {
            $(".Jdf_CitySelect").css("display", "none");
            $(".Jdf_City_Btn").removeClass("City_Btn_On");
            return;
        }
    });
};
$(function () {
    $(".Jdf_City_Btn").mousemove(function () {
        Jdf_CityToggle();
    });
});

//全站筛选条件事件
$(function () {
    $(".OtherQuicklyNavigation").mousemove(function () {
        $(this).children(".Other_FilterCont").css("display", "block");

        $(".Other_FilterItem").mousemove(function () {
            $(this).addClass("Select");
            $(this).children(".Other_MoreFilter").css("display", "block");
        }).mouseout(function () {
            $(this).removeClass("Select");
            $(this).children(".Other_MoreFilter").css("display", "none");
        });

    }).mouseout(function () {
        $(this).children(".Other_FilterCont").css("display", "none");
    });
});

//验证手机号
function all_tel_validate(a) {
    var re = /^1\d{10}$/; //验证手机号
    var val = a;
    if (isNaN(val) || val == null || !re.test(val)) {
        return false;
    } else {
        return true;
    }
}
//清除输入
function clear_form(the) {
    $(the).val('');
    return;
}

//提示框弹层
function Tip_Control(info, time) {
    $(".Tip_Info").css("display", "block");
    $(".Tip_Info").text(info);
    setTimeout(function () { $(".Tip_Info").css("display", "none"); }, time);
};

//点评点赞
//设置点赞cookie
function setCookie(c_name, value, expiredays) {
    var exdate = new Date()
    exdate.setDate(exdate.getDate() + expiredays)
    document.cookie = c_name + "=" + escape(value) +
        ((expiredays == null) ? "" : ";expires=" + exdate.toGMTString())
}
//获取cookie
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1
            c_end = document.cookie.indexOf(";", c_start)
            if (c_end == -1) c_end = document.cookie.length
            return unescape(document.cookie.substring(c_start, c_end))
        }
    }
    return ""
}
//ajax执行点赞(用于点评)
function DianZanAjax(ItemID, the, Type) {
    $.post('/DianZan.aspx',
	        {
	            sType: Type,
	            ItemID: ItemID
	        },
	        function (data) {
	            if (data.info == 'Yes') {
	                Tip_Control("评论成功！感谢您的支持。", 1500);
	                the.children("span").text(Number(the.children("span").text()) + 1);
	            } else {
	                return false;
	            }
	        }, "json").error(function () { kp.dialog.alert("数据加载失败，请检查后再操作！"); return false; });
}

//点赞逻辑
function DianZanSubmit(a, name, Type) {
    var myip = getCookie(name);
    if (a.attr("t_uip") != myip) {
        setCookie(name, a.attr("t_uip"), 365);
        DianZanAjax(a.attr("itemid"), a, Type);
    } else {
        Tip_Control("您已经评论过了！感谢支持。", 1500);
    }
}

//ajax喜欢提交
function HouseLikeAjax(LpID, the, Type) {
    $.post('/DianZan.aspx',
	    {
	        sType: Type,
	        LpID: LpID
	    },
	    function (data) {
	        if (data.info == 'Yes') {
	            Tip_Control("评论成功！感谢您的支持。", 1500);
	            the.children("font").text(Number(the.children("font").text()) + 1);
	        } else {
	            return false;
	        }
	    }, "json").error(function () { kp.dialog.alert("数据加载失败，请检查后再操作！"); return false; });
}

//返回顶部
$(function () {
    var tophtml = "<div id=\"top_back\" class=\"top-back\"><a href=\"javascript:void(0);\" onclick=\"openZoosUrl('chatwin');\" class=\"btn btn-qq\"></a><div class=\"btn btn-wx\"><img class=\"pic\" src=\"Images/wx_dyh.jpg\" onclick=\"window.location.href=\'http://www.3yayue.com\'\"/></div><div class=\"btn btn-phone\"><div class=\"phone\">400-1552-899</div></div><div class=\"btn btn-top\"></div></div>";
    $("#top").html(tophtml);
    $("#top_back").each(function () {
        $(this).find(".btn-wx").mouseenter(function () {
            $(this).find(".pic").fadeIn("fast");
        });
        $(this).find(".btn-wx").mouseleave(function () {
            $(this).find(".pic").fadeOut("fast");
        });
        $(this).find(".btn-phone").mouseenter(function () {
            $(this).find(".phone").fadeIn("fast");
        });
        $(this).find(".btn-phone").mouseleave(function () {
            $(this).find(".phone").fadeOut("fast");
        });
        $(this).find(".btn-top").click(function () {
            $("html, body").animate({
                "scroll-top": 0
            }, "fast");
        });
    });
    var lastRmenuStatus = false;
    $(window).scroll(function () {//bug
        var _top = $(window).scrollTop();
        if (_top > 200) {
            $("#top_back").data("expanded", true);
        } else {
            $("#top_back").data("expanded", false);
        }
        if ($("#top_back").data("expanded") != lastRmenuStatus) {
            lastRmenuStatus = $("#top_back").data("expanded");
            if (lastRmenuStatus) {
                $("#top_back .btn-top").slideDown();
            } else {
                $("#top_back .btn-top").slideUp();
            }
        }
    });
});