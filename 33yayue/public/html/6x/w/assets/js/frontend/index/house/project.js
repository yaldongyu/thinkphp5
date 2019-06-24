/**********楼盘详情页 - 初始化地图***********/
function initMap(e) {
    var t, n, a = {},
			i = 2e3,
			o = e.tabEle,
			r = e.listContainer,
			s = o.first().data("key"),
			l = o.first().data("index"),
			c = o.first().data("length"),
			d = "",
			u = "",
			f = "",
			p = "",
			g = !1,
			m = [];
    load = function (e) {
        var t = document.createElement("script");
        t.src = "//api.map.baidu.com/api?v=2.0&ak=" + e + "&callback=mapInitialize", document.body.appendChild(t)
    }, mapInit = function () {
        t = new BMap.Map("map", {
            enableMapClick: true
        }), n = new BMap.Point(e.longitude, e.latitude), e.isShowToolbar && t.addControl(new BMap.NavigationControl({
            type: BMAP_NAVIGATION_CONTROL_LARGE,
            offset: new BMap.Size(19, 78)
        })), t.centerAndZoom(n, 15), setResblockOverlays(), o.first().trigger("click"), tongji()
    }, renderTagBox = function () {
        var e = s.split(","),
				t = l.split(","),
				n = c.split(","),
				a = "";
        $.each(e, function (e, i) {
            a += '<div class="tagStyle" data-bl="' + t[e] + '" data-index="' + t[e] + '" data-length="' + n[e] + '">' + i + "</div>"
        }), $(".itemTagBox").html(a), liClick()
    }, liClick = function () {
        o.on("click", function () {
            $(this).hasClass("selectTag") || (s = $(this).data("key"), l = $(this).data("index"), c = $(this).data("length"), $(this).parent().find(".selectTag").removeClass("selectTag"), $(this).addClass("selectTag"), renderTagBox(), $(".tagStyle").first().trigger("click"))
        }), $(".tagStyle").on("click", function () {
            u = $(this).text(), d = $(this).data("index"), f = $(this).data("length"), t.clearOverlays(), setResblockOverlays(), t.reset(), $("#mapListContainer").html(""), $(".loading").show(), $(this).hasClass("select") || ($(this).parent().find(".select").removeClass("select"), $(this).addClass("select"), a[d] ? render() : searchDeal(u), r.scrollTop(0))
        }), r.delegate("li", "mouseover", function () {
            var e = $(this),
					t = e.data("index");
            0 == e.hasClass("itemBlue"), cancelBlue("hover"), setBlue(t, "hover")
        }), r.delegate("li", "mouseout", function () {
            cancelBlue("hover")
        }), r.delegate("li", "click", function () {
            var e = $(this).data("index"),
					n = $(this).data("address").split(","),
					i = new BMap.Point(n[0], n[1]),
					o = t.getBounds(),
					r = $(this).index(),
					s = a[d][r];
            p = e, cancelBlue("click"), renderMarkerDetail(e, s), setBlue(e, "click"), 1 != o.containsPoint(i) && (t.setViewport([i]), t.setZoom(16))
        })
    }, searchDeal = function (e) {
        var o = e;
        bdLocalSearch = new BMap.LocalSearch(t), bdLocalSearch.searchNearby(o, n, i), bdLocalSearch.setSearchCompleteCallback(function (e) {
            var t = [];
            if (bdLocalSearch.getStatus() == BMAP_STATUS_SUCCESS) for (var n = 0; n < e.getCurrentNumPois(); n++) t.push(e.getPoi(n));
            a[d] = t.filter(function (e) {
                return "null" != e.address
            }), calcDistance()
        })
    }, calcDistance = function () {
        var e = a[d],
				t = new BMap.MercatorProjection,
				i = t.lngLatToPoint(n);
        $.each(e, function (e, n) {
            var a = t.lngLatToPoint(n.point),
					o = Math.round(Math.sqrt(Math.pow(i.x - a.x, 2) + Math.pow(i.y - a.y, 2)));
            n.distance = o + "米"
        }), sortList(), rangeDeal()
    }, sortList = function () {
        $.each(a, function (e, t) {
            t.sort(function (e, t) {
                return parseFloat(e.distance) - parseFloat(t.distance)
            })
        })
    }, rangeDeal = function () {
        var e = a[d],
				t = e.filter(function (e) {
				    return parseFloat(e.distance) < 2e3 && "null" != e.address
				}),
				n = f >= t.length ? t.length : f - t.length;
        t.splice(n), a[d] = t, render()
    }, tongji = function () {
        var e = 0;
        $(window).bind("scroll", function () {
            var t = $("body").scrollTop();
            t > 5265 && 0 == e && 5855 > t ? (e = 1, window.$ULOG && $ULOG.send("10242", {
                action: {
                    ljweb_bl: "mapArea"
                }
            })) : (t > 5855 || 5265 > t) && (e = 0)
        }), t.addEventListener("zoomend", function () {
            var e = this.getZoom();
            e > 15 && window.$ULOG && $ULOG.send("10242", {
                action: {
                    ljweb_bl: "zoomBig"
                }
            }), 15 > e && window.$ULOG && $ULOG.send("10242", {
                action: {
                    ljweb_bl: "zoomSmall"
                }
            })
        }), t.addEventListener("click", function (e) {
            g || $(".showMarkerDetail").remove(), g = !1
        })
    }, setResblockOverlays = function () {
        var a = "<div class='name'>" + e.resblockName + "<i class='arrow'></i></div>",
				i = new BMap.Label(a, {
				    position: n,
				    offset: new BMap.Size(-8, -55)
				});
        i.setStyle({
            border: 0,
            backgroundColor: "transparent"
        }), t.addOverlay(i)
    }, render = function () {
        var e = a[d],
				n = "";
        if (t.clearOverlays(), setResblockOverlays(), e.length > 0) {
            var i = "";
            $.each(e, function (e, t) {
                var n = "<div  class='itemContent'> <span class='micon-" + d + "'></span><span class='itemText itemTitle'>" + t.title + "</span><span class='micon-distance'></span><span class='itemText itemdistance'>" + t.distance + "</span></div><div class='itemInfo'>" + t.address + "</div>";
                i += "<li data-index=" + d + e + " data-address=" + t.point.lng + "," + t.point.lat + " title=" + t.title + "><div class='contentBox'>" + n + "</div></li>", addItemOverlays("micon-" + d, d + e, t), m.push(t.point)
            }), n += "<ul class='itemBox'>" + i + "</ul>"
        }
        n = "" != n ? n : "<div class='nullSupport'>很抱歉，该配套下无相关内容，请查看其它配套</div>", $("#mapListContainer").html(n), $(".aroundList .name").eq(0).css("border-top", "none"), $(".loading").hide()
    }, addItemOverlays = function (e, n, a) {
        var i = "<i class='item " + e + "' data-label='" + n + "' title='" + a.title + "'></i>",
				o = new BMap.Label(i, {
				    position: a.point,
				    offset: new BMap.Size(-17, -40)
				});
        o.setStyle({
            border: 0,
            backgroundColor: "transparent"
        }), t.addOverlay(o), $(".BMapLabel").eq(0).css("z-index", 2), labelClick(o, n, a)
    }, renderMarkerDetail = function (e, n) {
        var a = "<div  class='itemContent'> <span class='micon-" + d + "'></span><span class='itemText itemTitle'>" + n.title + "</span><span class='micon-distance'></span><span class='itemText itemdistance'>" + n.distance + "</span></div><div class='itemInfo'>" + n.address + "</div>",
				i = $(".aroundMap").offset().top,
				o = $(".blueLabel").offset().top,
				r = '<div class="makerDetailStyle" data-detail="' + e + '">' + a + '<span class="detailArrow"></span></div>';
        $(".labelUp").append(r);
        var s = $(".makerDetailStyle").height(),
				l = i + s + 80,
				c = -parseInt(s) - parseInt($(".blueLabel").height()) - 20;
        l > o && t.panBy(0, l - o), $(".makerDetailStyle").css("top", c)
    }, labelClick = function (e, t, n) {
        e.addEventListener("click", function (e) {
            var a = e || window.event;
            p = t, cancelBlue("click"), renderMarkerDetail(t, n), setBlue(t, "click"), scrollTop(t), g = !0, a.stopPropagation ? a.stopPropagation() : a.cancelBubble = !0
        }), e.addEventListener("mouseover", function (e) {
            cancelBlue("hover"), setBlue(t, "hover")
        }), e.addEventListener("mouseout", function (e) {
            cancelBlue("hover")
        })

    }, scrollTop = function (e) {
        for (var t = 0, n = r.find("li"), a = 0; a < n.length; a++) {
            var i = n.eq(a).data("index");
            if (i == e) return r.scrollTop(t), !1;
            t += n.eq(a).height() + 20
        }
    }, cancelBlue = function (e) {
        "click" == e ? ($(".contentBox").removeClass("contentActive"), $(".itemText").removeClass("itemActive"), $(".itemInfo").removeClass("itemActive"), $(".makerDetailStyle").remove()) : r.find("li").css("backgroundColor", "#fff"), $(".BMapLabel").removeClass("labelUp"), $(".BMapLabel .item").removeClass("blueLabel"), p && setBlue(p, "click")
    }, setBlue = function (e, t) {
        var n = $('[data-index="' + e + '"]'),
				a = $('[data-label="' + e + '"]'),
				i = $('[data-detail="' + e + '"]');
        "click" == t ? (n.find(".contentBox").addClass("contentActive"), n.find(".itemText").addClass("itemActive"), n.find(".itemInfo").addClass("itemActive"), i.removeClass("hideMarkerDetail").addClass("showMarkerDetail")) : n.css("backgroundColor", "#f6f6f6"), a.parent().addClass("labelUp"), a.addClass("blueLabel")
    }, load(e.ak), window.mapInitialize = mapInit, renderTagBox();
}

function ShowLayout(counts) {
    //设置选中
    $("#layoutcountlist a").removeClass("select");
    $("#layoutcountlist #layoutcount" + counts).addClass("select");
    //加载数据
    var infolayouthtml = new Array();
    infolayouthtml.push('<ul class="picList">');
    for (var i = 0; i < lstlayout.length; i++) {
        var infolayout = lstlayout[i];
        if (counts == "" || counts == infolayout.CountS) {
            infolayouthtml.push(SetTableBody(infolayout));
        }
    }
    infolayouthtml.push("</ul>");
    $("#layoutlist .bd").empty();
    $("#layoutlist .bd").append(infolayouthtml.join(""));
    //调用效果
    $("#layoutlist").slide({ titCell: ".hd ul", mainCell: ".bd ul", autoPage: true, effect: "left", autoPlay: true, vis: 4, interTime: 6000 });
    //设置值
    //$("#layoutcounts").val(counts);
    //延迟加载
    //$("img.lazy2").lazyload({ effect: "fadeIn" });
}

function ShowLayoutDetail(layoutid) {
    var layoutcounts = $("#layoutcounts").val();
    //清空数据
    newlstlayout = [];
    //获取户型列表
    var selectindex = 0;
    for (var i = 0; i < lstlayout.length; i++) {
        var infolayout = lstlayout[i];
        if (layoutcounts == "" || layoutcounts == infolayout.CountS) {
            newlstlayout.push(infolayout);
            if (infolayout.LayoutID == layoutid)
            { currentlayoutindex = selectindex; }
            else
            { selectindex = selectindex + 1; }
        }
    }

    //console.log("总数据=" + newlstlayout.length + "；当前index=" + currentlayoutindex);
    //设置参数
    SetLayoutInfo(newlstlayout[currentlayoutindex]);
    $("#layer_layout_total").text(newlstlayout.length);
    $("#layer_layout_current").text((currentlayoutindex + 1));
    if (currentlayoutindex > 0)
    { $("#layer_layout_prev").show(); }
    else
    { $("#layer_layout_prev").hide(); }
    if (currentlayoutindex < newlstlayout.length - 1)
    { $("#layer_layout_next").show(); }
    else
    { $("#layer_layout_next").hide(); }

    //弹出图层
    layer.open({
        type: 1,
        shade: 0.4,
        title: false,
        content: $('#layer_layout'),
        area: ['1040px', '700px'],
        cancel: function () { }
    });
}

function ShowLayoutPrev() {
    currentlayoutindex = currentlayoutindex - 1;
    SetLayoutInfo(newlstlayout[currentlayoutindex]);
    $("#layer_layout_current").text((currentlayoutindex + 1));
    if (currentlayoutindex > 0)
    { $("#layer_layout_prev").show(); }
    else
    { $("#layer_layout_prev").hide(); }
    if (currentlayoutindex < newlstlayout.length - 1)
    { $("#layer_layout_next").show(); }
    else
    { $("#layer_layout_next").hide(); }
}

function ShowLayoutNext() {
    currentlayoutindex = currentlayoutindex + 1;
    SetLayoutInfo(newlstlayout[currentlayoutindex]);
    $("#layer_layout_current").text((currentlayoutindex + 1));
    if (currentlayoutindex > 0)
    { $("#layer_layout_prev").show(); }
    else
    { $("#layer_layout_prev").hide(); }
    if (currentlayoutindex < newlstlayout.length - 1)
    { $("#layer_layout_next").show(); }
    else
    { $("#layer_layout_next").hide(); }
}

function SetLayoutInfo(infolayout) {
    $("#layer_layout_title").text(infolayout.LayoutName);
    if (infolayout.IsRecommend == "1") {
        $("#layer_layout_recommand").show();
    } else {
        $("#layer_layout_recommand").hide();
    }
    switch (infolayout.LayoutStatus) {
        case "1": $("#layer_layout_status").removeClass().addClass("card-presell").text("待售"); break;
        case "2": $("#layer_layout_status").removeClass().addClass("card-onsale").text("在售"); break;
        case "3": $("#layer_layout_status").removeClass().addClass("card-sellout").text("售罄"); break;
    }
    $("#layer_layout_image").attr("src", infolayout.ImagePathOri);
    $("#layer_layout_imagelink").attr("href", infolayout.ImagePathOri);
    $("#layer_layout_faceto").text(infolayout.FaceTo);
    $("#layer_layout_area").text(infolayout.BuildingSquare + '平米' + (infolayout.UsageSquare != "" ? "，使用面积" + infolayout.UsageSquare + "平米" : ""));
    $("#layer_layout_counts").text(infolayout.CountS + '室' + infolayout.CountT + '厅' + infolayout.CountW + '卫');
    $("#layer_layout_intro").html(infolayout.LayoutIntro);
    var strInfo = "";
    if (infolayout.LayoutFeature != "") {
        var lstfeature = infolayout.LayoutFeature.split(',');
        for (var i = 1; i < lstfeature.length; i++) {
            if (lstfeature[i] != "") {
                strInfo = strInfo + "<span class=\"tag" + (i + 1) + "\">" + lstfeature[i] + "</span>";
            }
        }
    }
    $("#layer_layout_feature").html(strInfo);
}

function SetTableBody(modlayout) {
    var bU = new Array();
    bU.push('<li id="layout_' + modlayout.CountS + '_' + modlayout.LayoutID + '">');
    bU.push('    <a href="/project/project_detail_layout.aspx?projectid=' + projectid + '&layoutid=' + modlayout.LayoutID + '"\">');
    bU.push('        <div class="pic">');
    bU.push('            <img src="' + modlayout.ImagePathSma + '" />');
    switch (modlayout.LayoutStatus) {
        case "1": bU.push('<span class="card-presell">待售</span>'); break;
        case "2": bU.push('<span class="card-onsale">在售</span>'); break;
        case "3": bU.push('<span class="card-sellout">售罄</span>'); break;
    }
    if (modlayout.IsRecommend == "1") bU.push('<span class="card-recommand">推荐</span>');
    bU.push('        </div>');
    bU.push('        <div class="title">' + modlayout.LayoutName + ' ' + modlayout.CountS + '室' + modlayout.CountT + '厅' + modlayout.CountW + '卫</div>');
    bU.push('        <div class="intro">');
    if (modlayout.BuildingSquare != "") { bU.push(modlayout.BuildingSquare + '平米 '); }
    bU.push(modlayout.FaceTo);
    bU.push('        </div>');
    bU.push('    </a>');
    bU.push('</li>');
    return bU.join("");
}