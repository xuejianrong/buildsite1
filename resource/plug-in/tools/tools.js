(function(win){
    var Tools = {
		regexEmail: '^([a-zA-Z0-9]+[_|\\_|\\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\\_|\\.]?)*[a-zA-Z0-9]+\\.[a-zA-Z]{2,3}$',
		regexMobile: '^((\\+86)|(86))?1[3|4|5|7|8]{1}\\d{9}$',
    	/**
    	 * 是否是邮箱格式
    	 * @param  {String}  val [检测值]
    	 * @return {Boolean}       [结果]
    	 */
    	isEmail: function(val){
    		return new RegExp(this.regexEmail).test(val);
    	},

    	/**
    	 * 是否范围内
    	 * @param  {Number}  val [检测值]
    	 * @param  {Number}  min [最小]
    	 * @param  {Number}  max [最大]
    	 * @return {Boolean}     [结果]
    	 */
    	isRange: function(val, min, max){
    		if(min == undefined){min = -Infinity}
    		if(max == undefined){max = Infinity}
    		return min <= val && val <= max;
    	},

    	/**
    	 * 是否是手机号
    	 * @param  {String}  val [检测值]
    	 * @return {Boolean}     [结果]
    	 */
    	isMobile: function(val){
    		return new RegExp(this.regexMobile).test(val);
    	},

    	isNumber: function(val, len){
    		return new RegExp('^\\d{' + len + '}$').test(val);
    	},

    	/**
    	 * 是否链接
    	 * @param  {String}  val [检测值]
    	 * @return {Boolean}     [结果]
    	 */
    	isUrl: function(val){
    		return new RegExp('(\\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])', 'ig').test(val);
    	},
		
        /**
         * 转换日期
         */
        date: function (format, timestamp, isLocalTime) {
            if (isLocalTime == undefined) {
                isLocalTime = true;
            }

            if (!isLocalTime) {
                var oLocalDate = new Date();
                timestamp = timestamp - Math.abs(oLocalDate.getTimezoneOffset()) * 60;
            }

            var jsdate = new Date(timestamp * 1000);


            //补零
            var pad = function (data, len) {
                if ((data += '').length < len) {
                    //计算要补多少个零
                    len = len - data.length;
                    var str = '0000';
                    return data = str.substr(0, len) + data;
                } else {
                    return data;
                }
            };
            //var weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var weekdays = ['日', '一', '二', '三', '四', '五', '六'];

            //计算一年中的第几天
            var inYearDay = function () {
                var aDay = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
                var day = jsdate.getDate();
                var month = jsdate.getMonth();
                var year = jsdate.getFullYear();
                var $reDay = 0;
                for (var i = 0; i < month; i++) {
                    $reDay += aDay[i];
                }
                $reDay += day;
                //计算闰年
                if (month > 1 && (year % 4 == 0 && year % 100 != 0) || year % 400 == 0) {
                    $reDay += 1;
                }
                return $reDay;
            };

            var fm = {
                //天
                j: function () {
                    return jsdate.getDate();
                },
                d: function () {
                    return pad(fm.j(), 2);
                },
                w: function () {
                    return jsdate.getDay();
                },
                l: function () {
                    return '周' + weekdays[fm.w()];
                },
                D: function () {
                    return fm.l().substr(0, 3);
                },
                N: function () {
                    return fm.w() + 1;
                },
                z: function () {
                    return inYearDay();
                },

                //月
                n: function () {
                    return jsdate.getMonth() + 1;
                },
                m: function () {
                    return pad(fm.n(), 2);
                },
                t: function () {
                    var n;
                    if ((n = jsdate.getMonth() + 1) == 2) {
                        return 28 + fm.L();
                    } else {
                        if (n & 1 && n < 8 || !(n & 1) && n > 7) {
                            return 31;
                        } else {
                            return 30;
                        }
                    }
                },

                //年
                Y: function () {
                    return jsdate.getFullYear();
                },
                y: function () {
                    return (jsdate.getFullYear() + "").slice(2);
                },
                L: function () {
                    var y = fm.Y();
                    return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0;
                },

                //秒
                s: function () {
                    return pad(jsdate.getSeconds(), 2);
                },

                //分
                i: function () {
                    return pad(jsdate.getMinutes(), 2);
                },

                //时
                H: function () {
                    return pad(jsdate.getHours(), 2);
                },
                g: function () {
                    return jsdate.getHours() % 12 || 12;
                },

                //am或pm
                a: function () {
                    return jsdate.getHours() > 11 ? 'pm' : 'am';
                },

                //AM或PM
                A: function () {
                    return fm.a().toUpperCase();
                },

                //周
                W: function () {
                    var a = fm.z(), b = 364 + fm.L() - a;
                    var nd2, nd = (new Date(jsdate.getFullYear() + '/1/1').getDay() || 7) - 1;
                    if (b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b) {
                        return 1;
                    } else {
                        if (a <= 2 && nd >= 4 && a >= (6 - nd)) {
                            nd2 = new Date(jsdate.getFullYear() - 1 + '/12/31');
                            return self.date("W", Math.round(nd2.getTime() / 1000));
                        } else {
                            return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                        }
                    }
                }

            };

            //分析format
            return format.replace(/[\\]?([a-zA-Z])/g, function (rekey1, rekey2) {
                var result = '';
                if (rekey1 != rekey2) {
                    result = rekey2;
                } else if (fm[rekey2]) {
                    result = fm[rekey2]();
                } else {
                    result = rekey2;
                }
                return result;
            });
        },
		
		/**
         * 解释语言
         */
        words : function(keyName, aReplacement){
			if(typeof(aLanguageWordsDataList[keyName]) == 'undefined'){
				return '';
			}
			var returnString = aLanguageWordsDataList[keyName];
			if(aReplacement){
				for(var i in aReplacement){
					returnString = returnString.replace('{' + i + '}', aReplacement[i]);
				}
			}
			return returnString;
        },
		
		getLanguage : function(){
			return __current__site_language__;
		},
		
		/**
         * 生成url
         */
        url : function(appName, key, aParams){
			if(!aParams){
				aParams = {};
			}
			var aUrlConfigList = aUrlMapConfigList[appName];
			for(var i in aUrlConfigList.rules){
				if(aUrlConfigList.rules[i] == key){
					var url = i;
					var aMatch = this._getMatchTagList(url);
					if(this._compare(aMatch, aParams)){
						for(var t in aMatch){
							url = url.replace(aMatch[t], aParams[t]);
						}
						return aUrlConfigList.baseUrl + '/' + url;
					}
				}
			}
			
            return aUrlConfigList.baseUrl;
        },
		
		_getMatchTagList : function(str){
			var aMatch = {};
			var tag = '';
			var param = '';
			var tagFlag = false;
			var paramFlag = false;
			for(var j in str){
				if(str[j] == ':'){
					paramFlag = false;
				}
				if(str[j] == '>'){
					tagFlag = false;
					paramFlag = false;
					aMatch[param] = tag + str[j];
					param = '';
					tag = '';
				}
				if(tagFlag){
					tag += str[j]; 
					if(paramFlag){
						param += str[j]; 
					}
				}
				if(str[j] == '<'){
					tagFlag = true;
					paramFlag = true;
					tag += str[j];
				}
			}
			return aMatch;
		},
		
		_compare : function(aParams, aMatch){
			for(var p in aParams){
				if(typeof(aMatch[p]) == 'undefined'){
					return false;
				}
			}
			for(var q in aMatch){
				if(typeof(aParams[q]) == 'undefined'){
					return false;
				}
			}
			return true;
		},
		
		/**
		 * 计算中文个数,计算时, 汉字或全角字符占1个长度, 英文字符占0.5个长度
		 */
		chineseWordsCount : function(str, shortUrl){
			str = str + '';
			if (true == shortUrl) {
				// 一个URL当作十个字长度计算
				return Math.ceil(str.replace(/((news|telnet|nttp|file|http|ftp|https):\/\/){1}(([-A-Za-z0-9]+(\.[-A-Za-z0-9]+)*(\.[-A-Za-z]{2,5}))|([0-9]{1,3}(\.[0-9]{1,3}){3}))(:[0-9]*)?(\/[-A-Za-z0-9_\$\.\+\!\*\(\),;:@&=\?\/~\#\%]*)*/ig, 'xxxxxxxxxxxxxxxxxxxx').replace(/^\s+|\s+$/ig, '').replace(/[^\x00-\xff]/ig, 'xx').length / 2);
			}else{
				return Math.ceil(str.replace(/^\s+|\s+$/ig, '').replace(/[^\x00-\xff]/ig, 'xx').length / 2);
			}
		},
		
		/**
		 * 检测一个变量是否对象
		 */
		isObject : function(variable){
			return typeof(variable) == 'object' && Object.prototype.toString.call(variable).toLowerCase() == '[object object]' && !variable.length;
		},

		/**
		 * 合并两个数组或对象
		 */
		merge : function(obj1, obj2) {
			var oTmpObj1 = this.clone(obj1);
			if($.isArray(obj1) && $.isArray(obj2)){
				for(var i in obj2){
					if($.inArray(obj2[i], oTmpObj1) === -1){
						oTmpObj1.push(obj2[i]);
					}
				}
			}else if($.isPlainObject(obj1) && $.isPlainObject(obj2)){
				for(var key in obj2) {
					if(oTmpObj1.hasOwnProperty(key) || obj2.hasOwnProperty(key)){
						oTmpObj1[key] = obj2[key];
					}
				}
			}
			return this.clone(oTmpObj1);
		},

		/**
		 * 判断一个元素是否在数组内
		 * @param {type} value 元素值
		 * @param {type} array 数组
		 * @param {type} isStrict 是否严格模式
		 * @returns {Boolean}
		 */
		inArray : function(value, array, isStrict){
			for(var i in array){
				if(array[i] == value && !isStrict){
					return true;
				}else if(array[i] === value && isStrict){
					return true;
				}
			}
			return false;
		},

		/**
		 * 键值对转换成数组
		 */
		toArray : function(object){
			var key, item, arr = [];
			for(key in object){
				if(object.hasOwnProperty(key)){
					item = object[key];
					item['key'] = key;
					arr.push(item);
				}
			}
			return arr;
		},

		/**
		 * 计算数组最大最小值差距
		 * @param  {[type]} array [description]
		 * @return {[type]}       [description]
		 */
		calArray : function(array){
			return Math.max.apply(null, array) - Math.min.apply(null,array);
		},

		/**
		 * 按下标删除数组记录
		 */
		removeArrayItemIndex : function(aArray, removeIndex){
			if(isNaN(removeIndex) || removeIndex > aArray.length){
				return false;
			}

			for(var i = 0, n = 0; i < aArray.length; i++){
				if(aArray[i] != aArray[removeIndex]){
					aArray[n++] = aArray[i];
				}
			}
			aArray.length--;
			return aArray;
		},

		/**
		 * 克隆一个数组或对象
		 */
		clone : function(oObj){
			//数组或对象克隆
			if(oObj == null || typeof(oObj) !== 'object'){
				return oObj;
			}

			try{
				var oTempObj = new oObj.constructor();
			}catch(e){
				$.error('对象克隆失败,请确认对象中没有jQuery的fn对象');
				return oObj;
			}

			for(var key in oObj){
				oTempObj[key] = arguments.callee(oObj[key]);
			}
			return oTempObj;
		},

		/**
		 * 获取缓存
		 */
		getCache : function(key){
			if(localStorage && localStorage.getItem){
				return localStorage.getItem(key);
			}
		},

		/**
		 * 保存缓存
		 */
		setCache : function(key, value){
			if(localStorage && localStorage.setItem){
				return localStorage.setItem(key, value);
			}
		},
		
		showLoading : function(){
			var oHtml = $('<div class="J-loading-maskingx" style="display: block; z-index: 999998;opacity: 1;position: fixed; right: 0; bottom: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,.6);"><div style="width: 180px; height: 40px; line-height: 40px; color: #0e90d2; font-size: 1.2rem; background: #fff; border-radius: 4px; text-align: center; margin: 0 auto;"><i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;正在处理中，请稍候...</div></div>');
			$('body').append(oHtml);
			oHtml.find('div').css({'margin-top' : $(document).height() / 2 - 20});
		},
		
		hideLoading : function(){
			$('.J-loading-maskingx').remove();
		},
		
		bindSelectAll : function(oSelectAllDom, aItemSelectDom){
			oSelectAllDom.change(function(){
				if($(this).is(":checked")){
					aItemSelectDom.each(function(){
						if(!$(this).is(":checked")){
							$(this).click();
						}
					});
				}else{
					aItemSelectDom.each(function(){
						if($(this).is(":checked")){
							$(this).click();
						}
					});
				}
			});
		}
    };

    win.Tools = $.extend(win.Tools, Tools);
})(window);
