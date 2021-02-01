<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('data/config.inc.php');

class Page
{
    private $config_;
    private $mysqli_;
    
    
    public function __construct()
    {
        $this->config_ = new Config();
        $this->title_ = $this->config_::SITE_TITLE;
        $this->mysqli_ = new mysqli(
                                $this->config_::DB_HOST,
                                $this->config_::DB_USER,
                                $this->config_::DB_PASSWORD,
                                $this->config_::DB_DATABASE,
                                $this->config_::DB_PORT);
        
        ///////////////////////////////

        if($this->mysqli_->errno){
            die("Failed to connect to MySQL: " . $this->mysqli_->connect_error());
        }
    }
        
    public function Run()
    {
        $request_method = filter_input(
                            INPUT_SERVER,
                            'REQUEST_METHOD',
                            FILTER_SANITIZE_STRING);

        if($request_method === 'GET'){
            $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_URL);
            $user_activation_hash = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_URL);

            if(!empty($email) && !empty($user_activation_hash)){
                $sql = "UPDATE "
                        . "users "
                        . "SET user_activated = 1 "
                        . "WHERE user_email = '". $email ."' "
                        . "AND user_activation_hash = '". $user_activation_hash ."'";
                $result = $this->mysqli_->query($sql);
            }
        }
    }
}
      
        
$page = new Page();
$page->Run();


