<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
define('LOCAL', true);

if(LOCAL)
{
    //Localhost
    class Config
    {
        const DB_ADAPTER = 'mysql';
        const DB_USER = 'root';
        const DB_PASSWORD = '';
        const DB_DATABASE = 'antik-stock';
        const DB_HOST = "localhost";
        const DB_PORT = 3308;
        const DB_CHARSET = 'utf8mb4';
        const SITE_TITLE = 'Antik-Eicklingen';
    }
}
else{
    //Host Europe
    //Test Datenbank!!!!!
    class Config
    {
        const DB_ADAPTER = 'mysql';
        const DB_USER = 'db13448923-hdd02';
        const DB_PASSWORD = 'Ak+5gnU62-db';
        const DB_DATABASE = 'db13448923-hdd02';
        const DB_HOST = "localhost";
        const DB_PORT = 3308;
        const DB_CHARSET = 'utf8mb4';
        const SITE_TITLE = 'Antik-Eicklingen';
    }
}