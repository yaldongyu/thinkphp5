//兼容ie浏览器，添加placeholder属性
function placeholderAdd() {
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
}

//全站报名控制
//提交需求
    var dingzhi_html = "";
    dingzhi_html += '<div class="dingzhi_form">';
    dingzhi_html += '<h6>需求订制</h6>';
    dingzhi_html += '<div class="dingzhi_form_text">';
    dingzhi_html += '<ul>';
    dingzhi_html += '<li>';
    dingzhi_html += '意向区域：<input type="text" name="T_Quyu" id="T_Quyu" />';
    dingzhi_html += '</li>';
    dingzhi_html += '<li>';
    dingzhi_html += '买房用途：<input type="text" name="T_Mfyt" id="T_Mfyt" />';
    dingzhi_html += '</li>';
    dingzhi_html += '<li>';
    dingzhi_html += '选择预算：';
    dingzhi_html += '<select name="T_Budget" id="T_Budget">';
    dingzhi_html += '<option value="100万以下">100万以下</option>';
    dingzhi_html += '<option value="100-300万" selected="selected">100-300万</option>';
    dingzhi_html += '<option value="300-500万">300-500万</option>';
    dingzhi_html += '<option value="500-1000万">500-1000万</option>';
    dingzhi_html += '<option value="1000-2000万">1000-2000万</option>';
    dingzhi_html += '<option value="2000万以上">2000万以上</option>';
    dingzhi_html += '</select>';
    dingzhi_html += '</li>';
    dingzhi_html += '</ul>';
    dingzhi_html += '<div class="dingzhi_form_zdxq">';
    dingzhi_html += '自定需求：<textarea id="T_Zdxq" name="T_Zdxq" rows="4" cols="39" placeholder="请输入您的需求"></textarea>';
    dingzhi_html += '</div>';
    dingzhi_html += '<div class="dingzhi_form_tel">';
    dingzhi_html += '联系电话：<input type="text" name="T_Phone" id="T_Phone" placeholder="您的手机号码" />';
    dingzhi_html += '</div>';
    dingzhi_html += '<div class="dingzhi_form_btn">';
    dingzhi_html += '<a href="javacript:;" onclick="Bm_dingzhi();return false;">提交需求</a>';
    dingzhi_html += '</div>';
    dingzhi_html += '</div>';
    dingzhi_html += '<div class="close_btn">';
    dingzhi_html += '<a href="javascript:;" onclick="layer.close(dingzhi_index)"></a>';
    dingzhi_html += '</div>';
    dingzhi_html += '</div>';

    var dingzhi_index;
    function dingzhi_form() {
        layer.ready(function () {
            dingzhi_index = layer.open({
                skin: 'demo-class',
                type: 1,
                title: false,
                shade: 0.5,
                shadeClose: true,
                area: ['500px', '500px'],
                content: dingzhi_html,
                closeBtn: 0,
                skin: 'bm_yourclass'
            });
        });

    }
    //警告提示信息
    var tishi_index;
    function tishi_layer(tishi_html) {
        
            tishi_index = layer.open({
                skin: 'demo-class',
                type: 0,
                shadeClose: true,
                content: tishi_html,
                closeBtn: 0,
                time: 3000,
                skin: 'tishi_class'
            });
        
    }
    function tishi_layer1(tishi_html) {
        layer.ready(function () {
            tishi_index = layer.open({
                skin: 'demo-class',
                type: 0,
                shadeClose: true,
                content: tishi_html,
                closeBtn: 0,
                time: 3000,
                skin: 'tishi_class'
            });
        });
    }

    //验证手机号
    function tel_validate(a) {
        var re = /^1\d{10}$/; //验证手机号
        var val = a;
        if (isNaN(val) || val == null || !re.test(val)) {
            tishi_layer("请输入正确的手机号码!");
            return false;
        } else {
            return true;
        }
    }


    //提示弹层报名
    function Bm_dingzhi() {
            var T_Quyu = $.trim($("#T_Quyu").val());
            var T_Mfyt = $.trim($("#T_Mfyt").val());
            var T_Budget = $("#T_Budget").val();
            var T_Zdxq = $("#T_Zdxq").val();
            var T_Phone = $.trim($("#T_Phone").val());

            if (!tel_validate(T_Phone)) {
                return false;
            }

            $.post('/Baoming.aspx',
	    {
	        sType: 'Dingzhi',
	        T_Quyu: T_Quyu,
	        T_Mfyt: T_Mfyt,
	        T_Budget: T_Budget,
	        T_Zdxq: T_Zdxq,
	        T_Phone: T_Phone
	    },
	    function (data) {
	        if (data.info != 'Yes') {
	            return false;
	        } else {
	            layer.close(dingzhi_index);
	            setTimeout(function () {
	                tishi_layer("恭喜订制成功！我们豪宅的顾问将会联系您，请稍后片刻！");
	                return false;
	            }, 100);
	        }
	    }, "json").error(function () { alert("数据加载失败，请检查后再操作！"); return false; });
    }


    /*看房团报名*/

    var Kftbm_Index;
    function Kftbm_Form(Title,Ly_Message,LpID,PageUrl,oType) {

        var Kftbm_Html = "";
        if (oType == "1") {
            Kftbm_Html += '<div class="Kftbm_Form">';
            Kftbm_Html += '<div class="Kftbm_Form_Left">';
            Kftbm_Html += '<div class="Title">';
            Kftbm_Html += '阅房网看房团报名';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="Info">';
            Kftbm_Html += '承诺：报名阅房网看房团，从预约到看房，全程不收任何费用，阅房网为您提供以下优越的购房服务。';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="TagList">';
            Kftbm_Html += '<ul>';
            Kftbm_Html += '<li class="Tag1">资深置业顾问一对一服务</li>';
            Kftbm_Html += '<li class="Tag2">免费接机，预定酒店住宿</li>';
            Kftbm_Html += '<li class="Tag3">专车全程接送看房</li>';
            Kftbm_Html += '<li class="Tag4">提供专项购房优惠</li>';
            Kftbm_Html += '</ul>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="Kftbm_Form_Right">';
            Kftbm_Html += '<div class="InputItem">';
            Kftbm_Html += '<input class="KftName" type="text" id="Kft_Name" placeholder="请输入您的姓名" />';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="InputItem">';
            Kftbm_Html += '<input class="KftPhone" type="text" id="Kft_Phone" placeholder="您的电话号码" />';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_Title" />';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_Ly_Message" />';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_LpID" />';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_PageUrl" />';


            Kftbm_Html += '<div class="KftButton">';
            Kftbm_Html += '<a href="javascript:;" id="Kftbm_Btn" onclick="Kftbm_Submit()">提交报名</a>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="TiShi">';
            Kftbm_Html += '阅房网以保护客户信息为原则，帮助客户免受信息泄漏带来的不便。请正确地填写以上信息，阅房网置业顾问将为您提供专业一对一服务，服务完全免费！';
            Kftbm_Html += '</div>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="clear"></div>';
            Kftbm_Html += '<div class="close_btn">';
            Kftbm_Html += '<a href="javascript:;" onclick="layer.close(Kftbm_Index)"></a>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '</div>';
        } else {

            Kftbm_Html += '<div class="Kftbm_Form">';
            Kftbm_Html += '<div class="Kftbm_Form_Left">';
            Kftbm_Html += '<div class="Title">';
            Kftbm_Html += '阅房网VIP服务';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="Info">';
            Kftbm_Html += '承诺：阅房网为您提供以下VIP服务。';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="TagList">';
            Kftbm_Html += '<ul>';
            Kftbm_Html += '<li class="Tag1">资深置业顾问一对一服务</li>';
            Kftbm_Html += '<li class="Tag2">免费接机，预定酒店住宿</li>';
            Kftbm_Html += '<li class="Tag3">专车全程接送看房</li>';
            Kftbm_Html += '<li class="Tag4">提供专项购房优惠</li>';
            Kftbm_Html += '</ul>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="Kftbm_Form_Right">';
            Kftbm_Html += '<div class="InputItem">';
            Kftbm_Html += '<input class="KftName" type="text" id="Kft_Name" placeholder="请输入您的姓名" />';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="InputItem">';
            Kftbm_Html += '<input class="KftPhone" type="text" id="Kft_Phone" placeholder="您的电话号码" />';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_Title" />';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_Ly_Message" />';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_LpID" />';
            Kftbm_Html += '<input type="hidden" value="" id="Kft_PageUrl" />';


            Kftbm_Html += '<div class="KftButton">';
            Kftbm_Html += '<a href="javascript:;" id="Kftbm_Btn" onclick="Kftbm_Submit()">确定</a>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="TiShi">';
            Kftbm_Html += '阅房网以保护客户信息为原则，帮助客户免受信息泄漏带来的不便。请正确地填写以上信息，阅房网置业顾问将为您提供专业一对一服务，服务完全免费！';
            Kftbm_Html += '</div>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '<div class="clear"></div>';
            Kftbm_Html += '<div class="close_btn">';
            Kftbm_Html += '<a href="javascript:;" onclick="layer.close(Kftbm_Index)"></a>';
            Kftbm_Html += '</div>';
            Kftbm_Html += '</div>';
        }

        layer.ready(function () {
            Kftbm_Index = layer.open({
                skin: 'demo-class',
                type: 1,
                title: false,
                shade: 0.5,
                shadeClose: true,
                area: ['640px', '320px'],
                content: Kftbm_Html,
                closeBtn: 0,
                skin: 'kftbm_class'
            });
        });

        $("#Kft_Title").val(Title);
        $("#Kft_Ly_Message").val(Ly_Message);
        $("#Kft_LpID").val(LpID);
        //$("#Kft_PageUrl").val(PageUrl);
        placeholderAdd(); //兼容ie9,添加placeholder属性
    }
    //看房团提交报名执行
    function Kftbm_Submit() {

        var T_KftName = $.trim($("#Kft_Name").val());
        var T_KftPhone = $.trim($("#Kft_Phone").val());
        var T_Title = $.trim($("#Kft_Title").val()); //标题
        var T_Message = $.trim($("#Kft_Ly_Message").val()); //来源
        var T_LpID = $.trim($("#Kft_LpID").val()); //楼盘ID
        var T_PageUrl = document.location.href;  //页面地址

        if (T_KftName == "" || T_KftName == "请输入您的姓名") {
            tishi_layer("请输入您的姓名!");
            return false;
        }
        if (!tel_validate(T_KftPhone)) {
            return false;
        }
        $.post('/Baoming.aspx',
	    {
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
	            layer.close(Kftbm_Index);
	            clear_form("#Kft_Name");
	            clear_form("#Kft_Phone");
	            setTimeout(function () {
	                tishi_layer("恭喜提交成功！阅房网置业顾问将会联系您，请稍后片刻！");
	                return false;
	            }, 100);
	        }
	    }, "json").error(function () { alert("数据加载失败，请检查后再操作！"); return false; });
	}


	/*优惠信息发送至手机 弹层*/

    var Send_Index;
    function SendToPhone_Form(Lp_Name,Title,Ly_Message,LpID,PageUrl,oType) {
        var SendHtml = "";
        if (oType == "1"){
            SendHtml += "<div class=\"SendMsgToPhone\">";
            SendHtml += "<div class=\"Head\">订阅信息</div>";
            SendHtml += "<div class=\"Info\">将【" + Lp_Name + "】最新数据信息<br/>以及最新降价、优惠活动信息第一时间通知您！</div>";
            SendHtml += "<div class=\"InputText\">";
            SendHtml += "<input type=\"text\" id=\"SendPhone\" placeholder=\"请输入手机号码\" />";
            SendHtml += "<a href=\"javascript:;\" onclick=\"SendToPhone_Submit()\">免费订阅</a>";
            SendHtml += "</div>";

            SendHtml += '<input type="hidden" value="" id="Send_Title" />';
            SendHtml += '<input type="hidden" value="" id="Send_Ly_Message" />';
            SendHtml += '<input type="hidden" value="" id="Send_LpID" />';
            SendHtml += '<input type="hidden" value="" id="Send_PageUrl" />';

            SendHtml += "<a class=\"CloseBtn\" href=\"javascript:;\" onclick=\"layer.close(Send_Index)\"></a>";
            SendHtml += "</div>";
        }else{
            SendHtml += "<div class=\"SendMsgToPhone\">";
            SendHtml += "<div class=\"Head\">降价通知</div>";
            SendHtml += "<div class=\"Info\">将【" + Lp_Name + "】最新数据信息<br/>以及最新降价、优惠活动信息第一时间通知您！</div>";
            SendHtml += "<div class=\"InputText\">";
            SendHtml += "<input type=\"text\" id=\"SendPhone\" placeholder=\"请输入手机号码\" />";
            SendHtml += "<a href=\"javascript:;\" onclick=\"SendToPhone_Submit()\">确定</a>";
            SendHtml += "</div>";

            SendHtml += '<input type="hidden" value="" id="Send_Title" />';
            SendHtml += '<input type="hidden" value="" id="Send_Ly_Message" />';
            SendHtml += '<input type="hidden" value="" id="Send_LpID" />';
            SendHtml += '<input type="hidden" value="" id="Send_PageUrl" />';

            SendHtml += "<a class=\"CloseBtn\" href=\"javascript:;\" onclick=\"layer.close(Send_Index)\"></a>";
            SendHtml += "</div>";
        }
        layer.ready(function () {
            Send_Index = layer.open({
                skin: 'demo-class',
                type: 1,
                title: false,
                shade: 0.3,
                shadeClose: true,
                area: ['440px', '300px'],
                content: SendHtml,
                closeBtn: 0,
                skin: 'SendToPhone_class'
            });
        });

        $("#Send_Title").val(Title);
        $("#Send_Ly_Message").val(Ly_Message);
        $("#Send_LpID").val(LpID);
        $("#Send_PageUrl").val(PageUrl);

        placeholderAdd(); //兼容ie9,添加placeholder属性
    }

    //发送优惠信息至手机报名执行
    function SendToPhone_Submit() {

        var T_KftName = "游客";
        var T_KftPhone = $.trim($("#SendPhone").val());
        var T_Title = $.trim($("#Send_Title").val()); //标题
        var T_Message = $.trim($("#Send_Ly_Message").val()); //来源
        var T_LpID = $.trim($("#Send_LpID").val()); //楼盘ID
        var T_PageUrl = document.location.href;  //页面地址

        if (!tel_validate(T_KftPhone)) {
            return false;
        }
        $.post('ajax/LpBaoming',
	    {
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
	            
	            clear_form("#SendPhone");
	            setTimeout(function () {
	                tishi_layer("恭喜提交成功！平台将会给您推送项目优惠信息,降价通知，感谢支持！");
	                return false;
	            }, 100);
	        }
	    }, "json").error(function () { alert("数据加载失败，请检查后再操作！"); return false; });
	}

	
        //提示框弹层
function Tip_Control(info, time) {
    $(".Tip_Info").css("display", "block");
    $(".Tip_Info").text(info);
    setTimeout(function () { $(".Tip_Info").css("display", "none"); }, time);
};
