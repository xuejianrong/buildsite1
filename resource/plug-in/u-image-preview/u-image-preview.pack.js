window.UImagePreview = {
    /**
     *
     * @param index 当前图片
     * @param array 图片数组
     *
     */
    show: function (index, array) {
        //使用微信的图片浏览
        if(window.wx){
            var imgArray = [];
            $(array).each(function (i) {
                imgArray.push(Ui.buildImageUrl(array[i]['path']));
            });
            wx.previewImage({
                current: imgArray[index], // 当前显示图片的http链接
                urls: imgArray // 需要预览的图片http链接列表
            });
            return false;
        }



        var template = window.pageTemplate.uImagePreview,
            $root = $(template),
            $list = $root.find('.J-alert-list');

        var screenWidth = window.screen.width,startPageX, marginLeft = 0,length = array.length,
            _touchStart = function(event){
                startPageX = event['targetTouches'][0].pageX;
                $list.removeClass('move');
            },
            _touchMove = function(event){
                var pageX = event['targetTouches'][0].pageX;
                var left  = marginLeft + (pageX - startPageX);
                $list.css('transform','translate3d('+left+'px,0,0)');
            },

            _touchEnd = function(event){
                var  endPageX = event['changedTouches'][0].pageX,
                    distance = endPageX - startPageX,
                    page;
                if(distance < 0){    //left
                    page = index === (length-1) ? index : index + 1;
                }else if(0 < distance){
                    page = index === 0 ? index : index - 1;
                }
                _setTitle(page);
            },

            _setTitle = function(page){
                var title = array[page]['name'];
                $root.find('.J-title').text(title || '');
                $root.find('.J-page').text((page+1) + '/' + array.length);
                marginLeft = screenWidth * page * -1;
                $list.addClass('move');
                $list.css('transform','translate3d('+marginLeft+'px,0,0)');
                index = page;
            };

        $root.find('.J-alert-item').css('width', screenWidth);
        $list.css('width', screenWidth * array.length)
            .on('touchstart', _touchStart)
            .on('touchmove', _touchMove)
            .on('touchend', _touchEnd);

        PageBase.cloneItem($root.find('.J-alert-item'), length, true).each(function(index){
            var url = array[index]['path'];
            $(this).append(Ui.buildImage(Ui.buildImageUrl(url)));
        });

        _setTitle(index);
        $root.show();

        //关闭
        $root.find('.J-close').click(function(){
            $root.remove();
        });

        $root.appendTo('#wrapPage');
    }
};