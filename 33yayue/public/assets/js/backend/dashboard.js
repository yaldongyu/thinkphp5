define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');
            // 指定图表的配置项和数据
            var option = {
                            title: {
                                text: ''
                            },
                            legend: {
                                data:['楼盘数','资讯数','图片数']
                            },
                            color: ['#08e05e','#1597ef','#539745'],
                            tooltip : {
                                trigger: 'axis',
                                axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                                    type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                                }
                            },
                            grid: {
                                left: '0%',
                                right: '0%',
                                bottom: '3%',
                                containLabel: true
                            },
                            xAxis : [
                                {
                                    type : 'category',
                                    data : Orderdata.column,
                                    axisTick: {
                                        alignWithLabel: true
                                    }
                                }
                            ],
                            yAxis : [
                                {
                                    type : 'value'
                                }
                            ],
                            series : [
                                {
                                    name:'楼盘数',
                                    type:'bar',
                                    barWidth: '20%',
                                    data:Orderdata.paydata1,
                                    itemStyle : { normal: {label : {show: true,position: 'top',}}}

                                },

                                {
                                    name:'资讯数',
                                    type:'bar',
                                    barWidth: '20%',
                                    data:Orderdata.paydata2,
                                    itemStyle : { normal: {label : {show: true,position: 'top',}}}

                                },
                                {
                                    name:'图片数',
                                    type:'bar',
                                    barWidth: '20%',
                                    data:Orderdata.paydata3,
                                    itemStyle : { normal: {label : {show: true,position: 'top',}}}

                                }
                            ]
                        };        
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
            if ($("#echart").width() != $("#echart canvas").width() && $("#echart canvas").width() < $("#echart").width()) {
                    myChart.resize();
            }

            $(window).resize(function () {
                myChart.resize();
            });

            $(document).on("click", ".btn-checkversion", function () {
                top.window.$("[data-toggle=checkupdate]").trigger("click");
            });


            $('.click span').on('click',function(){
                $(this).addClass('sel').siblings().addClass('nosel');
                $(this).removeClass('nosel');
                var pos = $(this).attr('value');
                $.ajax({
                    url:'/Dashboard/getData',
                    data:{position:pos},
                    type: 'post',
                    dataType: 'json',
                    success: function (ret) {
                        myChart.setOption({
                            xAxis: {
                                data: ret.names
                            },
                            series: [{
                                name: __('楼盘数'),
                                data: ret.nums
                            },{
                                name: __('资讯数'),
                                data: ret.zxnums
                            },{
                                name: __('图片数'),
                                data: ret.picnums
                            }]
                        });
                            myChart.resize();
                    }
                });
            });


            //读取FastAdmin的更新信息和社区动态
            $.ajax({
                url: Config.fastadmin.api_url + '/news/index',
                type: 'post',
                dataType: 'jsonp',
                success: function (ret) {
                    $("#news-list").html(Template("newstpl", {news: ret.newslist}));
                }
            });
            $.ajax({
                url: Config.fastadmin.api_url + '/forum/discussion',
                type: 'post',
                dataType: 'jsonp',
                success: function (ret) {
                    $("#discussion-list").html(Template("discussiontpl", {news: ret.discussionlist}));
                }
            });
        }
    };

    return Controller;
});