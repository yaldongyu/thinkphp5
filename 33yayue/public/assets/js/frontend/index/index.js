// JavaScript Document
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



/*轮播图*/
$(document).ready(function(){
    $('.slider').mobilyslider({
        content:'.sliderContent',
        children:'div',
        transition:'horizontal',
        animationSpeed:300,
        autoplay: true,
        autoplaySpeed:5000,
        pauseOnHover:true,
        bullets:true,
        arrows:true,
        arrowsHide:true,
        prev:'prev',
        next:'next',
        animationStart: function(){},
        animationComplete: function(){}
		
    });  
});
/*[if IE 6]>
        <script type="text/javascript" src="js/DD_belatedPNG.js"></script>
        <script>
          DD_belatedPNG.fix('.png_bg');
        </script>
    <![endif]*/

/*本周甄选选项卡*/
$(function(){
    function tabs(tabTit,on,tabCon){
        $(tabTit).children().click(function(){
            $(this).addClass(on).siblings().removeClass(on);
            var index = $(tabTit).children().index(this);
           	$(tabCon).children().eq(index).show().siblings().hide();
    	});
	};
    tabs(".tab-hd","active",".tab-bd");   //本周甄选
    tabs(".tab-hd1","active1",".tab-bd1");   //房价走势

    // 本周甄选 click
    $($(".tab-hd").find("li")[1]).one("click",function(argument) { 
      getbzzx();
    });

      /*首页发现好房选项卡*/
    $('.f_title-list li').click(function(){ 
      var liindex = $('.f_title-list li').index(this); 
      $(this).addClass('on').siblings().removeClass('on');
      $('.product-wrap div.product').eq(liindex).fadeIn(150).siblings('div.product').hide();
      var liWidth = $('.f_title-list li').width();
      $('.find_tab .f_title-list p').stop(false,true).animate({'left' : liindex * liWidth + 'px'},300);
    });

    $('.f_title-list li').one("click",function(argument) {
      var liindex = $('.f_title-list li').index(this);
      getfxhf(liindex);
    })


      /*资讯切换*/
        var indexNewstls= $('ul.indexNews_nav > li');
        var indexNewsconts= $('.indexNews_tab');
        indexNewstls.each(function (i){
          $(this).click(function(){
            indexNewstls.removeClass();
            $(this).addClass('on');
            indexNewsconts.hide();
            indexNewsconts.eq(i).show();
          });
        });
        indexNewstls.eq(0).click();

        indexNewstls.one("click",function(argument) {
            var liindex = $('ul.indexNews_nav > li').index(this); 
            if(liindex>0){getnews(liindex);}
            
        });


    function getbzzx(argument) {
      $.post("/index/yxhgapi", function(data) {  
            var v = data.lptg;
            var c = $($(".tab-bd").find("li")[1]).find("a");  //[0]是标题，【1】是内容
            c.css("display","none");
            for (var i = 0; i < v.length; i++) { 
              $(c[i]).css("display","inline-block");
              $(c[i]).attr("href","tuangou/detail/ids/" + v[i].id) ;
              $(c[i]).find("img").attr("src",v[i].KP_PicUrl) ;  
            } 
      }); 
    }

    function getfxhf(position) { 
      $.post("/index/fxhfapi", {"position":position} , function(data) {  
            var v= data.tjlp ;
            $('.product-wrap div.product').eq(position).find(".gallery-item").css("display","none");
            for (var i = 0; i < v.length; i++) {
              var ga = $('.product-wrap div.product').eq(position).find(".gallery-item").eq(i);
              ga.css("display","");
              ga.find("img").eq(0).attr("src",v[i].KP_Wjt);
              ga.find("span").eq(0).html(v[i].KP_LpName);
              ga.find("em").eq(0).html(v[i].CNAME);
              ga.find(".fhui").eq(0).html("折扣：" + v[i].KP_YouHui);
                  var ts = v[i].KP_TsType.split(","); 
                      ts = function (data) {
                        var html = [];
                        for (var i = 0, length = data.length; i < length; i ++) {
                          if(i>1){break;}
                          html.push("<i>" + data[i] + "</i>"); 

                        }; 
                        return html.join('');
                      }(ts);
              ga.find(".f_tese").eq(0).html(ts);
                  var qijia = v[i].KP_Qiprice ;
                  var taojia = v[i].KP_TaoJia ;
                  var jiahtml="";
                  if(!qijia){
                    if(!taojia){
                      jiahtml ="待定";
                    }else{
                      jiahtml = taojia + "<i>万元/套</i>" ;
                    }
                  }else{
                    jiahtml = qijia + "<i>元/m²</i>" ;
                  }                  
              ga.find(".f_jia").eq(0).html(jiahtml);
              var gadesc = $('.product-wrap div.product').eq(position).find(".gallery-item-description").eq(i);
              gadesc.find("a").eq(0).attr("href","/house/house_details/ids/" + v[i].id ) ;
              

            } 

        }); 
    }

    function getnews(position) {
      var typeid = (position==1?1:5);
      $.post("/index/newsapi", {"typeid":typeid} , function(data) {   
        var v =data;
         $(".indexNews_tab").eq(position).find("li").css("display","none");
        for (var i = 0; i < v.length; i++) {
          var cli = $(".indexNews_tab").eq(position).find("li").eq(i) ;
          cli.css("display","");
          cli.find("img").eq(0).attr("src",v[i].KP_PicUrl);
          cli.find("img").eq(0).attr("alt",v[i].KP_Title);
          cli.find("a").eq(0).attr("href","/news/detail/ids" + v[i].id);
          cli.find("a").eq(0).html( v[i].KP_Title);
          cli.find("p").eq(0).html( v[i].KP_AddTime);
        } 
      });
    }

    function func() {
       setTimeout(function(argument) {
                getbzzx(); //页面执行完后就可以拿。
                getfxhf(1);getfxhf(2);getfxhf(3); //首页发现好房选项卡
                getnews(1);getnews(2); 
                              
            },0); 
      
    }

    var oldonload=window.onload; 
    if(typeof window.onload!='function'){ 
        window.onload=func; 
    }else{ 
        window.onload=function(){ 
            oldonload(); 
              func();
           
        } 
    } 

});
	  


 


			//报名滚动
			 function newsScroll(){
      var list = $('.roll-enroll .roll-list');

      function p(){
          list.animate({'top':'-32px'},1000,'linear',function(){
              for(var i=0;i<2;i++){
                  list.find('li:first').insertAfter(list.find('li:last'));
              }
              list.css({'top':'0px'});
          });
      }

      var t = setInterval(function(){
          p();
      },1000);

      list.hover(function(){
          clearInterval(t);
      },function(){
          t = setInterval(function(){
              p();
          },1000);
      });
  }
  function isScroll(){
      var list;
      var ttt = setInterval(function(){
          list = $('.roll-enroll .roll-list');
          if(list.length > 0){
              newsScroll();
              clearInterval(ttt);
          }
      },1000);
  }
  isScroll();


 $(function() {
      
        /*资讯图片滚动控制*/
        var clearTimer = null;
        var SleepTime = 2000;   //停留时长，单位毫秒
        $("#piclist1").jCarouselLite({
          btnPrev: "#pre1",
          btnNext: "#next1",
          visible:1,
          vertical:false,
          scroll:1,
          speed: 600,//滚动速度，单位毫秒
          auto:3000,
          mouseOver:true
        });
        $("#piclist2").jCarouselLite({
          btnPrev: "#pre2",
          btnNext: "#next2",
          visible:1,
          vertical:false,
          scroll:1,
          speed: 700,//滚动速度，单位毫秒
          auto:3000,
          mouseOver:true
        });
	 $("#piclist3").jCarouselLite({
          btnPrev: "#pre3",
          btnNext: "#next3",
          visible:1,
          vertical:false,
          scroll:1,
          speed: 600,//滚动速度，单位毫秒
          auto:3000,
          mouseOver:true
        });
    });


//看房报名
    function Bs_Submit() {
        var T_KftName = $.trim($("#Bs_Name").val());
        var T_KftPhone = $.trim($("#Bs_Phone").val());
        var T_Title = "首页-意向城市帮你找房报名";
        var T_Message = "PC端-首页"; //来源
        var T_LpID = '0'; //楼盘ID
        var T_TgID = '0'; //楼盘ID
        var T_PageUrl = document.location.href;  //页面地址

        if (T_KftName == "") {
            tishi_layer("请输入您的意向城市!");
            return false;
        }
        if (!tel_validate(T_KftPhone)) {
            return false;
        }
        $.post('/ajax/LpBaoming',
      {
          sType: 'KftBm',
          T_KftName: T_KftName,
          T_KftPhone: T_KftPhone,
          T_Title: T_Title,
          T_Message: T_Message,
          T_LpID: T_LpID,
          T_TgID: T_TgID,
          T_PageUrl: T_PageUrl
      },
      function (data) {
          if (data.info != 'Yes') {
              return false;
          } else {
              clear_form("#Bs_Name");
              clear_form("#Bs_Phone");
              setTimeout(function () {
                  tishi_layer("恭喜报名成功！我们的置业顾问将会联系您，请稍后片刻！");
                  return false;
              }, 100);
          }
      }, "json").error(function () { alert("数据加载失败，请检查后再操作！"); return false; });
    }


//首页图片跳转
$(function() {
     $(".gallery-item-image").click(function() {
     console.log( $(this).next().find("a").eq(0).attr("href") );
       location.href=$(this).next().find("a").eq(0).attr("href") ; 
    });  
});
