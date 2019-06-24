
define(['backend'], function (Backend) {

    $('body').on('click', '[data-tips-image]', function () {
        console.log(".");
        var img = new Image();
        var imgWidth = this.getAttribute('data-width') || '480px';
        img.onload = function () {
            var $content = $(img).appendTo('body').css({background: '.fff', width: imgWidth, height: 'auto'});
            Layer.open({
                type: 1, area: imgWidth, title: false, closeBtn: 1,
                skin: 'layui-layer-nobg', shadeClose: true, content: $content,
                end: function () {
                    $(img).remove();
                },
                success: function () {

                }
            });
        };
        img.onerror = function (e) {

        };
        img.src = this.getAttribute('data-tips-image') || this.src;

    });
});


 

