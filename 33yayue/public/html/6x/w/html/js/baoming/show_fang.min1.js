init: function () {
		var t = $(this.voiceEl);
		return t.length < 1 ? !1 : (this.voiceCheck(), this.bufferedChange(), void this.voiceControl())
	},
	voiceCheck: function () {
		var t = this;
		$(this.voiceEl)[0].load(), $(this.voiceEl).on("canplay", function () {
			return t.load ? (t.duration = $(this)[0].duration, t.setTime(t.timeConversion(t.duration), !0), t.load = !1, void(t.ready = !0)) : !1
		}), $(this.voiceEl).on("canplaythrough", function () {
			t.bufferComplete = !0
		}), $(this.voiceEl).on("timeupdate", function () {
			return t.dragMove ? !1 : void t.progressBarChange($(this)[0].currentTime, "time")
		}), $(this.voiceEl).on("playing", function () {
			$(".voice-play").removeClass("switch-play").addClass("switch-pause")
		}), $(this.voiceEl).on("pause", function () {
			$(".voice-play").removeClass("switch-pause").addClass("switch-play")
		})
	},
	voiceControl: function () {
		var t = this,
			e = $(t.voiceEl)[0];
		this.progressBarWidth = parseInt($(".voice-progressBar").css("width").replace("px", "")), this.progressBarStart = parseInt($(".voice-progressBar").offset().left), this.progressBarEnd = this.progressBarWidth + this.progressBarStart, this.volumeLength = parseInt($(".voice-volume-progressBar").css("width").replace("px", "")), this.getMuted(e), $(".voice-cover .play-control").on("click", function () {
			return t.ready ? void(e.paused ? e.play() : e.pause()) : !1
		}), $("body").on("dragstart", function () {
			return !1
		}), $(".voice-control-FR").on("click", function () {
			if (!t.ready || !t.lock) return !1;
			if (e.paused) return !1;
			var i = e.currentTime,
				s = Math.floor(i) - 5 <= 0;
			e.currentTime = s ? 0 : i - 5
		}), $(".voice-control-FF").on("click", function () {
			if (!t.ready || !t.lock) return !1;
			if (e.paused) return !1;
			var i = e.currentTime,
				s = t.duration - (Math.floor(i) + 5) <= 0;
			e.currentTime = s ? t.duration : i + 5
		}), $(".voice-progressBar").on("mousedown", function (i) {
			if (!t.ready || !t.lock) return !1;
			var s = i;
			if (0 != s.button) return !1;
			t.progressClick = !0, e.paused || (t.play = !0), e.pause();
			var n = s.offsetX,
				o = n / t.progressBarWidth;
			t.progressBarChange(o), t.dragMove = !0, $("body").on("selectstart", function () {
				return !1
			}), $(document).on("mousemove", function (i) {
				if (!t.ready || !t.dragMove) return !1;
				var s = i,
					n = $(".voice-shadow");
				n.length < 1 ? $(".voice-cover").append(t.shadow) : n.show();
				var o = s.clientX;
				t.progressDrag(e, o)
			})
		}), $(document).on("mouseup", function (i) {
			var s, n = $(".voice-shadow");
			if (!t.ready) return !1;
			var o = i;
			return 0 != o.button ? !1 : t.dragMove ? (s && clearTimeout(s), n.length > 0 && n.hide(), s = setTimeout(function () {
				e.currentTime = t.currentTime, t.dragMove = !1, t.play && (e.play(), t.play = !1)
			}, 200), $(document).unbind("mousemove"), $("body").unbind("selectstart"), !1) : void 0
		}), $(".voice-progressBar-button").on("mousedown", function (i) {
			if (!t.ready || !t.lock) return !1;
			var s = i;
			return 0 != s.button ? !1 : (t.progressClick = !0, e.paused || (t.play = !0), e.pause(), t.dragMove = !0, $(document).on("mousemove", function (i) {
				if (!t.ready || !t.dragMove) return !1;
				var s = i,
					n = s.clientX;
				return t.progressDrag(e, n), !1
			}), !1)
		})
	},
	timeConversion: function (t) {
		var e = Math.floor(t / 60),
			i = Math.floor(t % 60);
		return i = 10 > i ? "0" + String(i) : i, e = 10 > e ? "0" + String(e) : e, {
			min: e,
			s: i
		}
	},
	getMuted: function (t) {
		var e = this;
		t.muted && (t.muted = !1), $(".voice-volume-switch").on("click", function (i) {
			if (!e.ready) return !1;
			var s = i;
			return 0 != s.button ? !1 : (t.muted = t.muted ? !1 : !0, void(t.muted ? ($(this).removeClass("volume-switch-off").addClass("volume-switch-on"), $(".voice-volume-progressBar-now").removeClass("voice-volume-progressBar-now-freeze")) : ($(this).removeClass("volume-switch-on").addClass("volume-switch-off"), $(".voice-volume-progressBar-now").addClass("voice-volume-progressBar-now-freeze"))))
		}), $(".voice-volume-progressBar").on("mousedown", function (i) {
			if (!e.ready) return !1;
			var s = i;
			if (0 != s.button) return !1;
			var n = s.offsetX,
				o = n / e.volumeLength;
			t.volume = o, e.setMuted(o)
		})
	},
	setMuted: function (t) {
		t = (100 * t).toFixed(2), $(".voice-volume-progressBar-now").css("width", t + "%")
	},
	progressBarChange: function (t, e) {
		t = e && "time" == e ? t : t * this.duration;
		var i = $(".pop"),
			s = $(this.voiceEl).attr("data-over");
		"show" == s && s && (this.lock = !0);
		var n = 0 == t ? 0 : (t / this.duration * 100).toFixed(2);
		return t >= this.limitTime && !this.lock ? ($(this.voiceEl)[0].pause(), i.is(":hidden") && $(this.voiceEl).siblings(".up-pop").click(), !1) : (Math.floor(t) != this.nowTime && (this.setTime(this.timeConversion(t)), this.nowTime = Math.floor(t)), (n || 0 == n) && $(".voice-progressBar-current").css("width", n + "%"), void(this.currentTime = t))
	},
	progressDrag: function (t, e) {
		var i = parseInt($(".voice-progressBar").offset().left);
		this.progressBarStart = this.progressBarStart == i ? this.progressBarStart : i, this.progressBarEnd = this.progressBarWidth + this.progressBarStart;
		var s = (e - this.progressBarStart) / this.progressBarWidth;
		return e <= this.progressBarStart ? (this.progressBarChange(0), !1) : e >= this.progressBarEnd ? (this.progressBarChange(1), !1) : void this.progressBarChange(s)
	},
	bufferedChange: function () {
		var t = this,
			e = $(".voice-progressBar-load"),
			i = setInterval(function () {
				if (t.bufferComplete) return clearInterval(i), e.animate({
					width: "100%"
				}, 1e3), !1;
				var s = $(t.voiceEl)[0].buffered.length;
				e.animate({
					width: 100 * s + "%"
				}, 100)
			}, 1e3)
	},
	setTime: function (t, e) {
		var i = e ? ".voice-time-all" : ".voice-time-now";
		$(i + " .time-m").text(t.min), $(i + " .time-s").text(t.s)
	}
}, videoGET.prototype.init = function () {
	var t = !0,
		e = this;
	try {
		{
			$(this.el)[0].canPlayType
		}
	} catch (i) {
		t = !1
	}
	if (!t) return !1;
	if (0 == $(this.el).length) return !1;
	$(this.el)[0].paused;
	return 0 == $(this.el).length ? !1 : ($(this.el)[0].load(), this.loadReady(), $(this.el).on("timeupdate", function () {
		e.progressChange($(this)[0].currentTime)
	}), $(this.erweima).children(".close").on("click", function () {
		e.erweimaHide()
	}), $(this.el).on("ended", function () {
		e.erweimaShow()
	}), $(this.playel).on("click", function () {
		e.playChange($(this))
	}), void $(this.playel).hover(function () {
		$(e.conf).fadeIn()
	}, function () {
		var t = $(e.el)[0].paused;
		t || $(e.conf).fadeOut(1500)
	}))
}, videoGET.prototype.playChange = function () {
	var t = $(this.el),
		e = $(this.el)[0].paused;
	e ? ($(this.conf).removeClass(this.playIcon).addClass(this.pauseIcon), t[0].play()) : ($(this.conf).removeClass(this.pauseIcon).addClass(this.playIcon), t[0].pause())
}, videoGET.prototype.progressChange = function (t) {
	if (t = parseFloat(t), 0 == t) return !1;
	var e = this.time - t <= 0 ? !0 : !1;
	return this.playing ? ($(this.el).attr("controls", "controls"), $(this.playel).hide()) : this.playing = "show" == $(this.el).data("over") ? !0 : !1, e && !this.playing ? ($(this.el).siblings(".up-pop").click(), $(this.conf).removeClass(this.pauseIcon).addClass(this.playIcon).fadeIn(), $(this.el)[0].pause(), !1) : void 0
}, videoGET.prototype.loadReady = function () {
	{
		var t = this;
		$(this.el)[0]
	}
	$(this.el).on("canplay", function () {
		var e = parseInt($(this)[0].duration);
		t.duration = e, t.num = parseFloat(e / 100)
	})
}, videoGET.prototype.erweimaShow = function () {
	var t = $(this.erweima);
	return !t || t.length <= 0 ? !1 : ($(this.el)[0].controls = !1, void $(this.erweima).fadeIn())
}, videoGET.prototype.erweimaHide = function () {
	var t = this;
	$(this.erweima).fadeOut(), setTimeout(function () {
		$(t.el).attr("controls", "controls")
	}, 500)
};