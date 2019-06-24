/*下拉刷新列表样式*/

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
                        + '      <div class="newsli_tu"><img src="images/video.jpg"></div>'
                        + '      <div class="newsli_txt">'
                        + '        <p>南海幸福汇加推建面为63-96平米一至两房户型，总价99.1万/套起</p>'
                        + '        <p>'
						+ '        <span class="newsli_lei">楼盘热点</span>'
						+ '        <span class="newsli_time">2019-5-9</span>'
						+ '		   </p>'
                        + '      </div>'
						+ '    </a>'
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