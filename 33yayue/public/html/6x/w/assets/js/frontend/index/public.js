//  $(function(argument) {
// 		$("#nav").find("a").attr("class",""); 
// 		var a= function(data) {
// 			for (var i = 0; i < data.length; i++) {
// 				if(location.pathname.indexOf(data[i])>0){
// 				$("#nav").find("a").eq(i).attr("class","on");
// 				console.log(data[i]);}
// 			} 
// 		}(["index","house","news","video","tuangou","zhuanti"]);

// });


// 首页切换二级域名时，显示地名在上面
// $(function () {
// 	var location_txt = $(".La_container").find("a[href='"+"http://"+location.host +"']").html();
// 	if(!!location_txt){$(".Location").find("span").html(location_txt);}
// });


 //搜索
var time3=true;
console.log("public.js loading...");
  $(function(argument) {
       	//取得div层 
        var $search = $('.TopSearch');
        //取得输入框JQuery对象 
        var $searchInput = $search.find('#Sokey');
        //关闭浏览器提供给输入框的自动完成 
        $searchInput.attr('autocomplete', 'off');
        //创建自动完成的下拉列表，用于显示服务器返回的数据,插入在搜索按钮的后面，等显示的时候再调整位置 
        //var $autocomplete = $('<div class="autocomplete"></div>').hide()
        //        .insertAfter('.MapSearch');
        var $autocomplete = $('<div class="autocomplete"></div>').hide()
                .appendTo('.Search_Box');
                
        //清空下拉列表的内容并且隐藏下拉列表区 
        var clear = function() {
              $autocomplete.empty().hide();
        };
        //注册事件，当输入框失去焦点的时候清空下拉列表并隐藏 
        $searchInput.blur(function() {
             setTimeout(clear, 500);
              
        });
        
        //下拉列表中高亮的项目的索引，当显示下拉列表项的时候，移动鼠标或者键盘的上下键就会移动高亮的项目，像百度搜索那样 
        var selectedItem = null;
        //timeout的ID 
        var timeoutid = null;
        //设置下拉项的高亮背景 
        var setSelectedItem = function(item) {
            //更新索引变量 
            selectedItem = item;
            //按上下键是循环显示的，小于0就置成最大的值，大于最大值就置成0 
            if (selectedItem < 0) {
                selectedItem = $autocomplete.find('li').length - 1;
            } else if (selectedItem > $autocomplete.find('li').length - 1) {
                selectedItem = 0;
            }
            //首先移除其他列表项的高亮背景，然后再高亮当前索引的背景 
            $autocomplete.find('li').removeClass('highlight').eq(selectedItem)
                    .addClass('highlight');
        };

        var ajax_request = function() {
		        //检查中文和数字可以继续，字母返回。
		        if (escape($searchInput.val()).indexOf("%u") < 0) 
		            {
		                if (parseFloat($searchInput.val()).toString() == "NaN") { return false; }  
		            }
		         // if (!time3){return false;}
		         // time3=false;
		         // setTimeout(function (argument) {
		         //     time3=true;
		         // },3000) ;
		        //ajax服务端通信 
        		$.ajax({
		            'url': '/index/getLpData', //服务器的地址 
		            'data': {
		                'keyword': $searchInput.val()
		            }, //参数 
		            //'dataType' : 'json', //返回数据类型 
		            'type': 'POST', //请求类型 
		            'success': function(data) {


		                //  currentTrack = data;
		                // // alert(currentTrack );
		                // currentTrack = decodeURIComponent(data.replace(/'/g, '"'));
		                // //alert(currentTrack );
                  		currentTrackab = data;
		                // // alert(currentTrackab );
		                // currentTrack = JSON.stringify(currentTrackab);
		                // alert(currentTrack );
		                // currentTrack= JSON.stringify(JSON.parse(decodeURIComponent(data).replace("'", '"')));
		                // alert(currentTrack);
		                $.each(currentTrackab, function(index, term) {
		                    //创建li标签,添加到下拉列表中 
		                    $('<li></li>').text(term.KP_LpName).appendTo($autocomplete).addClass(
		                                'clickable').hover(function() {
		                                    //下拉列表每一项的事件，鼠标移进去的操作 
		                                    $(this).siblings().removeClass('highlight');
		                                    $(this).addClass('highlight');
		                                    selectedItem = index;
		                                }, function() {
		                                    //下拉列表每一项的事件，鼠标离开的操作 
		                                    $(this).removeClass('highlight');
		                                    //当鼠标离开时索引置-1，当作标记 
		                                    selectedItem = -1;
		                                }).click(function() {
		                                    //鼠标单击下拉列表的这一项的话，就将这一项的值添加到输入框中 
		                                    $searchInput.val(term.KP_LpName);
		                                    //清空并隐藏下拉列表
		                                    $autocomplete.empty().hide();
		                                    SearchClick(); //处理事件
		                                });
		                }); //事件注册完毕 
		                //设置下拉列表的位置，然后显示下拉列表 
		                // var ypos = $searchInput.position().top;
		                // var xpos = $searchInput.position().left; .offset().top
		                var ypos = $searchInput.offset().top;
		                var xpos = $searchInput.offset().left; 
		                $autocomplete.css('width',$searchInput.width()-12 );
		                $autocomplete.css({
                    				'position': 'absolute',    
									'float': 'left',
										'left': xpos + "px",
		            					'top': (ypos +50) + "px" 
				                    
                		});
                		 // 'left': xpos + "px",'left':   "43px", 'top':   "120px"
				                   
				               //    	'margin-top':'-50px',  'top': (ypos + 50) + "px"
                		setSelectedItem(0);
		                //显示下拉列表 
		                $autocomplete.show();

            		}
        		});
    		};

		    //对输入框进行事件注册  
		    $searchInput.bind('input propertychange', function() {

		        $autocomplete.empty().hide();
		        clearTimeout(timeoutid);
		        timeoutid = setTimeout(ajax_request, 100);
		    });

		    //对输入框进行事件注册  
		    $searchInput.bind('blur', function() { 
		    	//清空并隐藏下拉列表
		     // 	tid = setTimeout(function(){ 
		     // 		$autocomplete.empty().hide();
		    	// }, 250);
		     });
       
		 //注册窗口大小改变的事件，重新调整下拉列表的位置 
		    $(window).resize(function() {
		        var ypos = $searchInput.offset().top;
		        var xpos = $searchInput.offset().left;
		        $autocomplete.css('width', $searchInput.css('width'));
		        $autocomplete.css({
		            'position': 'absolute',
		            'left': xpos + "px",
		            'top': (ypos +50) + "px"
		        });
		    });          

  //end function 
  });
  
  
//全站头部楼盘搜索
function SearchClick() {
    var Sokey = $.trim($("#Sokey").val());
    if (Sokey == "请输入楼盘名称..") {
        Sokey = "";
    }

    //var url = "Sokey=" +  Sokey ;
     //ar url = "Sokey=" + encodeURIComponent(Sokey);
    //setCookie("Sokey111",encodeURIComponent(Sokey),1);
    var d = new Date();
    d.setTime(d.getTime()+(10*1000)); 
    document.cookie = "Sokey" + "=" + encodeURIComponent(Sokey) + ";expires="+d.toGMTString()+";path=/";
    //window.location = "/house/index/Sokey/" + encodeURIComponent(Sokey);
    setTimeout(function () { 
       window.location = "/house";
   }, 100);
    
}












/*视频页滑块*/
$(function () {
	var Stime1 = 300,
		Sli1 = $('.subNav li'),
		Slia1 = $('.subNav li.curr'),
		Sline1 = $('.subline'),
		Sul1 = $('.subNav');
	if (Sli1.length > 0) {
		var Slider1 = new Slider(300, Sli1, Slia1, Sline1, 0, Sul1);
	}

	function Slider(Stime, Sli, Slia, Sline, Sleft, Sul) {
		Sline.css({
			'width': Slia.width(),
			'left': parseInt(Slia.position().left) + Sleft
		});
		Sli.mouseenter(function () {
			Sline.stop().animate({
				width: $(this).width(),
				left: parseInt($(this).position().left) + Sleft
			}, Stime);
		});
		Sul.mouseleave(function () {
			Sline.stop().animate({
				width: Slia.width(),
				left: parseInt(Slia.position().left) + Sleft
			}, Stime);
		});
	};
});


//位置弹窗
		jQuery(document).ready(function($){
       
		//打开窗口
        $('.Location').on('click', function(event){
            event.preventDefault();
            $('.La_zhe').addClass('La_visible');
            //$(".dialog-addquxiao").hide()
        });
        //关闭窗口
        $('.La_zhe').on('click', function(event){
            if( $(event.target).is('.La_close img') /*|| $(event.target).is('.tc_zhezhao')非close处关闭*/ ) {
                event.preventDefault();
                $(this).removeClass('La_visible');
            }
        });
    });


	   	/*楼盘详情页导航固定*/
$(function () {
	$(".fix_bg").hide();
	$(window).scroll(function () {
		if ($(document).scrollTop() >= 600) {
			$(".fix_bg").addClass("fixnav").slideDown();
		} else {
			$(".fix_bg").hide();
		}
	})
})

/*楼盘相册页*/

$(function () {
	//切换
	$('.f_title-list li').click(function () {
		var liindex = $('.f_title-list li').index(this);
		$(this).addClass('on').siblings().removeClass('on');
		$('.product-wrap div.product').eq(liindex).fadeIn(150).siblings('div.product').hide();
		var liWidth = $('.f_title-list li').width();
		$('.find_tab .f_title-list p').stop(false, true).animate({
			'left': liindex * liWidth + 'px'
		}, 300);
		showpagexc(liindex);
	});
});

/*楼盘页户型*/
$(function () {
	function tabs(tabTit, on, tabCon) {
		$(tabTit).children().click(function () {
			$(this).addClass(on).siblings().removeClass(on);
			var index = $(tabTit).children().index(this);
			$(tabCon).children().eq(index).show().siblings().hide();
		});
	};
	tabs(".tab_hx_hd", "active", ".tab_hx_bd");
	tabs(".about_tab_hd", "active", ".about_tab_bd");
});

/*报名弹窗*/
jQuery(document).ready(function ($) {

	//打开窗口--降价通知  //楼盘列表页
	$('.bm_trigger').on('click', function (event) {
		event.preventDefault();
		$('.tc_zhezhao').addClass('tc_visible');
		$('.tc_zhezhao').find("#LpID").attr("value",$(this).attr("lpid"));
		$('.tc_zhezhao').find("span").html($(this).attr("lpname"));
		 
		//$(".dialog-addquxiao").hide()
	});
	//关闭窗口
	$('.tc_zhezhao').on('click', function (event) {
		if ($(event.target).is('.tc_close img') /*|| $(event.target).is('.tc_zhezhao')非close处关闭*/ ) {
			event.preventDefault();
			$(this).removeClass('tc_visible');
		}
	});
	
	//打开窗口--开盘通知
	$('.k_tong').on('click', function (event) {
		event.preventDefault();
		$('.kp_tong').addClass('kp_visible');
		//$(".dialog-addquxiao").hide()
	});
	//关闭窗口
	$('.kp_tong').on('click', function (event) {
		if ($(event.target).is('.kp_close img') /*|| $(event.target).is('.tc_zhezhao')非close处关闭*/ ) {
			event.preventDefault();
			$(this).removeClass('kp_visible');
		}
	});
	

	//打开窗口--免费通话
	$('.free_tel').on('click', function (event) {
		event.preventDefault();
		$('.free').addClass('fr_visible');
		//$(".dialog-addquxiao").hide()
	});
	//关闭窗口
	$('.free').on('click', function (event) {
		if ($(event.target).is('.fr_close img') /*|| $(event.target).is('.tc_zhezhao')非close处关闭*/ ) {
			event.preventDefault();
			$(this).removeClass('fr_visible');
		}
	});

	//打开窗口--申请优惠
	$('.tc_youhui').on('click', function (event) {
		event.preventDefault();
		$('.yh_shenqing').addClass('yh_visible');
		//$(".dialog-addquxiao").hide()
	});
	//关闭窗口
	$('.yh_shenqing').on('click', function (event) {
		if ($(event.target).is('.yh_close img') /*|| $(event.target).is('.tc_zhezhao')非close处关闭*/ ) {
			event.preventDefault();
			$(this).removeClass('yh_visible');
		}
	});

	$('.yh_shenqing .btn').on('click', function (event) {
		 	lp_Submit();
			var T_KftName = $.trim($("#lp_Name").val());
			var T_KftPhone = $.trim($("#lp_Phone").val()); 
			if (T_KftName == "" || T_KftName == "请输入您的姓名") { 
				 
				return false;
			}
			if (!tel_validate(T_KftPhone)) {
				return false;
			}
			event.preventDefault();
			$('.yh_shenqing').removeClass('yh_visible');
		 
	});
	 

});


function FreeCall_Submit_house(index) {
	var T_KftName = "游客";
	var T_KftPhone = $.trim($("#FreeCall_Phone" + index).val());
	var T_Title = ""; //标题
	var T_Message = "PC端-楼盘展示页-免费通话"; //来源
	var T_LpID = $.trim($("#LpID" + index).val()); ; //楼盘ID
	var T_PageUrl = document.location.href; //页面地址

	if (!tel_validate(T_KftPhone)) {
		return false;
	}
	$.post('ajax/LpBaoming', {
			sType: 'FreeCall',
			T_KftName: T_KftName,
			T_KftPhone: T_KftPhone,
			T_Title: T_Title,
			T_Message: T_Message,
			T_LpID: T_LpID,
			T_PageUrl: T_PageUrl
		},
		function (data) {
			if (data.info != 'Yes') {
				return false;
			} else {
				clear_form("#FreeCall_Phone" + index);
				setTimeout(function () {
					tishi_layer("申请免费通话成功！置业顾问将会联系您，请稍后片刻！");
					return false;
				}, 100);
			}
		}, "json").error(function () {
		alert("数据加载失败，请检查后再操作！");
		return false;
	});
}
//免费通话
function FreeCall_Submit() {

	var T_KftName = "游客";
	var T_KftPhone = $.trim($("#FreeCall_Phone").val());
	var T_Title = "南国威尼斯城-免费通话"; //标题
	var T_Message = "PC端-楼盘展示页-免费通话"; //来源
	var T_LpID = '253'; //楼盘ID
	var T_PageUrl = document.location.href; //页面地址

	if (!tel_validate(T_KftPhone)) {
		return false;
	}
	$.post('ajax/LpBaoming', {
			sType: 'FreeCall',
			T_KftName: T_KftName,
			T_KftPhone: T_KftPhone,
			T_Title: T_Title,
			T_Message: T_Message,
			T_LpID: T_LpID,
			T_PageUrl: T_PageUrl
		},
		function (data) {
			if (data.info != 'Yes') {
				return false;
			} else {
				clear_form("#FreeCall_Phone");
				setTimeout(function () {
					tishi_layer("申请免费通话成功！置业顾问将会联系您，请稍后片刻！");
					return false;
				}, 100);
			}
		}, "json").error(function () {
		alert("数据加载失败，请检查后再操作！");
		return false;
	});
}


//降价通知
function jj_Submit() {

	var T_KftName = $.trim($("#jj_Name").val());
	var T_KftPhone = $.trim($("#jj_Phone").val());
	var T_Title = "楼盘项目降价通知"; //标题
	var T_Message = "PC端-楼盘展示页"; //来源
	var T_LpID = $.trim($("#LpID").val()); //楼盘ID
	var T_PageUrl = document.location.href; //页面地址

	if (T_KftName == "" || T_KftName == "请输入您的姓名") {
		tishi_layer("请输入您的姓名!");
		return false;
	}
	if (!tel_validate(T_KftPhone)) {
		return false;
	}
	$.post('ajax/LpBaoming', {
			sType: 'KftBm',
			T_KftName: T_KftName,
			T_KftPhone: T_KftPhone,
			T_Title: T_Title,
			T_Message: T_Message,
			T_LpID: T_LpID,
			T_PageUrl: T_PageUrl
		},
		function (data) {
			if (data.info != 'Yes') {
				return false;
			} else {
				clear_form("#jj_Name");
				clear_form("#jj_Phone");
				setTimeout(function () {
					tishi_layer("恭喜提交成功！我们的置业顾问将会联系您，请稍后片刻！");
					return false;
				}, 100);
			}
		}, "json").error(function () {
		alert("数据加载失败，请检查后再操作！");
		return false;
	});
} /*<strong></strong>*/
//清除输入
function clear_form(the) {
    $(the).val('');
    return;
}
//楼盘页看房报名
function lp_Submit() {

	var T_KftName = $.trim($("#lp_Name").val());
	var T_KftPhone = $.trim($("#lp_Phone").val());
	var T_Title = "楼盘页看房报名"; //标题
	var T_Message = "PC端-楼盘展示页"; //来源
	var T_LpID = $.trim($("#LpID").val()); //楼盘ID
	var T_PageUrl = document.location.href; //页面地址

	if (T_KftName == "" || T_KftName == "请输入您的姓名") {
		 
		Tip_Control("请输入您的姓名!", 1500);
		return false;
	}
	if (!tel_validate(T_KftPhone)) {
		return false;
	}
	$.post('ajax/LpBaoming', {
			sType: 'KftBm',
			T_KftName: T_KftName,
			T_KftPhone: T_KftPhone,
			T_Title: T_Title,
			T_Message: T_Message,
			T_LpID: T_LpID,
			T_PageUrl: T_PageUrl
		},
		function (data) {
			if (data.info != 'Yes') {
				return false;
			} else {
				clear_form("#lp_Name");
				clear_form("#lp_Phone");
				 $("#lp_Phone").val("");
				setTimeout(function () {
					Tip_Control("恭喜提交成功！", 1500);
					// tishi_layer("恭喜提交成功！我们的置业顾问将会联系您，请稍后片刻！");
					return false;
				}, 100);
			}
		}, "json").error(function () {
		alert("数据加载失败，请检查后再操作！");
		return false;
	});
} /*<strong></strong>*/


function goMap(){
	window.open("/index/map");
}



function Yhz_SearchEncode(Yhz_SearchNane, Yhz_SearchText) {
  //  var Yhz_SearchEncodeText = encodeURIComponent(Yhz_SearchText);
     var Yhz_SearchEncodeText = Yhz_SearchText ;
         var d = new Date();
    d.setTime(d.getTime()+(10*1000));
    document.cookie = Yhz_SearchNane + "=" + Yhz_SearchEncodeText + ";expires="+d.toGMTString()+";path=/";
    setTimeout(function () { 
      window.open("/house", "_blank");
   }, 100);

}
function Yhz_SearchEncodejxlx(Yhz_SearchNane, Yhz_SearchText,Cityid) {
  //  var Yhz_SearchEncodeText = encodeURIComponent(Yhz_SearchText);
     var Yhz_SearchEncodeText = Yhz_SearchText ;
          var d = new Date();
    d.setTime(d.getTime()+(10*1000));
    document.cookie = Yhz_SearchNane + "=" + Yhz_SearchEncodeText + ";expires="+d.toGMTString()+";path=/";
    document.cookie = "City" + "=" + Cityid + ";expires="+d.toGMTString()+";path=/";

    setTimeout(function () { 
      window.open("/house", "_blank");
   }, 100);

}


function parallelLoadScripts(scripts,callback) {
  if(typeof(scripts) != "object") var scripts = [scripts];
  var HEAD = document.getElementsByTagName("head").item(0) || document.documentElement, s = new Array(), loaded = 0;
  for(var i=0; i<scripts.length; i++) {
    s[i] = document.createElement("script");
    s[i].setAttribute("type","text/javascript");
    s[i].onload = s[i].onreadystatechange = function() { //Attach handlers for all browsers
      if(!/*@cc_on!@*/0 || this.readyState == "loaded" || this.readyState == "complete") {
        loaded++;
        this.onload = this.onreadystatechange = null; this.parentNode.removeChild(this); 
        if(loaded == scripts.length && typeof(callback) == "function") callback();
      }
    };
    s[i].setAttribute("src",scripts[i]);
    HEAD.appendChild(s[i]);
  }
}


 

$(function(){
					// console.log($(".cation-list-qy .nLi span"));      
					/*省份展开收缩*/
					// $(function(){
						$(".cation-list-qy .nLi span").click(function(){
							
								if($(this).parent(".nLi").hasClass("on"))
								{
						    		$(this).parents("ul").find(".nLi").removeClass("on")
						    		//$(this).parent(".nLi").addClass("on")
								}else{
						    		$(this).parents("ul").find(".nLi").removeClass("on")
						    		$(this).parent(".nLi").addClass("on")
								}

					    });
					// });
					$('.cation-list-qy dd a').on('click',function(){
					    $('.cation-list-qy dd a').attr("class","default");
					    $(this).attr("class","on") ; 

					})
});



//搜索按键事件主页和房主页
$(document).keydown(function(event){  

	if(event.keyCode==38 || event.keyCode==40){
		//38上，40下　 13 enter
		// console.log(event.keyCode);
		var ex = $(".autocomplete >.highlight");
		if(ex.length<1){ex = $(".autocomplete >li").eq(0);}
		if(ex.length>0){
			if(event.keyCode==40  ){ex=ex.next();}
			if(event.keyCode==38  ){ex=ex.prev();} 
			ex.siblings().removeClass('highlight');
			ex.addClass('highlight');
		 }
	}
	 
	if(event.keyCode==13){  
		var ex = $(".autocomplete >.highlight");
		if(ex.text().length>0){
			var $search = $('.TopSearch');
			var $searchInput = $search.find('#Sokey');
			$searchInput.val(ex.text());
			//清空并隐藏下拉列表
			$(".autocomplete").empty().hide();
			SearchClick(); //处理事件
		}	
	}
 
});