// JavaScript Document
function openwid3(tit,tits,frm) {  
    iBoxWidth = $(".s_group3").width();
    iBoxHeight = $(".s_group3").height();
    iWinWidth = $(window).width();
    iWinHeight = $(window).height();
    $(".s_group3").css("left", (iWinWidth / 6 - iBoxWidth / 6) + "%");
    $(".s_group3").css("top", (iWinHeight / 20 - iBoxHeight / 20) + "%");
    $(".s_group3").fadeIn();    
    $(".s_alert3").show();
}
		  function wid_close3() {
    $(".s_group3").fadeOut();
    $(".s_alert3").hide();
}
		  
$(function(argument){
      $('.mediaplayer').on('click','#closeyinp',function(){
        var oPlayer=document.getElementById('player');
        oPlayer.pause();          
        $('.mediaplayer').hide();
    });
    $('.house_yuyin').on('click','.openyinp',function(){
        // var oPlayer=document.getElementById('player');
        // oPlayer.play();          
        // $('.mediaplayer').show();
    });
    $('.house_yuyin').on('click','.openboyin',function(){  
        goaddyynum();
        if($("#player").attr("src")==""){
            openwid3('语音播报','提示：请输入您正确的手机号码继续免费收听，了解更多的楼盘详情。','【三亚】碧桂园海棠盛世楼盘内页_语音播报');
            return false;
        }      
        $('.mediaplayer').show();
        var oPlayer=document.getElementById('player');
        oPlayer.play();
            var i = parseInt($("#timers").html());
            var tim = document.getElementById("timers");
            var timer = setInterval(function () {
                i=parseInt($("#timers").html());
                console.log($("#timers").html());
                if (i == -1) {
                    openwid3('语音播报','提示：请输入您正确的手机号码继续免费收听，了解更多的楼盘详情。','【三亚】碧桂园海棠盛世楼盘内页_语音播报');
                    var oPlayer=document.getElementById('player');
                     oPlayer.pause();        
                    clearInterval(timer);
                } else {
                    $("#timers").html(i-1) ;
                }
            }, 1000);
    });   
})

function goaddyynum() {
    var id=$("#Bm_HouseID2").val();
    $.post("/house/addyynum", {  id: id  },
            function (data) { }, "json").error(function () {  return false; });
}
