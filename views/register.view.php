<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<form action="/myniture/anmelden.php" method="post">
<?php
    if(!empty($errorstring)){
        echo $errorstring;
    }
    
    if(!empty($messagestring)){
        echo $messagestring;
    }
?>    
    <input type="hidden" name="form_name" value="registerForm"/>
    <p>
    <p>
        Sie möchten online einkaufen? Dann möchten wir Sie bitten, sich zunächst zu registrieren.
        Nachdem Sie das Formular abgesendet haben, senden wir Ihnen eine E-Mail. 
        Durch Klicken auf den Link bestätigen Sie Ihre E-Mail Adresse.
    </p>
    <p>
        Der Link ist <strong>24</strong> Stunden gültig. Nach Bestätigung der E-Mail Adresse
        wird Ihr Benutzerkonto frei gegeben und Sie können sich einloggen.
    </p>
        Wir freuen uns auf Ihre Registrierung.
    </p>
    <h4 style="font-family: Oswald; letter-spacing: 0px; font-weight: 700; margin-top: 48px;">Personendaten</h4><hr>
    <p>
        <h5>Vorname: *</h5>
        <input type="text"  name="forename" required="required">
    </p>
    <p>
        <h5>Nachname: *</h5>
        <input type="text"  name="lastname" required="required">
    </p>   
    <p>
        <h5>E-Mail-Adresse: *</h5>
        <input type="text"  name="email" required="required">
    </p>
    <p>
        <h5>Telefonnummer: *</h5>
        <input type="text"  name="phonenumber" required="required">
    </p>
    <h4 style="font-family: Oswald; letter-spacing: 0px; font-weight: 700; margin-top: 48px;">Adressdaten</h4><hr>
    <p>
        <h5>Postleitzahl: *</h5>
        <input type="text"  name="zipcode" required="required">
    </p>
    <p>
        <h5>Ort: *</h5>
        <input type="text"  name="city" required="required">
    </p>  
    <p>
        <h5>Straße: *</h5>
        <input type="text"  name="streetname" required="required">
    </p> 
    <p>
        <h5>Hausnummer: *</h5>
        <input type="text"  name="streetnumber">
    </p>            
    <h4 style="font-family: Oswald; letter-spacing: 0px; font-weight: 700; margin-top: 48px;">Anmeldedaten</h4><hr>
    <p>
        <h5>Passwort: *</h5>
        <input type="password" name="credential" maxlength="40" required="required">
        <p><span>Das Passwort muss mindestens acht Zeichen lang sein.</span></p>
    </p>
    <p>
        <h5>Passwort wiederholen: *</h5>
        <input type="password" name="credential_confirm" maxlength="40" required="required">
    </p>
    <p>(*) Plichtfelder</p>
    <p>    
    <hr>
        <input type="submit" name="register" value="Registrieren">        
    </p>
</form>