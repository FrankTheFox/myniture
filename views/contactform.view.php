<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class=" container-fluid">
    <div class="container" id="contact-form" >                
        <div class="row">                
        <div class="col-lg-12">
        <p>Damit wir uns noch besser auf Ihre Bedüfrnissse einstellen können, ist Ihre Meinung wichtig. Ob Lob, Kritik, Anregungen oder Anfragen - wir warten auf eine Nachricht von Ihnen.</p>        
    </div>
    <div class="col-lg-12 form-group">
        <form class="form-horizontal" method="post" action="/myniture/kontakt.php" >
            <div class="form-group">
            <input type="text" name="fname" placeholder="Vorname*" class="form-control" required="required">
            </div>
            <div class="form-group">
            <input type="text" name="lname" placeholder="Nachname*" class="form-control" required="required">
            </div>
            <div class="form-group">
            <input type="text" name="phonenumber" placeholder="Telefonnummer*" class="form-control" required="required">
            </div>
            <div class="form-group">
            <input type="text" name="streetname" placeholder="Strasse*" class="form-control" required="required">
            </div>
            <div class="form-group">
            <input type="text" name="postcode" placeholder="Postleitzahl*" class="form-control" required="required">
            </div>
            <div class="form-group">
            <input type="text" name="residence" placeholder="Wohnort*" class="form-control" required="required">  
            </div>
             <div class="form-group">
            <input type="email" name="email" placeholder="E-Mail*" class="form-control" required="required">  
            </div>
            <div class="form-group">
            <textarea  name="comment" rows="5" cols="40" placeholder="Kommentar*" class="form-control" required="required"></textarea>
            </div>
            <div class="form-group">
                <label class="form-check-label"><input type="checkbox" value="Ich habe die Hinweise zum Datenschutz gelesen und akzeptiere." name="privacy" required="required"> Ich habe die Hinweise zum <a href="/myniture/datenschutz.php" style="color: black;">Datenschutz</a> gelesen und akzeptiere diese.*</label>
            </div>
            <div class="form-group">
                <p>(*) Benötigte Felder.</p>
            </div>
            <div class="form-group">
            <input type="submit" name="action" value="Nachricht senden">
            </div>
        </form>
    </div>
    </div>
    </div>
</div>
