<?php

namespace appxq\sdii\widgets;

use Yii;
use yii\helpers\Html;
use yii\bootstrap\Widget as BaseWidget;

/**
 * MapInput class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @package appxq\sdii\widgets
 * @version 2.0.0 Date: Sep 5, 2015 3:18:34 PM
 * @example 
 */
class MapInput extends BaseWidget {

    public $key = '';
    public $sensor = 'false';
    public $lat='';
    public $lng='';
    public $latValue='';
    public $lngValue='';
    
    public function init() {
	parent::init();
	
	$this->clientOptions['id'] = $this->options['id'].'-map';
	$this->clientOptions['search'] = $this->options['id'].'-search';
	$this->clientOptions['lat'] = $this->options['id'].'-lat';
	$this->clientOptions['lng'] = $this->options['id'].'-lng';
	$this->clientOptions['btn-search'] = $this->options['id'].'-btn-search';
	$this->clientOptions['btn-auto'] = $this->options['id'].'-btn-auto';
	$this->clientOptions['lat-attr'] = $this->lat;
	$this->clientOptions['lng-attr'] = $this->lng;
	$this->clientOptions['lat-value'] = isset($this->latValue)?$this->latValue:'';
	$this->clientOptions['lng-value'] = isset($this->lngValue)?$this->lngValue:'';
	//$this->options['style'] = 'width: 100%; height: 350px;';
	
	echo Html::beginTag('div', $this->options) . "\n";
    }

    /**
     * Renders the widget.
     */
    public function run() {
        
        
	echo Html::beginTag('div', ['class'=>'row']) . "\n";
	echo Html::beginTag('div', ['class'=>'col-sm-9']) . "\n";
	
	echo Html::beginTag('div', ['id'=>$this->clientOptions['id'], 'style'=>'width: 100%; height: 350px;']) . "\n";
	
	echo "\n" . Html::endTag('div');
	
	echo "\n" . Html::endTag('div');
	echo Html::beginTag('div', ['class'=>'col-sm-3 sdbox-col']) . "\n";
	
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::label('ค้นหา');
	echo Html::textInput($this->clientOptions['search'], '', ['class'=>'form-control', 'id'=>$this->clientOptions['search']]);
	echo "\n" . Html::endTag('div');
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::button('ค้นหา', ['class'=>'btn btn-block btn-success', 'id'=>$this->clientOptions['btn-search']]);
	echo "\n" . Html::endTag('div');
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::label('พิกัด');
	echo Html::textInput($this->clientOptions['lat'], '', ['class'=>'form-control', 'id'=>$this->clientOptions['lat']]);
        echo isset($this->options['annotated_lat'])?$this->options['annotated_lat']:'';
	echo "\n" . Html::endTag('div');
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::textInput($this->clientOptions['lng'], '', ['class'=>'form-control', 'id'=>$this->clientOptions['lng']]);
        echo isset($this->options['annotated_lng'])?$this->options['annotated_lng']:'';
	echo "\n" . Html::endTag('div');
	
	echo Html::beginTag('div', ['class'=>'form-group']) . "\n";
	echo Html::button('ใช้ตำแหน่งปัจจุบัน', ['class'=>'btn btn-block btn-success', 'id'=>$this->clientOptions['btn-auto']]);
	echo "\n" . Html::endTag('div');
	
	echo "\n" . Html::endTag('div');
	echo "\n" . Html::endTag('div');
	echo "\n" . Html::endTag('div');
	
	
	
	$view = $this->getView();
	
//	$op['sensor'] = $this->sensor;
//	if($this->key!=''){
//	    $op['key'] = $this->key;
//	}
//	$op['language'] = 'th';
//	
//	$q = array_filter($op);
//
//        $view->registerJsFile('https://maps.google.com/maps/api/js?'.http_build_query($q), [
//	    'position'=>\yii\web\View::POS_HEAD,
//	    'depends'=>'yii\web\YiiAsset',
//	]);
	
	$option = \yii\helpers\Json::encode($this->clientOptions);
	
	//\appxq\sdii\assets\map\MapAsset::register($view);
	
	$view->registerJs("
            var searchPlace;
            var autoPlace;
                
	    function initialize(options) {
                var initialLocation;
                var marker;
                var latlng = false;
		var geocoder = new google.maps.Geocoder();
		
		var mapOptions = {
                    zoom: 13,// ค่าซูมยิ่งมากยิ่งใกล้
                    mapTypeControl: true,
                    navigationControlOptions: {style: google.maps.NavigationControlStyle.SMALL},
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
		var map = new google.maps.Map(document.getElementById(options['id']), mapOptions);
		
		setLatlng();
		
		// ตรวจว่ารองรับ geolocation หรือไม่
                if ( navigator.geolocation && !latlng) {
                    navigator.geolocation.getCurrentPosition(function(location) {
                        var location = location.coords;
			$('#'+options['lat']).val(location.latitude);
			$('#'+options['lng']).val(location.longitude);
			
			$('#'+options['lat']).trigger('change');
			$('#'+options['lng']).trigger('change');
                        initialLocation = new google.maps.LatLng(location.latitude, location.longitude);// ระบุตำแหน่งแสดงแผนที่ให้ google map
                        map.setCenter(initialLocation);// ให้ตำแหน่งแสดงแผนที่อยู่ตรงกลางพอดี
                        setMarker(initialLocation);// เรียกฟังก์ชั่นแสดง marker ในตำแหน่ง lat/lng ที่ตรวจได้
                    }, function() {
                        handleNoGeolocation();// ตรวจตำแหน่ง lat/lng ไม่ได้ ให้ใช้ค่าเริ่มต้น
                    });
                } else {
                    handleNoGeolocation();// ตรวจตำแหน่ง lat/lng ไม่ได้ ให้ใช้ค่าเริ่มต้น
                }
		
		// no geolocation ฟังก์ชั่นนี้จะถูกเรียกใช้งานเมื่อตรวจค่า lat/lng ไม่ได้
                function handleNoGeolocation() {
		    if(!latlng){
			latlng = new google.maps.LatLng(16.4419355, 102.8359921);
		    }
		    $('#'+options['lat']).val(latlng.lat());
		    $('#'+options['lng']).val(latlng.lng());
                    
                    $('#'+options['lat']).trigger('change');
                    $('#'+options['lng']).trigger('change');
                    map.setCenter(latlng);
                    setMarker(latlng);
                }
		
		function setLatlng(){
		    if(options['lat-value']!='' && options['lng-value']!=''){
			latlng = new google.maps.LatLng(options['lat-value'], options['lng-value']);
		    }
		}
		
		// set marker
                function setMarker(initialName) {
                    marker = new google.maps.Marker({
                        draggable: true,
                        position: initialName,
                        map: map,
			animation: google.maps.Animation.DROP,
                        title: 'คุณอยู่ที่นี่'
                    });
                    // ด้านล่างนี้คือกำหนดให้สามารถลากตำแหน่ง marker ได้
                    google.maps.event.addListener(marker, 'dragend', function(event) {
			var my_Point = marker.getPosition();
			map.panTo(my_Point);
			$('#'+options['lat']).val(my_Point.lat());
			$('#'+options['lng']).val(my_Point.lng());
			
			$('#'+options['lat']).trigger('change');
			$('#'+options['lng']).trigger('change');
                    });
                }
		
		map.addListener('click', function(event) {
		    marker.setMap(map);
		    marker.setPosition(event.latLng);
		    $('#'+options['lat']).val(event.latLng.lat());
		    $('#'+options['lng']).val(event.latLng.lng());   
		    
		    $('#'+options['lat']).trigger('change');
		    $('#'+options['lng']).trigger('change');
		});

		// ส่วนของฟังก์ชันค้นหาชื่อสถานที่ในแผนที่
		searchPlace = function() {
		    var AddressSearch=$('#'+options['search']).val();
		    if(geocoder){ // ตรวจสอบว่าถ้ามี Geocoder Object   
			geocoder.geocode({  
			     address: AddressSearch // ให้ส่งชื่อสถานที่ไปค้นหา  
			},function(results, status){ // ส่งกลับการค้นหาเป็นผลลัพธ์ และสถานะ  
			    if(status == google.maps.GeocoderStatus.OK) { // ตรวจสอบสถานะ ถ้าหากเจอ  
				var my_Point=results[0].geometry.location; // เอาผลลัพธ์ของพิกัด มาเก็บไว้ที่ตัวแปร  
				map.setCenter(my_Point); // กำหนดจุดกลางของแผนที่ไปที่ พิกัดผลลัพธ์  
				marker.setMap(map); // กำหนดตัว marker ให้ใช้กับแผนที่ชื่อ map                     
				marker.setPosition(my_Point); // กำหนดตำแหน่งของตัว marker เท่ากับ พิกัดผลลัพธ์  
				$('#'+options['lat']).val(my_Point.lat());
				$('#'+options['lng']).val(my_Point.lng());   
				
				$('#'+options['lat']).trigger('change');
				$('#'+options['lng']).trigger('change');
			    }else{  
				// ค้นหาไม่พบแสดงข้อความแจ้ง  
				alert('Geocode was not successful for the following reason: ' + status);  
				$('#'+options['search']).val('');
			     }  
			});  
		    }        
		}
		
		autoPlace = function() {
		    if ( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition(function(location) {
			    var location = location.coords;
			    $('#'+options['lat']).val(location.latitude);
			    $('#'+options['lng']).val(location.longitude);
			    
			    $('#'+options['lat']).trigger('change');
			    $('#'+options['lng']).trigger('change');
			    var myLocation = new google.maps.LatLng(location.latitude, location.longitude);// ระบุตำแหน่งแสดงแผนที่ให้ google map
			    map.setCenter(myLocation);// ให้ตำแหน่งแสดงแผนที่อยู่ตรงกลางพอดี
			    marker.setPosition(myLocation);
			}, function() {
			    alert('navigator geolocation was not successful.');  
			});
		    } else {
			alert('navigator geolocation not support.');  
		    }
		}
	    }
	", \yii\web\View::POS_END);
	
	$view->registerJs("
	    initialize($option);
	
	    $('#{$this->clientOptions['btn-search']}').click(function(){ // เมื่อคลิกที่ปุ่ม id=SearchPlace ให้ทำงานฟังก์ฃันค้นหาสถานที่  
		searchPlace();  // ฟังก์ฃันค้นหาสถานที่  
	    }); 
	    
	    $('#{$this->clientOptions['btn-auto']}').click(function(){
		autoPlace();  // ฟังก์ฃันค้นหาสถานที่  
	    }); 
	    
	    $('#{$this->clientOptions['lat']}').change(function(){
		var lat = '{$this->clientOptions['lat-attr']}';
		if(lat!=''){
		    $('#'+lat).val($(this).val());
		}    
	    }); 
	    
	    $('#{$this->clientOptions['lng']}').change(function(){
		var lng = '{$this->clientOptions['lng-attr']}';
		if(lng!=''){
		    $('#'+lng).val($(this).val());
		}
	    }); 
	    
	    $('#{$this->clientOptions['search']}').keyup(function(event){ // เมื่อพิมพ์คำค้นหาในกล่องค้นหา  
		if(event.keyCode==13){  //  ตรวจสอบปุ่มถ้ากด ถ้าเป็นปุ่ม Enter ให้เรียกฟังก์ชันค้นหาสถานที่  
		    searchPlace();      // ฟังก์ฃันค้นหาสถานที่  
		}         
	    });  
	", \yii\web\View::POS_READY);
    }

}
