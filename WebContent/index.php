<?php

require '../aws/aws-autoloader.php';

use Aws\DynamoDb\DynamoDbClient;

$client = DynamoDbClient::factory(array(
    'version' => '2012-08-10',
    'profile' => 'default',
    'region'  => 'us-east-1'
));

$startKey = array();

$tweets = array();

do {
    $args = array('TableName' => 'Twitable') + $startKey;
    $result = $client->scan($args);
 
    foreach ($result['Items'] as $item) {
       $tweets[] = array($item['longitude']['N'], $item['latitude']['N'], $item['Text']['S']);
    }
 
    $startKey['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
} while ($startKey['ExclusiveStartKey']);
?>

<!DOCTYPE html>
<html>
  <head>
   <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
    <style>
      html, body, #map{
        margin: 0;
        padding: 0;
        height: 100%;
      }
      #map{
        top: 10%;
        height: 80%;
      }
      #header{
        position: absolute;
        height: 9.9%;
        width: 100%;
        background-color: whitesmoke;
        border-bottom: solid 1px white;
      }
      #header ul{
        list-style-type: none;
        padding: 0;
        margin: 0;
        font-family: 'Montserrat', sans-serif;
        font-size: 18px;
        color: #ccc;
        width: 100%;
        margin-left: 95px;
        margin-top: 28px;
      }
      #header ul li{
        display: inline-block;
        width: 19%;
        cursor: pointer;
      }
      #header ul li:last-child{
        color: grey;
      }
      #header ul li:hover{
        color: grey;
      }
      #footer{
        position: absolute;
        bottom: 0;
        height: 9.9%;
        width: 100%;
        max-height: 100px;
        background-color: whitesmoke;
        border-top: solid 1px white;
      }
      #footer div{
          display: block; 
          width: 230px;
          height: 60px;
          margin: auto;
          margin-top: 5px;
          background: url(../files/twitterapi_logo.png) no-repeat center center;
          background-size: contain;    
      }
      #footer div:before{
        content: "Powered By";
        color: grey;
        margin-left: 80px
      }

    </style>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>Twitmap</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="styles/styles.css" type="text/css" media="screen">

    <script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCuHdnABZbMmooZBpPNOGjnDN80dHLjjuw&libraries=geometry,visualization">
</script>
    <script>
      var map;

      function initialize(filter) {
        var mapOptions = {
          zoom: 3,
          center: new google.maps.LatLng(37.774546, -122.433523),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map'),
              mapOptions);

        var heatmapData = [];

        <?php foreach($tweets as $tweet){ ?>
          if(filter != null){
            if(new String(<?=json_encode($tweet[2])?>).indexOf(filter) > -1)
              heatmapData.push(new google.maps.LatLng(<?=$tweet[1]?>, <?=$tweet[0]?>));
          }
          else
            heatmapData.push(new google.maps.LatLng(<?=$tweet[1]?>, <?=$tweet[0]?>));
        <?php } ?>

        var blueGradient = [
          'rgba(0, 255, 255, 0)',
          'rgba(0, 255, 255, 1)',
          'rgba(0, 191, 255, 1)',
          'rgba(0, 127, 255, 1)',
          'rgba(0, 63, 255, 1)',
          'rgba(0, 0, 255, 1)',
          'rgba(0, 0, 223, 1)',
          'rgba(0, 0, 191, 1)',
          'rgba(0, 0, 159, 1)',
          'rgba(0, 0, 127, 1)',
          'rgba(63, 0, 91, 1)',
          'rgba(127, 0, 63, 1)',
          'rgba(191, 0, 31, 1)',
          'rgba(255, 0, 0, 1)'
        ]
        var heatmap = new google.maps.visualization.HeatmapLayer({
          data: heatmapData,
          dissipating: false,
          radius: 2,
          gradient: blueGradient
        });

        heatmap.setMap(map);
      }
        // Call the initialize function after the page has finished loading
        google.maps.event.addDomListener(window, 'load', function(){initialize(null);});

  </script>
  </head>
  <body>
    <div id="header">
      <ul>
          <li onclick="getTweets(this, 'sport')"> Sports </li>
          <li onclick="getTweets(this, 'weather')"> Weather </li>
          <li onclick="getTweets(this, 'music')"> Music </li>
          <li onclick="getTweets(this, 'movie')"> Movies </li>
          <li onclick="getTweets(this)"> All </li>
      </ul>
    </div>
    <div id="map"></div>
    <div id="footer"><div></div></div>
  </body>
  <script>
    function getTweets(elem, filter){
      items = document.getElementsByTagName("li");
      for (i = 0; i<items.length; i++){
        items[i].style.color = "#ccc";
      }
      initialize(filter);
      elem.style.color="grey";

    }
  </script>
</html>
