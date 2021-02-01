<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ContactForm
{
    private $fname_;
    private $lname_;
    private $phonenumber_;
    private $streetname_;
    private $postcode_;
    private $residence_;
    private $comment_;
    private $privacy_;
    private $email_;
    private $sendmail_ok_;    
    
    public function __construct()
    {
        if(session_status() === PHP_SESSION_NONE){            
            session_start();
        }
        
        $request_method = filter_input(
                                    INPUT_SERVER,
                                    'REQUEST_METHOD',
                                    FILTER_SANITIZE_STRING);               
        
        if($request_method === 'POST'){                                    
            $this->fname_ = filter_input(
                                        INPUT_POST,
                                        'fname', 
                                        FILTER_SANITIZE_STRING);
            
            $this->lname_ = filter_input(
                                        INPUT_POST,
                                        'lname', 
                                        FILTER_SANITIZE_STRING);
            
            $this->phonenumber_ = filter_input(
                                        INPUT_POST,
                                        'phonenumber', 
                                        FILTER_SANITIZE_STRING);
            
            
            $this->streetname_ = filter_input(
                                        INPUT_POST,
                                        'streetname', 
                                        FILTER_SANITIZE_STRING);
            
            $this->postcode_ = filter_input(
                                        INPUT_POST,
                                        'postcode', 
                                        FILTER_SANITIZE_NUMBER_INT);
            
            $this->residence_ = filter_input(
                                        INPUT_POST,
                                        'residence', 
                                        FILTER_SANITIZE_STRING);
            
            $this->comment_ = filter_input(
                                        INPUT_POST,
                                        'comment', 
                                        FILTER_SANITIZE_STRING);
            
            
            $this->privacy_ = filter_input(
                                        INPUT_POST,
                                        'privacy', 
                                        FILTER_SANITIZE_SPECIAL_CHARS);   
            
            $this->email_ = filter_input(
                                        INPUT_POST,
                                        'email', 
                                        FILTER_SANITIZE_EMAIL);     
            
            $validate_postcode = preg_match('#^\d{5}$#', $this->postcode_);
            
            //if(!$validate_postcode){
            //    echo "dskhskfghsdkshgkdhgdkghdfkghdf<br>";
            //
            $this->sendmail_ok_ = true;
        }
        else{
            $this->fname_ = false;
            $this->lname_ = false;
            $this->phonenumber_ = false;
            $this->streetname_ = false;
            $this->postcode_ = false;
            $this->residence_ = false;
            $this->comment_ = false;
            $this->privacy_ = false;
            $this->email_ = false;
            $this->sendmail_ok_ = false;
        }                                
    }
    
    public function __destruct()
    {
        
    }
    
    public function Render()
    {
        require('views/contactform.view.php');
    }
    
    public function Validate(): bool
    {
        //Validate forename
        
        if(!empty($this->fname_) && preg_match("/^[a-zA-Z ]*$/", $this->fname_) === 0){
            $this->validate_ok_ = false;
        }
        
        if($this->validate_ok_ === false){
            return false;
        }
        
        if(!empty($this->lname_) && preg_match("/^[a-zA-Z ]*$/", $this->lname_) === 0){
            $this->validate_ok_ = false;
        }
        
        if($this->validate_ok_ === false){
            return false;
        }
        
        if(!empty($this->email_) && preg_match("/^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$/", $this->email_) === 0){
            $this->validate_ok_ = false;
        }
        
        if($this->validate_ok_ === false){
             return false;
        }
        
        if(!empty($this->postcode_) && preg_match("/[0-9]{5}$/", $this->postcode_) === 0){
            $this->validate_ok_ = false;
        }
        
        if($this->validate_ok_ === false){
            return false;
        }
        
        $this->phonenumber_ = str_replace(' ','',$this->phonenumber_ );
        
        if(!empty($this->phonenumber_) && preg_match("/([0-9]{5,20})/", $this->phonenumber_) === 0){
            $this->validate_ok_ = false;
        }
        
        if($this->validate_ok_ === false){
            return false;
        }
        
        if(!isset($this->privacy_)){
            return false;
        }
        
        return true;
    }
    
    public function SendMail(): bool
    {
        $success = false;
        
        if($this->sendmail_ok_){
        
            $to = 'zentrale@antik-eicklingen.de';

            //$extra = "From: $absendername <$absendermail>\n";
            //$extra .= "Content-Type: text/html\n";
            //$extra .= "Content-Transfer-Encoding: 8bit\n";
             
             $subject = "Kontaktformular";
             $forename_field = $this->fname_;
             $lastname_field = $this->lname_;
             $email_field = $this->email_;
             $message = $this->comment_;                 
             $body = "Von: $forename_field  $lastname_field\n Kontakt: $this->phonenumber_\n $this->streetname_ \n $this->postcode_\n $this->residence_ \n E-Mail: $email_field\n Nachrticht:\n $message";
             $success = mail($to, $subject, $body, "From: $lastname_field", "-f info@antik-eicklingen.de");
            //$success = mail($to, $subject, $body, "From: Kontakt", "-f uwe.werner@wp13448923.server-he.de");
        }
         
         //if(!$success){
             //Log file
         //}
         
         return $success;
    }
}