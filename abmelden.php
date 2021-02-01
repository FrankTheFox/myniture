<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();

if(isset($_COOKIE[session_name()])){
    $params = session_get_cookie_params();
    setcookie(
            session_name(), 
            '', 
            time() - 7000000, 
            $params["path"], 
            $params["domain"], 
            $params["secure"], 
            $params["httponly"]);

    //Cookies entfernen    
    if(isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken'])){        
        setcookie(
                'identifier', 
                '', 
                time()-(3600*24*365), 
                $params["path"], 
                $params["domain"], 
                $params["secure"], 
                $params["httponly"]); 
        setcookie(
                'securitytoken', 
                '', 
                time()-(3600*24*365), 
                $params["path"], 
                $params["domain"], 
                $params["secure"], 
                $params["httponly"]); 
    }
}

if(session_status() === PHP_SESSION_ACTIVE){   
    session_unset();
    session_destroy();
}
 
header('Location: /myniture/index.php');
die();
