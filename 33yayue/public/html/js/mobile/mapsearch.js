var isfirstload = true;

map = new BMap.Map("map-content");
centerPoint = new BMap.Point(baidulong, baidulati);
map.centerAndZoom(centerPoint, 14);
map.addControl(new BMap.NavigationControl());
map.enableScrollWheelZoom(true);
map.enableDragging();
map.setMinZoom(10);

//获取用户的当前地址
var geoc = new BMap.Geocoder();
var geolocation = new BMap.Geolocation();
geolocation.getCurrentPosition(function (r) {
    if (this.getStatus() == BMAP_STATUS_SUCCESS) {
        //重新设置中心点
        //console.log(r.point);
        centerPoint = r.point;
        map.centerAndZoom(centerPoint, 14);
        //加个图标
        var marker = new BMap.Marker(centerPoint);
        map.addOverlay(marker);
    }
});

//当移动屏幕、缩放的时候都需要重新获取地址
map.addEventListener("dragend", function () {
    //拖放事件
    //console.log("我拖放了");
    Housesajax();
});

map.addEventListener("zoomend", function () {
    //缩放事件
    //console.log("我缩放了");
    Housesajax();
});

//第一次加载完成
map.addEventListener('tilesloaded', loadcomplate);


//页面加载完成
function loadcomplate() {
    //加载完成了
    if (isfirstload) {
        Housesajax();
        isfirstload = false;
        map.removeEventListener('tilesloaded', loadcomplate)
    }
}

//获取周边的房源
function Housesajax() {
    var bs = map.getBounds();   //获取可视区域
    var bssw = bs.getSouthWest();   //可视区域左下角
    var bsne = bs.getNorthEast();   //可视区域右上角
    var zoom = map.getZoom();

    layer.msg("加载中...", { time: 0 });
    $.ajax({
        type: "get",
        url: "/handler.ashx?action=maplist",
        data: {
            cityid: cityid,
            districtid: "",
            price: "",
            propertytype: "",
            status: "",
            kw: "",
            lngstart: bssw.lng,
            lngend: bsne.lng,
            latstart: bssw.lat,
            latend: bsne.lat,
            zoom: zoom
        },
        dataType: "json",
        success: function (json) {
            layer.closeAll();
            if (json.success) {
                //添加覆盖物
                map.clearOverlays();
                //设置中心点
                map.addOverlay(new BMap.Marker(centerPoint));
                //添加其他坐标
                if (json.data.length <= 0) {
                    layer.msg("可视范围内没有找到楼盘。");
                } else {
                    if (json.data.length >= 100) {
                        var tiplayer = layer.msg("最多显示100个楼盘", { time: 1000, offset: "70px" });
                    } else {
                        var tiplayer = layer.msg("找到" + json.data.length + "个楼盘", { time: 1000, offset: "70px" });
                    }
                    $(json.data).each(function (i, ite) {
                        //var myCompOverlay = new ComplexCustomOverlay(ite.BaiduLongitude, ite.BaiduLatitude, ite.ProjectID, ite.ProjectName);
                        //mp.addOverlay(myCompOverlay);

                        var labelHtml = '<div class="label-container"><div class="label-wrapper bounce " data-id="' + ite.ProjectID + '"><div class="self-label">' + ite.ProjectName + '</div><i class="label-bottom-icon"></i></div></div>';
                        var opts = {
                            position: new BMap.Point(ite.BaiduLongitude, ite.BaiduLatitude)
                        };
                        var style = {
                            "margin-left": "-3.9375rem",
                            "margin-top": "-1.8rem",
                            background: "transparent",
                            border: "none"
                        };
                        addLabel(labelHtml, opts, style, i);
                        //console.log(ite.ProjectName);
                    });

                    //设置触发事件
                    $(".label-wrapper").on("touchend click", function () {
                        var dataid = $(this).attr("data-id");
                        GoProject(dataid);
                    });
                }
            } else {
                layer.msg(json.msg);
            }
        },
        error: function (data) {
            layer.closeAll();
            layer.msg('数据加载失败，网络错误');
        }
    });
}

function GoProject(id) {
    window.location.href = "/house/" + id + ".html";
}

//添加坐标
function addLabel(labelHtml, labelOpts, style, index) {
    var label = new BMap.Label(labelHtml, labelOpts);
    label.setStyle(style);
    label.setZIndex(index);
    map.addOverlay(label);

    //console.log("加啊加啊");
}