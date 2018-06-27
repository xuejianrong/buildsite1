<?php
use umeworld\lib\Url;

$siteTitle = '联系我们';
$this->setTitle($siteTitle);

?>
<script type="text/javascript" src="<?php echo Yii::$app->siteSetting->aContactusSetting['mapApi']; ?>"></script>
<div class="banner_about">
     <div class="job_nav">
        <ul>
        <li class="newshover"><a href="<?php echo Url::to(Yii::$app->id, 'site/contactus'); ?>" style="color:#005fd4;">联系我们</a></li>
        <li><a href="<?php echo Url::to(Yii::$app->id, 'site-message/index'); ?>">在线留言</a></li>
        </ul>
        </div>
</div>
<div class="about">

 <div class="map">
 <div style="width:900px;height:550px;border:#ccc solid 1px;font-size:12px" id="map"></div>
 </div>
  
       <ul class="contactlist"> 
       <li>服务热线：<?php echo Yii::$app->siteSetting->aContactusSetting['hotsPhone']; ?></li>
       <li>电话：<?php echo Yii::$app->siteSetting->aContactusSetting['phone']; ?> </li>
       <li>地址：<?php echo Yii::$app->siteSetting->aContactusSetting['address']; ?></li>
       <li>售后服务电话：<?php echo Yii::$app->siteSetting->aContactusSetting['servicePhone']; ?></li>
       <li>服务邮箱：<?php echo Yii::$app->siteSetting->aContactusSetting['email']; ?></li>
       </ul>
   <div class="clear"></div>
</div>
<script type="text/javascript">
    //创建和初始化地图函数：
    function initMap(){
      createMap();//创建地图
      setMapEvent();//设置地图事件
      addMapControl();//向地图添加控件
      addMapOverlay();//向地图添加覆盖物
    }
    function createMap(){ 
      map = new BMap.Map("map"); 
      map.centerAndZoom(new BMap.Point(<?php echo Yii::$app->siteSetting->aContactusSetting['lng']; ?>,<?php echo Yii::$app->siteSetting->aContactusSetting['lat']; ?>),14);
    }
    function setMapEvent(){
      map.enableScrollWheelZoom();
      map.enableKeyboard();
      map.enableDragging();
      map.enableDoubleClickZoom()
    }
    function addClickHandler(target,window){
      target.addEventListener("click",function(){
        target.openInfoWindow(window);
      });
    }
    function addMapOverlay(){
      var markers = [
        {content:"     电话：<?php echo Yii::$app->siteSetting->aContactusSetting['phone']; ?>     地址：<?php echo Yii::$app->siteSetting->aContactusSetting['address']; ?>     售后服务电话：<?php echo Yii::$app->siteSetting->aContactusSetting['servicePhone']; ?>     服务邮箱：<?php echo Yii::$app->siteSetting->aContactusSetting['email']; ?>",title:"<?php echo Yii::$app->siteSetting->aContactusSetting['companyName']; ?>",imageOffset: {width:0,height:3},position:{lat:<?php echo Yii::$app->siteSetting->aContactusSetting['lat']; ?>,lng:<?php echo Yii::$app->siteSetting->aContactusSetting['lng']; ?>}}
      ];
      for(var index = 0; index < markers.length; index++ ){
        var point = new BMap.Point(markers[index].position.lng,markers[index].position.lat);
        var marker = new BMap.Marker(point,{icon:new BMap.Icon("http://api.map.baidu.com/lbsapi/createmap/images/icon.png",new BMap.Size(20,25),{
          imageOffset: new BMap.Size(markers[index].imageOffset.width,markers[index].imageOffset.height)
        })});
        var label = new BMap.Label(markers[index].title,{offset: new BMap.Size(25,5)});
        var opts = {
          width: 200,
          title: markers[index].title,
          enableMessage: false
        };
        var infoWindow = new BMap.InfoWindow(markers[index].content,opts);
        marker.setLabel(label);
        addClickHandler(marker,infoWindow);
        map.addOverlay(marker);
      };
    }
    //向地图添加控件
    function addMapControl(){
      var scaleControl = new BMap.ScaleControl({anchor:BMAP_ANCHOR_BOTTOM_LEFT});
      scaleControl.setUnit(BMAP_UNIT_IMPERIAL);
      map.addControl(scaleControl);
      var navControl = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
      map.addControl(navControl);
      var overviewControl = new BMap.OverviewMapControl({anchor:BMAP_ANCHOR_BOTTOM_RIGHT,isOpen:true});
      map.addControl(overviewControl);
    }
    var map;
      initMap();
  </script>