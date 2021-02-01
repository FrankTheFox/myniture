<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$title = 'Hier finden Sie uns';
?>
<div class="row">
    <!-- <div class="col-lg-8 offset-lg-4"> -->
    <div class="col-lg-12" style="padding-bottom: 1.5rem !important;">
        <ul class="list-group" style="padding-bottom: 2%;">
            <li class="list-group-item"><h2><?php echo $title; ?></h2></li>
            <li class="list-group-item">
        <!-- <h2 style="margin-top: 15px; line-height: 60px; padding: 0 36px;">Hier finden Sie uns:</h2> -->
            <div id="mapid"></div>                 
                <script>
        var mymap = L.map('mapid').setView([52.5462, 10.1867], 14);
        //mymap.boxZoom.disable();
        mymap.keyboard.disable();
        mymap.dragging.disable();
        //mymap.removeControl(mymap.zoomControl);
        mymap.scrollWheelZoom.disable();
        
       L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
         'attribution':  'Kartendaten &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> Mitwirkende'
         }).addTo(mymap);
         
        var circle = L.circle([52.548796, 10.180304], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 50
        }).addTo(mymap);
                </script>
        </li>
        </ul>
    </div>
</div>
