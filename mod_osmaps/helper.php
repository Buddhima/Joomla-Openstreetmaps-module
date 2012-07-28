<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

class OsModMapsHelper{

public static function main($params,$id){

$result='';

$result.='<html>
		<head>
			<script src="http://openlayers.org/api/2/OpenLayers.js"></script>
		</head>
		<body>
			<div style="width:100%; height:250px" id="map'.$id.'"></div>
			<script  type="text/javascript">
				var map'.$id.' = new OpenLayers.Map(\'map'.$id.'\');';

// Setting map-layer
		
if( $params->get('layer')=='osm') // should be changed to 'osm'
{
	$result.='map'.$id.'.addLayer(new OpenLayers.Layer.OSM());';
}else if($params->get('layer')=='wms')
{
	$result.='map'.$id.'.addLayer(new OpenLayers.Layer.WMS("OpenLayers WMS","http://labs.metacarta.com/wms/vmap0", {layers: \'basic\'}));';
}



// using lon-lat based center
if($params->get('centerType') == '0'){
	$center_lon=$params->get('centerLon');
	$center_lat=$params->get('centerLat');
	
}
// using geo location based center
else if ($params->get('centerType') == '1'){

	$xml_center=simplexml_load_file("http://nominatim.openstreetmap.org/search?q=".$params->get('centerGeo')."&format=xml"); // + seperated keywords necessary

	$array=$xml_center->xpath('place');

	$center_lon=$array[0]['lon'];
	$center_lat=$array[0]['lat'];
	
}

$result .= 'var center'.$id.'= new OpenLayers.LonLat('.$center_lon.', '.$center_lat.') .transform(
            new OpenLayers.Projection("EPSG:4326"), 
            map'.$id.'.getProjectionObject() 
          	); 
			map'.$id.'.setCenter(center'.$id.', '.$params->get('zoom').' );';


$result .= 'var markers'.$id.' = new OpenLayers.Layer.Markers("Markers");';



//var_dump($params);


$result .= 'var size'.$id.' = new OpenLayers.Size(20,30);
			var offset'.$id.' = new OpenLayers.Pixel(-(size'.$id.'.w/2), -size'.$id.'.h);
			var icon'.$id.' = new OpenLayers.Icon(\'http://i50.tinypic.com/34qttaa.png\', size'.$id.', offset'.$id.');
			';

// using lon-lat based - marker
if($params->get('pinType') == '0'){
	$pin_lon=$params->get('pinLon');
	$pin_lat=$params->get('pinLat');
	$pop_txt=$params->get('popuptext');
}
// using geo location based - marker
else if ($params->get('pinType') == '1'){
	
	$xml=simplexml_load_file("http://nominatim.openstreetmap.org/search?q=".$params->get('pinGeo')."&format=xml"); // + seperated keywords necessary
		
	$array=$xml->xpath('place');
	
	$pin_lon=$array[0]['lon'];
	$pin_lat=$array[0]['lat'];
	
	if($params->get('popuptext')== '') 
	{
		$pop_txt=$array[0]['display_name'];		
	}
	else
	{
		$pop_txt=$params->get('popuptext');
	}
	
}

if($pin_lon != '' && $pin_lat != '' && $pop_txt !=''){
$result .=	'feature'.$id.' = new OpenLayers.Feature(markers'.$id.', 
			new OpenLayers.LonLat('.$pin_lon.', '.$pin_lat.').transform(
			new OpenLayers.Projection("EPSG:4326"),
			map'.$id.'.getProjectionObject()));
			
	feature'.$id.'.closeBox = true;
	feature'.$id.'.popupClass =  OpenLayers.Class(OpenLayers.Popup.FramedCloud, {
		\'autoSize\': true,
		\'maxSize\': new OpenLayers.Size(200,150)
	});
	feature'.$id.'.data.popupContentHTML = "'.$pop_txt.'";
	feature'.$id.'.data.overflow = "auto";';
	
if ($params->get('pin')==1){
	$result .='feature'.$id.'.data.icon=icon'.$id.';';
}	
	 
	$result .= 'marker'.$id.' = feature'.$id.'.createMarker();
	
	markerClick'.$id.' = function (evt) {
		if (this.popup'.$id.' == null) {
			this.popup'.$id.' = this.createPopup(this.closeBox'.$id.');
			map'.$id.'.addPopup(this.popup'.$id.');
			this.popup'.$id.'.show();
		} else {
			this.popup'.$id.'.toggle();
		}
		currentPopup'.$id.' = this.popup'.$id.';
		OpenLayers.Event.stop(evt);
	};
	marker'.$id.'.events.register("mousedown", feature'.$id.', markerClick'.$id.');
	marker'.$id.'.setOpacity(0.9);';


	$result .='markers'.$id.'.addMarker(marker'.$id.');';

	
}
else if($pin_lon != '' && $pin_lat != ''){
	$result .= 'var point'.$id.' = new OpenLayers.LonLat('.$pin_lon.', '.$pin_lat.') .transform(
	new OpenLayers.Projection("EPSG:4326"),
	map'.$id.'.getProjectionObject()
	);';
		
		$result .='var marker'.$id.' = new OpenLayers.Marker(point'.$id.');';
		
	 if ($params->get('pin')==1){
		$result .='var marker'.$id.' = new OpenLayers.Marker(point'.$id.',icon'.$id.');';
	}
	
	$result .= 'markers'.$id.'.addMarker(marker'.$id.');';
	
}

$result .= 'map'.$id.'.addLayer(markers'.$id.');';


// final part

$result.='	</script>
		</body>
	</html>';


return $result;
}

}


?>
