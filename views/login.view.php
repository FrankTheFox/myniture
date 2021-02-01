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
?>
    <h4>Benutzername oder E-Mail-Adresse:</h4>
    <input type="hidden" name="form_name" value="loginForm"/>
    <input type="text"  name="identity">
    <p><span>Der Benutzername muss mindestens fünf Zeichen lang sein.</span></p>
    <h4>Passwort:</h4>
    <input type="password" name="credential" maxlength="40">
    <p><span>Das Passwort muss mindestens acht Zeichen lang sein.</span></p>
    <p>
        <label>
            <input type="checkbox" name="remember"> Angemeldet bleiben
        </label>
    </p>
    <p>
        Benutzername oder Passwort vergessen?
    </p>
    <p>
    <hr>
        <input type="submit" name="submit" value="Anmelden">        
    </p>
</form>       