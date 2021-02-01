<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!--
<footer class="container" style="">
    <ul style="list-style: none;" class="list-group list-group-horizontal-sm">
<li class="list-group-item" style="border: none;"><a class="navbar-link" href="/myniture/impressum.php" title="Impressum">Impressum</a></li>
<li class="list-group-item" style="border: none;"><a class="navbar-link" href="/myniture/contact.php" title="Kontakt">Kontakt</a></li>
<li class="list-group-item" style="border: none;"><a class="navbar-link" href="/myniture/privacy.php" title="Datenschutzerklärung">Datenschutz</a></li>
<li class="list-group-item" style="border: none;"><a  class="navbar-link" href="/myniture/showcopyrights.php" class="showcopyrights">Bildrechte</a></li>
<li class="list-group-item" style="border: none;" id="copy"><div class="modulepadding">© W & L Antiquitäten GmbH</div></li>
<li style="width: 100%; border: none;" class="list-group-item" style="border: none;" id="copy"></li>
    </footer> -->

<div id="cookieHint" style="display: block;"><span class="cookietext">
    Diese Website benutzt Cookies, die für den technischen Betrieb der Website notwendig sind. Wenn Sie die Website weiter nutzen gehen wir von ihrem Einverständnis aus.    
    </span><a href="/myniture/datenschutz.php" class="button">
    Mehr Informationen
    </a><a onclick="var d = new Date(); d = new Date(d.getTime() +1000*60*60*24*730); document.cookie = 'cookiehint=1; expires='+ d.toGMTString() + ';path=/;'; 
    document.getElementById('cookieHint').style.display = 'none';" class="button">
    OK, verstanden
    </a>
</div>
<footer class="container" style="background-color:#2d2d2d; color:#fff;  padding-bottom: .9rem !important;  padding-top: .9rem !important;">
    <div class="container">
        <div class="row">             
            <div class="col-lg-4">
                <address>
                    <strong>W & L Antiquitäten GmbH</strong><br>
                    Braunschweiger Str. 25<br>
                    29358 Eicklingen<br>
                    <span class="fa fa-phone"></span> 05144 57 57 </br>
                    <span class="fa fa-mobile-phone"></span> 0172  50 40 69 68</br>
                    <span class="fa fa-phone"></span> 05082 27 89 94 8 </br>
                    <span class="fa fa-mobile-phone"></span> 0174  16 17 06 0</br>                                        
                    <span class="fa fa-fax"></span> 05144 22 99</br>                                        
                    <span class="fa fa-envelope-o"></span><a href="mailto:zentrale@antik-eicklingen.de" style="color: #fff"> zentrale@antik-eicklingen.de</a></br>
                <address>
            </div>
            <div class="col-lg-4">
                <div><a href="/myniture/impressum.php" title="Impressum"  style="color: #fff">Impressum</a></div>
                <div><a href="/myniture/datenschutz.php" title="Datenschutzerklärung" style="color: #fff">Datenschutz</a></div>
            </div>
            <div class="col-lg-4">
                 &copy; Copyright 2020, W & L Antiquitäten GmbH
            </div>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<!-- Include cookiealert script -->
<script>
(function () {
    "use strict";

    var cookieHint = document.querySelector("#cookieHint");

    if (!cookieHint) {
       return;
    }

    cookieHint.offsetHeight; // Force browser to trigger reflow (https://stackoverflow.com/a/39451131)
        
    // Show the hint if we cant find the "cookiehint" cookie
    if (!getCookie("cookiehint")) {          
        cookieHint.style.display = 'block';
    }
    else{        
        cookieHint.style.display = 'none';
    }
        
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);                         
        var ca = decodedCookie.split(';');
        
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }                        
            
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        
        return "";
    }    
})();
</script>
</body>
</html>