/*下来刷新列表*/

$(function () {

    var itemIndex = 0;

    var tabLoadEndArray = [false];
    var tabLenghtArray = [20];
    var tabScroolTopArray = [0];
    
    // dropload
    var dropload = $('.khfxWarp').dropload({
        scrollArea: window,
        domDown: {
            domClass: 'dropload-down',
            domRefresh: '<div class="dropload-refresh">上拉加载更多</div>',
            domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
            domNoData: '<div class="dropload-noData">已无数据</div>'
        },
        loadDownFn: function (me) {
            setTimeout(function () {
                if (tabLoadEndArray[itemIndex]) {
                    me.resetload();
                    me.lock();
                    me.noData();
                    me.resetload();
                    return;
                }
                var result = '';
                for (var index = 0; index < 10; index++) {
                    if (tabLenghtArray[itemIndex] > 0) {
                        tabLenghtArray[itemIndex]--;
                    } else {
                        tabLoadEndArray[itemIndex] = true;
                        break;
                    }
                    if (itemIndex == 0) {
                        result
                        += ''
                        + '    <hgroup class="khfxRow">'
						+ '    <a href="">'
                        + '      <div class="houseli_tu"><img src="images/house.jpg"><i>海口</i></div>'
                        + '      <div class="houseli_txt">'
                        + '        <p class="houseli_title">碧桂园海棠盛世<span> | 在售</span></p>'
						+ '        <p class="houseli_hux">3居(建面40-120㎡)</p>'
                        + '        <p class="houseli_jia"><span>12500</span>元/㎡</p>'
						+ '        <p class="houseli_data">有效期：2019/5/16-2019/6/16</p>'
                        + '      </div>'
						+ '    </a>'
						+ '    <div class="houseli_tell"><a href=""><img src="images/footer.png"></a></div>'
						+ '    <div class="clear"></div>'
						+ '    <div class="houseli_biaoq">'
						+ '    <span class="houseli_guanz">已有 <em>702</em> 人关注</span>'
						+ '    <span class="houseli_tese">品牌地产</span>'
						+ '    <span class="houseli_tese">旅游地产</span>'
						+ '    <span class="houseli_tese">海景地产</span>'
						+ '	   </div>'
                        + '    </hgroup>';
                    } 
                }
                $('.khfxPane').eq(itemIndex).append(result);
                me.resetload();
            }, 500);
        }
    });


    $('.tabHead span').on('click', function () {

        tabScroolTopArray[itemIndex] = $(window).scrollTop();
        var $this = $(this);
        itemIndex = $this.index();
        $(window).scrollTop(tabScroolTopArray[itemIndex]);
        
        $(this).addClass('active').siblings('.tabHead span').removeClass('active');
        $('.tabHead .border').css('left', $(this).offset().left + 'px');
        $('.khfxPane').eq(itemIndex).show().siblings('.khfxPane').hide();

        if (!tabLoadEndArray[itemIndex]) {
            dropload.unlock();
            dropload.noData(false);
        } else {
            dropload.lock('down');
            dropload.noData();
        }
        dropload.resetload();
    });
});