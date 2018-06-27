;(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    }
    else if (typeof exports === 'object') {
        module.exports = factory(require("jquery"));
    }
    else {
        factory(jQuery);
    }
}
(function($) {
    "use strict";
	
	var oPlugin = '';
	
    var pluginName = "tinyscrollbar"
    ,   defaults = {
            axis : 'y'
        ,   wheel : true
        ,   scrollbarVisable : true
        ,   scrollEndEventFunc : function(){}
        ,   scrollStartEventFunc : function(){}
        ,   wheelSpeed : 10
        ,   wheelLock : true
        ,   touchLock : true
        ,   trackSize : false
        ,   thumbSize : false
        ,   thumbSizeMin : 20
        }
    ;

    function Plugin($container, options) {
        /**
         * The options of the carousel extend with the defaults.
         *
         * @property options
         * @type Object
         */
        this.options = $.extend({}, defaults, options);

        /**
         * @property _defaults
         * @type Object
         * @private
         * @default defaults
         */
        this._defaults = defaults;

        /**
         * @property _name
         * @type String
         * @private
         * @final
         * @default 'tinyscrollbar'
         */
        this._name = pluginName;

        var self = this
        ,   $viewport = $container.find(".J-tinyscrollbar-viewport")
        ,   $overview = $container.find(".J-tinyscrollbar-overview")
        ,   $scrollbar = $container.find(".J-tinyscrollbar-scrollbar")
        ,   $track = $scrollbar.find(".J-tinyscrollbar-track")
        ,   $thumb = $scrollbar.find(".J-tinyscrollbar-thumb")

        ,   hasTouchEvents = ("ontouchstart" in document.documentElement)
        ,   wheelEvent = "onwheel" in document.createElement("div") ? "wheel" : // Modern browsers support "wheel"
                         document.onmousewheel !== undefined ? "mousewheel" : // Webkit and IE support at least "mousewheel"
                         "DOMMouseScroll" // let's assume that remaining browsers are older Firefox
        ,   isHorizontal = this.options.axis === 'x'
        ,   sizeLabel = isHorizontal ? "width" : "height"
        ,   posiLabel = isHorizontal ? "left" : "top"

        ,   mousePosition = 0
        ;

        /**
         * The position of the content relative to the viewport.
         *
         * @property contentPosition
         * @type Number
         */
        this.contentPosition = 0;
        this._prevContentPosition = -1;

        /**
         * The height or width of the viewport.
         *
         * @property viewportSize
         * @type Number
         */
        this.viewportSize = 0;

        /**
         * The height or width of the content.
         *
         * @property contentSize
         * @type Number
         */
        this.contentSize = 0;

        /**
         * The ratio of the content size relative to the viewport size.
         *
         * @property contentRatio
         * @type Number
         */
        this.contentRatio = 0;

        /**
         * The height or width of the content.
         *
         * @property trackSize
         * @type Number
         */
        this.trackSize = 0;

        /**
         * The size of the track relative to the size of the content.
         *
         * @property trackRatio
         * @type Number
         */
        this.trackRatio = 0;

        /**
         * The height or width of the thumb.
         *
         * @property thumbSize
         * @type Number
         */
        this.thumbSize = 0;

        /**
         * The position of the thumb relative to the track.
         *
         * @property thumbPosition
         * @type Number
         */
        this.thumbPosition = 0;

        /**
         * Will be true if there is content to scroll.
         *
         * @property hasContentToSroll
         * @type Boolean
         */
        this.hasContentToSroll = false;
		
        this.isNeverScroll = true;
				
        /**
         * @method _initialize
         * @private
         */
        function _initialize() {
            self.update();
            _setEvents();

            return self;
        }

        /**
         * You can use the update method to adjust the scrollbar to new content or to move the scrollbar to a certain point.
         *
         * @method update
         * @chainable
         * @param {Number|String} [scrollTo] Number in pixels or the values "relative" or "bottom". If you dont specify a parameter it will default to top
         */
        this.update = function(scrollTo) {
            var sizeLabelCap = sizeLabel.charAt(0).toUpperCase() + sizeLabel.slice(1).toLowerCase();
            this.viewportSize = $viewport[0]['offset'+ sizeLabelCap];
            this.contentSize = $overview[0]['scroll'+ sizeLabelCap];
            this.contentRatio = this.viewportSize / this.contentSize;
            this.trackSize = this.options.trackSize || this.viewportSize;
            this.thumbSize = Math.min(this.trackSize, Math.max(this.options.thumbSizeMin, (this.options.thumbSize || (this.trackSize * this.contentRatio))));
            this.trackRatio = (this.contentSize - this.viewportSize) / (this.trackSize - this.thumbSize);
            this.hasContentToSroll = this.contentRatio < 1;

            $scrollbar.toggleClass("disable", !this.hasContentToSroll);

            switch (scrollTo) {
                case "bottom":
                    this.contentPosition = Math.max(this.contentSize - this.viewportSize, 0);
                    break;

                case "relative":
                    this.contentPosition = Math.min(Math.max(this.contentSize - this.viewportSize, 0), Math.max(0, this.contentPosition));
                    break;

                default:
                    //this.contentPosition = parseInt(scrollTo, 10) || 0;
                    this.contentPosition = scrollTo || 0;
            }

            this.thumbPosition = this.contentPosition / this.trackRatio;

            _setCss();
			if(this.isEdged()){
				if(typeof(this.scrollEndEventFunc) != 'undefined' && _isAtBegin()){
					this.scrollEndEventFunc();
				}
				if(typeof(this.scrollStartEventFunc) != 'undefined' && _isAtEnd()){
					this.scrollStartEventFunc();
				}
			}
            return self;
        };
		
		this.isScrollable = function(){
			return this.viewportSize / this.contentSize < 1;
		}
		
		this.next = function(stepSize){
			self._prevContentPosition = self.contentPosition;
			var contentPosition = self.contentPosition + stepSize;
			if(contentPosition > self.contentSize - self.viewportSize){
				contentPosition = self.contentSize - self.viewportSize;
			}
			this.update(contentPosition);
		};
		
		this.prev = function(stepSize){
			self._prevContentPosition = self.contentPosition;
			var contentPosition = self.contentPosition - stepSize;
			if(contentPosition < 0){
				contentPosition = 0;
			}
			this.update(contentPosition);
		};
		
		this.isEdged = function(){
			if(this._prevContentPosition == this.contentPosition){
				return true;
			}else{
				return false;
			}
		};

        /**
         * @method _setCss
         * @private
         */
        function _setCss() {
            $thumb.css(posiLabel, self.thumbPosition);
            $overview.css(posiLabel, -self.contentPosition);
            $scrollbar.css(sizeLabel, self.trackSize);
            $track.css(sizeLabel, self.trackSize);
            $thumb.css(sizeLabel, self.thumbSize);
			if(!self.hasContentToSroll){
				$scrollbar.hide();
			}else{
				$scrollbar.show();
			}
			if(!self.options.scrollbarVisable){
				$scrollbar.hide();
			}
        }

        /**
         * @method _setEvents
         * @private
         */
        function _setEvents() {
            if(hasTouchEvents) {
                $viewport[0].ontouchstart = function(event) {
                    if(1 === event.touches.length) {
                        event.stopPropagation();

                        _start(event.touches[0]);
                    }
                };
            }
            $thumb.bind("mousedown", function(event){
                event.stopPropagation();
                _start(event);
            });
            $track.bind("mousedown", function(event){
                _start(event, true);
            });

            $(window).resize(function() {
                self.update("relative");
            });

            if(self.options.wheel && window.addEventListener) {
                $container[0].addEventListener(wheelEvent, _wheel, false);
            }
            else if(self.options.wheel) {
                $container[0].onmousewheel = _wheel;
            }
        }

        /**
         * @method _isAtBegin
         * @private
         */
        function _isAtBegin() {
            return self.contentPosition > 0;
        }

        /**
         * @method _isAtEnd
         * @private
         */
        function _isAtEnd() {
            return self.contentPosition <= (self.contentSize - self.viewportSize) - 5;
        }

        /**
         * @method _start
         * @private
         */
        function _start(event, gotoMouse) {
            if(self.hasContentToSroll) {
                $("body").addClass("noSelect");

                mousePosition = gotoMouse ? $thumb.offset()[posiLabel] : (isHorizontal ? event.pageX : event.pageY);

                if(hasTouchEvents) {
                    document.ontouchmove = function(event) {
						self._prevContentPosition = self.contentPosition;
                        if(self.options.touchLock || _isAtBegin() && _isAtEnd()) {
							event.preventDefault();
                        }
                        event.touches[0][pluginName + "Touch"] = 1;
                        _drag(event.touches[0]);
                    };
                    document.ontouchend = _end;
                }
                $(document).bind("mousemove", _drag);
                $(document).bind("mouseup", _end);
                $thumb.bind("mouseup", _end);
                $track.bind("mouseup", _end);

                _drag(event);
            }
        }

        /**
         * @method _wheel
         * @private
         */
        function _wheel(event) {
			self._prevContentPosition = self.contentPosition;
            if(self.hasContentToSroll) {
                // Trying to make sense of all the different wheel event implementations..
                //
                var evntObj = event || window.event
                ,   wheelDelta = -(evntObj.deltaY || evntObj.detail || (-1 / 3 * evntObj.wheelDelta)) / 40
                ,   multiply = (evntObj.deltaMode === 1) ? self.options.wheelSpeed : 1
                ;

				if(self.isNeverScroll){
					self.contentPosition -= parseInt($container.find('.J-tinyscrollbar-overview').css('top'));
					self.isNeverScroll = false;
				}
                self.contentPosition -= wheelDelta * multiply * self.options.wheelSpeed;
                self.contentPosition = Math.min((self.contentSize - self.viewportSize), Math.max(0, self.contentPosition));
                self.thumbPosition = self.contentPosition / self.trackRatio;

                /**
                 * The move event will trigger when the carousel slides to a new slide.
                 *
                 * @event move
                 */
                $container.trigger("move");

                $thumb.css(posiLabel, self.thumbPosition);
                $overview.css(posiLabel, -self.contentPosition);

                if(self.options.wheelLock || _isAtBegin() && _isAtEnd()) {
                    evntObj = $.event.fix(evntObj);
                    evntObj.preventDefault();
                }
            }
            event.stopPropagation();
			if(self.isEdged()){
				if(typeof(self.scrollEndEventFunc) != 'undefined' && _isAtBegin()){
					self.scrollEndEventFunc();
				}
				if(typeof(self.scrollStartEventFunc) != 'undefined' && _isAtEnd()){
					self.scrollStartEventFunc();
				}
			}
        }

        /**
         * @method _drag
         * @private
         */
        function _drag(event) {
            if(self.hasContentToSroll) {
                var mousePositionNew = isHorizontal ? event.pageX : event.pageY
                ,   thumbPositionDelta = event[pluginName + "Touch"] ? 
                        (mousePosition - mousePositionNew) : (mousePositionNew - mousePosition)
                ,   thumbPositionNew = Math.min((self.contentSize) - (self.trackSize - self.thumbSize), Math.max(0, self.thumbPosition + thumbPositionDelta))
                ;
				self.trackRatio = 1;
                self.contentPosition = thumbPositionNew * self.trackRatio;
                $container.trigger("move");

                $thumb.css(posiLabel, thumbPositionNew);
                $overview.css(posiLabel, -self.contentPosition);
            }
			//if(self.isEdged()){
				if(typeof(self.scrollEndEventFunc) != 'undefined' && _isAtBegin()){
					self.scrollEndEventFunc();
				}
				if(typeof(self.scrollStartEventFunc) != 'undefined' && _isAtEnd()){
					self.scrollStartEventFunc();
				}
			//}
        }

        /**
         * @method _end
         * @private
         */
        function _end() {
            self.thumbPosition = parseInt($thumb.css(posiLabel), 10) || 0;

            $("body").removeClass("noSelect");
            $(document).unbind("mousemove", _drag);
            $(document).unbind("mouseup", _end);
            $thumb.unbind("mouseup", _end);
            $track.unbind("mouseup", _end);
            document.ontouchmove = document.ontouchend = null;
        }
		
		return _initialize();
    }

    /**
    * @class tinyscrollbar
    * @constructor
    * @param {Object} options
        @param {String} [options.axis='y'] Vertical or horizontal scroller? ( x || y ).
        @param {Boolean} [options.wheel=true] Enable or disable the mousewheel.
        @param {Boolean} [options.wheelSpeed=40] How many pixels must the mouswheel scroll at a time.
        @param {Boolean} [options.wheelLock=true] Lock default window wheel scrolling when there is no more content to scroll.
        @param {Number} [options.touchLock=true] Lock default window touch scrolling when there is no more content to scroll.
        @param {Boolean|Number} [options.trackSize=false] Set the size of the scrollbar to auto(false) or a fixed number.
        @param {Boolean|Number} [options.thumbSize=false] Set the size of the thumb to auto(false) or a fixed number
        @param {Boolean} [options.thumbSizeMin=20] Minimum thumb size.
    */
    $.fn[pluginName] = function(options) {
		var oChild = $(this).children();
		var oHtml = $('<div class="J-tinyscrollbar-scrollbar" style="border-radius:4px; position: relative; float: right; width: 8px;z-index:1;"><div class="J-tinyscrollbar-track" style="background: rgba(0,0,0,0.7); border-radius:4px; height: 100%; width: 8px; position: relative;"><div class="J-tinyscrollbar-thumb" style="background: rgba(255,255,255,0.5); border-radius:4px; width: 6px; cursor: pointer; overflow: hidden; position: absolute; top: 0; left: 1px;"></div></div></div><div class="J-tinyscrollbar-viewport" style="/*width:500px;height:200px;*/overflow: hidden; position: relative;"><div class="J-tinyscrollbar-overview" style="list-style: none; position: absolute; left: 0; top: 0; padding: 0; margin: 0;"></div></div></div><style type="text/css">.noSelect { user-select: none; -o-user-select: none; -moz-user-select: none; -khtml-user-select: none; -webkit-user-select: none; } .J-tinyscrollbar-thumb:hover{ background: rgba(255,255,255,0.6);filter:"alpha(opacity=50)"; -ms-filter:"alpha(opacity=50)";}</style>');
		$(this).prepend(oHtml);
		$(oChild).appendTo(oHtml.find('.J-tinyscrollbar-overview'));
		$(this).find('.J-tinyscrollbar-scrollbar').height($(this).height());
		$(this).find('.J-tinyscrollbar-viewport').height($(this).height());
		var w = 0;
		if(options.axis == 'y'){
			w = 0;
			if(options.scrollbarVisable){
				$(this).find('.J-tinyscrollbar-viewport').css('position', 'absolute');
			}
		}
		$(this).find('.J-tinyscrollbar-viewport').width($(this).width() - w);
		
		 this.each(function() {
            if(!$.data(this, "plugin_" + pluginName)) {
				oPlugin = new Plugin($(this), options);
				if(typeof(options.scrollEndEventFunc) != 'undefined'){
					oPlugin.scrollEndEventFunc = options.scrollEndEventFunc;
				}
				if(typeof(options.scrollStartEventFunc) != 'undefined'){
					oPlugin.scrollStartEventFunc = options.scrollStartEventFunc;
				}
                $.data(this, "plugin_" + pluginName, oPlugin);
            }
        });
		return oPlugin;
    };
}));
//ScrollViewer
(function(container, $){
	container.ScrollViewer = function(aOptions){
		this.oScrollViewerDom = '';
		this.oScrollViewerContainer = '';
		this.oScrollBar = '';
		this.scrollWidth = '';
		this.aDataList = '';
		
		this.init = function(aConfig){
			if(typeof(aConfig.templeFile) != 'undefined'){
				if(aConfig.templeFile instanceof Array){
					for(var j in aConfig.templeFile){
						$.extend(this, aConfig.templeFile[j]);
					}
				}else{
					$.extend(this, aConfig.templeFile);
				}
			}
			if(typeof(aConfig.scrollWidth) != 'undefined'){
				this.scrollWidth = aConfig.scrollWidth;
			}
			if(typeof(aConfig.oDom) != 'undefined'){
				this.oScrollViewerDom = aConfig.oDom;
			}
			if(typeof(aConfig.aDataList) != 'undefined'){
				this.aDataList = aConfig.aDataList;
			}
			if(typeof(this.buildHtml) != 'undefined'){
				var aData = [];
				if(typeof(aConfig.aDataList) != 'undefined'){
					aData = aConfig.aDataList;
				}
				this.oScrollViewerDom.append(this.buildHtml(aData));
			}
			this.oScrollViewerContainer = this.oScrollViewerDom.find('.J-scroll-container');
			if(typeof(this.before) != 'undefined'){
				this.before();
			}
			if(typeof(this.setScrollbar) != 'undefined'){
				this.setScrollbar();
			}
			if(typeof(this.after) != 'undefined'){
				this.after();
			}
		};
	}
})(window, jQuery);
//ListViewer
(function(container, $){
	container.ListViewer = function(aOptions){
		this.oContainerDom = '';
		this.aDataList = '';
		
		this.init = function(aConfig){
			if(typeof(aConfig.templeFile) != 'undefined'){
				if(aConfig.templeFile instanceof Array){
					for(var j in aConfig.templeFile){
						$.extend(this, aConfig.templeFile[j]);
					}
				}else{
					$.extend(this, aConfig.templeFile);
				}
			}
			if(typeof(aConfig.oDom) != 'undefined'){
				this.oContainerDom = aConfig.oDom;
			}
			if(typeof(aConfig.listUrl) != 'undefined'){
				this.listUrl = aConfig.listUrl;
			}
			if(typeof(aConfig.aDataList) != 'undefined'){
				this.aDataList = aConfig.aDataList;
			}
			if(typeof(this.buildHtml) != 'undefined'){
				var aData = [];
				if(typeof(aConfig.aDataList) != 'undefined'){
					aData = aConfig.aDataList;
				}
				this.oContainerDom.append(this.buildHtml(aData));
			}
			if(typeof(this.before) != 'undefined'){
				this.before();
			}
			if(typeof(this.after) != 'undefined'){
				this.after();
			}
		};
	}
})(window, jQuery);