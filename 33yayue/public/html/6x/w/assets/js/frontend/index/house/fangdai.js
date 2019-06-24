//楼盘详情页购房预算模块

$(function(){
    /* 饼状图表 */
    $("#htype-dropdown li").click(function(){
        var currentChooseArea =  $(this).attr('data-code');    // 当前所选户型面积
        $("#currentChooseArea").val(currentChooseArea);

        var currentBuildingPirce = $("#currentBuildingPirce").val();    // 当前楼盘均价
        var totalHousePrice = Math.round(currentBuildingPirce * currentChooseArea / 10000);    // 房屋总金额
        $("#totalHousePrice").val(totalHousePrice);
        // $("#totalPrice").html(totalHousePrice);
        $('#ht-total-price').html(totalHousePrice);

        var anjie = $('#anjie').val();   // 按揭成数
        var daikuan_total_price = Math.round(currentBuildingPirce * currentChooseArea / 10000 * anjie);    // 贷款总额
        $("#daikuan_total_price").val(daikuan_total_price);
        $('#group-dk-total').text($("#daikuan_total_price").val());
    })

    /* 输入总价 */
    $('#totalHousePrice').keyup(function (){
        var totalPrice = $.trim($(this).val());

        if (/^[\d]+(\.[\d]{1,2})?$/.test(totalPrice))
        {
            $("#daikuan_total_price").val(Math.round(totalPrice * $('#anjie').val()));
            $('#group-dk-total').text($("#daikuan_total_price").val());
            $('#ht-total-price').text(totalPrice);
        }
    });

    /* 贷款类型 */
    $("#daikuan_type_list li").click(function(){
        $("#daikuan_type").val($(this).attr('data-code'));

        if ($(this).attr('data-code') == 3)
        {
            $('#group-dk-total').text($('#daikuan_total_price').val());
            $('#group-dk-area').css('display', 'block');
        }
        else
        {
            $('#group-dk-area').css('display', 'none');
        }

        if ($(this).parents('.hj-col-md-9:first').length > 0)
        {
            $('.referesult').css('height', $(this).parents('.hj-col-md-9:first').height());
        }
        else
        {
            $('.referesult').css('height', $(this).parents('.month-pay:first').height() + 40);
        }
    })

    /* 贷款年限 */
    $("#time-dropdown li").click(function(){
        $("#years").val($(this).attr('data-code'));
    })

    /* 按揭比例 */
    $("#percent-dropdown li").click(function(){
        var anjie = $(this).attr('data-code');
        var totalPrice = parseInt($('#totalHousePrice').val());
        $("#anjie").val(anjie);

        var daikuan_total_price = Math.round(totalPrice * anjie);    // 贷款总额
        $("#daikuan_total_price").val(daikuan_total_price);
        $('#group-dk-total').text($("#daikuan_total_price").val());
    })

    /* 点击计算按钮计算房贷 */
    $('#mortgageCalculation').click(function (){
        /* 饼状图表 */
        showResult($.trim($("#totalHousePrice").val()));
    });

    /* 初始化总价 */
    $('#totalHousePrice').val($('#totalHousePrice').attr('total-price'));
    $('#anjie').val('0.7');
    $("#daikuan_total_price").val(Math.round($('#totalHousePrice').attr('total-price') * $('#anjie').val()));
    $('#daikuan_type').val('1');
    $('#years').val('20');

    showResult($.trim($("#totalHousePrice").val()));
})

lilv_array = new Array; 
//12年6月8日基准利率
lilv_array[1] = new Array;
lilv_array[1][1] = new Array;
lilv_array[1][2] = new Array;
lilv_array[1][1][1] = 0.0631;//商贷1年 6.31%
lilv_array[1][1][3] = 0.0640;//商贷1～3年 6.4%
lilv_array[1][1][5] = 0.0665;//商贷 3～5年 6.65%
lilv_array[1][1][10] = 0.0680;//商贷 5-30年 6.8%
lilv_array[1][2][5] = 0.0420;//公积金 1～5年 4.2%
lilv_array[1][2][10] = 0.0470;//公积金 5-30年 4.7%
//12年6月8日利率下限（7折）
lilv_array[2] = new Array;
lilv_array[2][1] = new Array;
lilv_array[2][2] = new Array;
lilv_array[2][1][1] = 0.04417;//商贷1年 4.417%
lilv_array[2][1][3] = 0.0448;//商贷1～3年 4.48%
lilv_array[2][1][5] = 0.04655;//商贷 3～5年 4.655%
lilv_array[2][1][10] = 0.0476;//商贷 5-30年 4.76%
lilv_array[2][2][5] = 0.0420;//公积金 1～5年 4.2%
lilv_array[2][2][10] = 0.0470;//公积金 5-30年 4.7%
//12年6月8日利率下限（85折）
lilv_array[3] = new Array;
lilv_array[3][1] = new Array;
lilv_array[3][2] = new Array;
lilv_array[3][1][1] = 0.053635;//商贷1年 5.3635%
lilv_array[3][1][3] = 0.0544;//商贷1～3年 5.44%
lilv_array[3][1][5] = 0.056525;//商贷 3～5年 5.6525%
lilv_array[3][1][10] = 0.0578;//商贷 5-30年 5.78%
lilv_array[3][2][5] = 0.0420;//公积金 1～5年 4.2%
lilv_array[3][2][10] = 0.0470;//公积金 5-30年 4.7%
//12年6月8日利率上限（1.1倍）
lilv_array[4] = new Array;
lilv_array[4][1] = new Array;
lilv_array[4][2] = new Array;
lilv_array[4][1][1] = 0.06941;//商贷1年 6.941%
lilv_array[4][1][3] = 0.0704;//商贷1～3年 7.04%
lilv_array[4][1][5] = 0.07315;//商贷 3～5年 7.315%
lilv_array[4][1][10] = 0.0748;//商贷 5-30年 7.48%
lilv_array[4][2][5] = 0.0420;//公积金 1～5年 4.2%
lilv_array[4][2][10] = 0.0470;//公积金 5-30年 4.7%
//12年7月6日基准利率
lilv_array[5] = new Array;
lilv_array[5][1] = new Array;
lilv_array[5][2] = new Array;
lilv_array[5][1][1] = 0.0485;//商贷1年 6%
lilv_array[5][1][3] = 0.0525;//商贷1～3年 6.15%
lilv_array[5][1][5] = 0.0525;//商贷 3～5年 6.4%
lilv_array[5][1][10] = 0.0540;//商贷 5-30年 6.55%
lilv_array[5][2][5] = 0.0300;//公积金 1～5年 4%
lilv_array[5][2][10] = 0.0350;//公积金 5-30年 4.5%
//12年7月6日利率下限（7折）
lilv_array[6] = new Array;
lilv_array[6][1] = new Array;
lilv_array[6][2] = new Array;
lilv_array[6][1][1] = 0.042;//商贷1年 4.2%
lilv_array[6][1][3] = 0.04305;//商贷1～3年 4.305%
lilv_array[6][1][5] = 0.0448;//商贷 3～5年 4.48%
lilv_array[6][1][10] = 0.04585;//商贷 5-30年 4.585%
lilv_array[6][2][5] = 0.0400;//公积金 1～5年 4%
lilv_array[6][2][10] = 0.0450;//公积金 5-30年 4.5%
//12年7月6日利率下限（85折）
lilv_array[7] = new Array;
lilv_array[7][1] = new Array;
lilv_array[7][2] = new Array;
lilv_array[7][1][1] = 0.051;//商贷1年 5.1%
lilv_array[7][1][3] = 0.052275;//商贷1～3年 5.2275%
lilv_array[7][1][5] = 0.0544;//商贷 3～5年 5.44%
lilv_array[7][1][10] = 0.055675;//商贷 5-30年 5.5675%
lilv_array[7][2][5] = 0.0400;//公积金 1～5年 4%
lilv_array[7][2][10] = 0.0450;//公积金 5-30年 4.5%
//12年7月6日利率上限（1.1倍）
lilv_array[8] = new Array;
lilv_array[8][1] = new Array;
lilv_array[8][2] = new Array;
lilv_array[8][1][1] = 0.066;//商贷1年 6.6%
lilv_array[8][1][3] = 0.06765;//商贷1～3年 6.765%
lilv_array[8][1][5] = 0.0704;//商贷 3～5年 7.04%
lilv_array[8][1][10] = 0.07205;//商贷 5-30年 7.205%
lilv_array[8][2][5] = 0.0400;//公积金 1～5年 4%
lilv_array[8][2][10] = 0.0450;//公积金 5-30年 4.5%

//15年10月24日利率上限（1.1倍）
lilv_array[9] = new Array;
lilv_array[9][1] = new Array;
lilv_array[9][2] = new Array;
lilv_array[9][1][1] = 0.0435;//商贷1年 6.6%
lilv_array[9][1][3] = 0.0475;//商贷1～3年 6.765%
lilv_array[9][1][5] = 0.0475;//商贷 3～5年 7.04%
lilv_array[9][1][10] = 0.0490;//商贷 5-30年 7.205%
lilv_array[9][2][5] = 0.0275;//公积金 1～5年 4%
lilv_array[9][2][10] = 0.0325;//公积金 5-30年 4.5%


var _$ = function(id){
   return document.getElementById(id);
}

function myround(v, e){
    var t = 1;
    e = Math.round(e);
    for(; e > 0; t *= 10, e--);
    for(; e < 0; t /= 10, e++);
    return Math.round(v * t) / t;
}

/* 选择的贷款类型 */
function exc_zuhe(fmobj, v){
    if (v == 3){
        document.all.calc1_zuhe.style.display = 'block';
    }else{
        document.all.calc1_zuhe.style.display = 'none';
    }
}

//验证是否为数字
function reg_Num(str){
    if (str.length == 0){return false;}
    var Letters = "1234567890.";

    for(i = 0; i < str.length; i++){
        var CheckChar = str.charAt(i);
        if (Letters.indexOf(CheckChar) == -1){return false;}
    }

    return true;
}

//得到利率
function getlilv(lilv_class, type, years){
    var lilv_class = parseInt(lilv_class);
    if (years <= 5){
        return lilv_array[lilv_class][type][5];
    }else{
        return lilv_array[lilv_class][type][10];
    }
}

//本金还款的月还款额(参数: 年利率 / 贷款总额 / 贷款总月份 / 贷款当前月0～length-1)
function getMonthMoney2(lilv, total, month, cur_month){
    var lilv_month = lilv / 12;//月利率
    //return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
    var benjin_money = total / month;
    return (total - benjin_money * cur_month) * lilv_month + benjin_money;
}

//本息还款的月还款额(参数: 年利率/贷款总额/贷款总月份)
function getMonthMoney1(lilv, total, month){
    var lilv_month = lilv / 12;//月利率
    return total * lilv_month * Math.pow(1 + lilv_month, month) / ( Math.pow(1 + lilv_month, month) -1 );
}

/* 显示结果 */
function showResult(totalPrice)
{
    if (totalPrice >= 0 && /^[\d]+(\.[\d]{1,2})?$/.test($.trim(totalPrice)))
    {
        chartPie(ext_total());
    }
    else
    {
        alert('估算总价填写错误');
    }
}

/* 统计计算 */
function ext_total(){

    var years = $('#years').val();
    var month = years * 12;
    var totalHousePrice = $('#totalHousePrice').val();    // 总房款

    /* 组合型贷款(组合型贷款的计算，只和商业贷款额、和公积金贷款额有关，和按贷款总额计算无关) */
    if($("#daikuan_type").val() == 3 )
    {
        var total_price_sy = parseInt($('#total-price-sy').val());
        var total_price_gjj = parseInt($('#total-price-gjj').val());
        var lilv_sy = getlilv($("#lilv").val(), 1, $("#years").val());
        var lilv_gjj = getlilv($("#lilv").val(), 2, $("#years").val());
        var daikuan_total_price = parseInt($('#daikuan_total_price').val());    // 贷款总额

        /* 判断总金额是否正确 */
        if ((total_price_gjj + total_price_sy) != daikuan_total_price)
        {
            alert('公积金和商业贷款总和不等于贷款总额');
            return false;
        }

        // 首期付款
        var first_pay = totalHousePrice - daikuan_total_price;
        $("#first_pay").html( first_pay + '万元');


        $("#daikuan_total_price2").html(daikuan_total_price + '万元');

        // 贷款总月数
        $("#daikuan_total_month").val(month);

        /* 本息还款 */
        //商贷月均还款
        var month_money_sy = getMonthMoney1(lilv_sy, total_price_sy * 10000, month);    // 调用函数计算
        var average_month_pay_sy = Math.round(month_money_sy * 100);

        //商贷还款总额
        var all_total_sy = Math.round(month_money_sy * month * 100) / 100;
        //商贷支付利息款
        var pay_lixi_sy = (Math.round((all_total_sy - total_price_sy * 10000) * 100) / 100) / 10000;

        //公积金月均还款
        var month_money_gjj = getMonthMoney1(lilv_gjj, total_price_gjj * 10000, month);    // 调用函数计算
        var average_month_pay_gjj = Math.round(month_money_gjj * 100);

        //公积金还款总额
        var all_total_gjj = Math.round(month_money_gjj * month * 100) / 100;
        //公积金支付利息款
        var pay_lixi_gjj = (Math.round((all_total_gjj - total_price_gjj * 10000) * 100) / 100) / 10000;

        var month_money = (average_month_pay_sy + average_month_pay_gjj) / 100;
        var pay_lixi = parseFloat(pay_lixi_sy.toFixed(1)) + parseFloat(pay_lixi_gjj.toFixed(1));

        $("#pay_total_price").val(all_total_sy + all_total_gjj);
        $("#pay_lixi").html((pay_lixi).toFixed(1) + '万元');
        $("#average_month_pay").html(month_money + '元');
    }
    /* 公积金贷款或者商贷 */
    else
    {
        /* 商业贷款、公积金贷款 */
        var lilv = getlilv($("#lilv").val(), $("#daikuan_type").val(), $("#years").val());    // 得到利率

        /* 根据贷款总额计算 */
        if ($("#daikuan_total_price").val() == '') {
            return;
        }

        // 首期付款
        var first_pay = totalHousePrice - $('#daikuan_total_price').val();
        $("#first_pay").html( first_pay + '万元');

        //贷款总额
        var daikuan_total_price = $('#daikuan_total_price').val();
        $("#daikuan_total_price2").html(daikuan_total_price + '万元');

        // 贷款总月数
        $("#daikuan_total_month").val(month);
    
        /* 2.本息还款 */
        //月均还款
        var month_money1 = getMonthMoney1(lilv, daikuan_total_price * 10000, month);    // 调用函数计算

        var average_month_pay = Math.round(month_money1 * 100) / 100;
        $("#average_month_pay").html(average_month_pay + '元');

        //还款总额
        var all_total1 = Math.round(month_money1 * month * 100) / 100;
        $("#pay_total_price").val(all_total1);

        //支付利息款
        var pay_lixi = (Math.round((all_total1 - daikuan_total_price*10000) * 100) / 100) / 10000;
        $("#pay_lixi").html(pay_lixi.toFixed(1) + '万元');
    }
    
    $('.toolsform-defal').hide();
    $('.toolsform-res').stop().fadeIn(350);

    return [first_pay, daikuan_total_price, pay_lixi.toFixed(1)];
}

function isLeapYear(year){

    if((year % 4 == 0 && year % 100 != 0) || (year % 400 == 0)){
        return true;
    }

    return false;
}

function isEmpty(str){
    if((str == null) || (str.length == 0)) return true;
    else return(false);
}

function adv_format(value,num)   //四舍五入
{
    var a_str = formatnumber(value,num);

    var a_int = parseFloat(a_str);

    if (value.toString().length>a_str.length)

        {

        var b_str = value.toString().substring(a_str.length,a_str.length+1)

        var b_int = parseFloat(b_str);

        if (b_int<5)

            {

            return a_str

            }

        else

            {

            var bonus_str,bonus_int;

            if (num==0)

                {

                bonus_int = 1;

                }

            else

                {

                bonus_str = "0."

                for (var i=1; i<num; i++)

                    bonus_str+="0";

                bonus_str+="1";

                bonus_int = parseFloat(bonus_str);

                }

            a_str = formatnumber(a_int + bonus_int, num)

            }

        }

        return a_str
}

/* 房贷计算饼图 */
function chartPie(analyzeResult)
{
    var pieChart = echarts.init(document.getElementById('pieChart'));

    var name = ['参考首付','贷款金额','支付利息'];
    var colorArr = ['#6DA9DE', '#FBB01F', '#78C340'];
    var data = [];

    for(var i = 0; i < analyzeResult.length; i++)
    {
        data.push({
            'value' : analyzeResult[i], 
            'name' : name[i],
            'itemStyle' : 
            {
                normal: {
                    color: colorArr[i]
                }
            }
        });
    }

    /*pieChart.showLoading({
     text: '正在努力的读取数据中...'
     });*/

    var option = {
        tooltip : {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        calculable : true,
        series : [
            {
                name:'',
                type:'pie',
                radius : ['50%', '70%'],
                itemStyle : {
                    normal : {
                        label : {
                            show : false
                        },
                        labelLine : {
                            show : false
                        }
                    },
                    emphasis : {
                        label : {
                            show : true,
                            position : 'center',
                            textStyle : {
                                fontSize : '30',
                                fontWeight : 'bold'
                            }
                        }
                    }
                },
                data : data
            }
        ]
    };

    pieChart.setOption(option);
}



