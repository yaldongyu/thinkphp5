// 楼盘首页地图
$(function(){
    //获取周边配套
    $('.y_lpdt_list ul li a').on('click',function(){        
        var _data = $(this).find('p.y_text').text(); //选择中的内容
        var _longitude =$('#y_lpmap').attr('data-jwd'); //获取经纬度
        var pintx = _longitude.split(',')[0];
        var pinty = _longitude.split(',')[1];
        _longitude = new BMap.Point(pintx,pinty);
        doSearch(_data,_longitude);//调用地图弹窗
    })

 


// 百度地图API功能
var map = new BMap.Map("y_lpmap");
var _lTude =$('#y_lpmap').attr('data-jwd');         //获取经纬度
var _lJg =$('#y_lpmap').attr('data-jg');         //获取楼盘价格
var _lTitle =$('#y_lpmap').attr('data-title');         //获取楼盘标题

var pintx = _lTude.split(',')[0];
var pinty = _lTude.split(',')[1];
_lTude = new BMap.Point(pintx,pinty);

map.centerAndZoom(_lTude, 15);
map.enableScrollWheelZoom(); //启用滚轮放大缩小，默认禁用

// 复杂的自定义覆盖物
function ComplexCustomOverlay(_lTude, text, mouseoverText){
    this._point = _lTude;
    this._text = text;
    this._overText = mouseoverText;
}
ComplexCustomOverlay.prototype = new BMap.Overlay();
ComplexCustomOverlay.prototype.initialize = function(map){
    this._map = map;
    var div = this._div = document.createElement("div");
    div.style.position = "absolute";
    div.style.zIndex = BMap.Overlay.getZIndex(this._point.lat);
    div.style.backgroundColor = "#fff";
    div.style.border = "1px solid #e1e1e1";
    div.style.color = "666";
    // div.style.height = "18px";
    div.style.padding = "5px 10px";
    // div.style.lineHeight = "10px";
    div.style.whiteSpace = "nowrap";
    // div.style.MozUserSelect = "none";
    div.style.fontSize = "14px"
    var span = this._span = document.createElement("span");
    div.appendChild(span);
    span.appendChild(document.createTextNode(this._text));
    var that = this;

    var arrow = this._arrow = document.createElement("div");
    arrow.style.background = "url(/html/img/mobile/ico_map1.png) no-repeat center";
    arrow.style.backgroundSize = "100%";
    arrow.style.position = "absolute";
    arrow.style.width = "20px";
    arrow.style.height = "28px";
    arrow.style.top = "30px";
    arrow.style.left = "25px";
    arrow.style.overflow = "hidden";
    div.appendChild(arrow);

    div.onmouseover = function(){
        this.style.backgroundColor = "#6BADCA";
        this.style.borderColor = "#3487ab";
        this.getElementsByTagName("span")[0].innerHTML = that._overText;
        arrow.style.backgroundPosition = "0px -20px";
    }

    div.onmouseout = function(){
        this.style.backgroundColor = "#EE5D5B";
        this.style.borderColor = "#BC3B3A";
        this.getElementsByTagName("span")[0].innerHTML = that._text;
        arrow.style.backgroundPosition = "0px 0px";
    }

    map.getPanes().labelPane.appendChild(div);

    return div;
}
ComplexCustomOverlay.prototype.draw = function(){
    var map = this._map;
    var pixel = map.pointToOverlayPixel(this._point);
    this._div.style.left = pixel.x - parseInt(this._arrow.style.left) -13 + "px";
    this._div.style.top  = pixel.y - 45 + "px";
}
var txt = _lTitle, mouseoverTxt = txt + " " + _lJg ;
var myCompOverlay = new ComplexCustomOverlay(_lTude, _lTitle ,mouseoverTxt);

map.addOverlay(myCompOverlay);

//map展现结果的地图实例
//autoViewport检索结束后是否自动调整地图视野,false 不调整地图视野
var local = new BMap.LocalSearch(map,{renderOptions:{map:map,autoViewport:true}});

//地址检索
function doSearch(Odata,Opoint){
    var address = Odata;
    local.searchNearby(address,Opoint);
}


    
})