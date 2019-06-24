define(['jquery', 'bootstrap', 'backend', 'addtabs', 'adminlte', 'form'], function ($, undefined, Backend, undefined, AdminLTE, Form) {
    var Controller = {
        index: function () { 
					
					/*价格排序*/ 
					/* 点击修改 */
					$('.jg_down_center span[name="jiage"]').on('click',function(){
					    var _txt = $(this).text();
					    $('span.down_text').html(_txt);
					    gotopriceorder(_txt);
					})

					$('.jg_down').hover(function(){
					    $('.jg_down_center').show();
					},function(){
					    $('.jg_down_center').hide();
					});

					/*选中效果 */
					$('.lp_shai ul li').on('click',function(){
					    $(this).addClass('jiaodian').siblings().removeClass('jiaodian');
					})

					$('.cation-list dd a').on('click',function(){
					    $(this).attr("class","on").siblings().attr("class","default"); 
					})

				 
				 

					        
					        
					/*右侧楼盘*/
					$(function(){
					        $(".right_lp .right_lp_li .right_lptitle").click(function(){
					            if($(this).parent(".right_lp_li").hasClass("on")){
					                //当前状态展开的时候，继续点击无效
					            }else{
					                $(this).parents("ul").find(".right_lp_li_down").slideUp(300,function(){
					                    $(this).parents("ul").find(".right_lp_li").removeClass("on");
					                });
					                $(this).next(".right_lp_li_down").slideDown(300,function(){
					                    $(this).parent(".right_lp_li").addClass("on");
					                });
					            }
					        });
					});

					/*楼盘页右侧报名看房滚动固定*/
					$(function () {
						var elm = $('.right_bm');
						var startPos = $(elm).offset().top;
						$.event.add(window, "scroll", function () {
							var p = $(window).scrollTop();
							$(elm).css('position', ((p) > startPos) ? 'fixed' : 'static');
							$(elm).css('top', ((p) > startPos) ? '0px' : '');
						});
					});


					

        },
        lpxc:function(){
    //     	 baguetteBox.run('.baguetteBoxOne', {
			 //    	animation: 'fadeIn',
				// });
        	// console.log("lpxc loading...");
        }
     }
    return Controller;
});