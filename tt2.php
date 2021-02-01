<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$hashed_password = password_hash('fshn1234', PASSWORD_BCRYPT);
var_dump($hashed_password);

echo '////////////////////////////////////<br>';

$date = date("Y-m-d H:i:s");
var_dump($date);



function isValidEmail($email): bool
{ 
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

if(isValidEmail('bazmega@kapa')){
    echo 'PPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP<br>';
}
else{
    echo 'QQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQ<br>';
}