<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class RegisterForm
{
    private $forename_;
    private $lastname_;
    private $email_;
    private $credential_;
    private $credential_confirm_;
    private $phone_number_;
    private $zipcode_;
    private $errors_;
    private $city_;
    private $street_number_;
    private $street_name_;
    private $input_post_;
    private $messages_;
    
    const CREDENTIAL_LENGTH = 8;          
    
    public function __construct()
    {                
        $this->forename_ = false;
        $this->lastname_ = false;
        $this->credential_ = false;
        $this->credential_confirm_ = false;
        $this->email_ = false;
        $this->phone_number_ = false;
        $this->zipcode_ = false;
        $this->city_ = false;
        $this->street_number_ = false;
        $this->street_name_ = false;
        $this->input_post_ = false;
        $this->errors_ = [];
        $this->messages_ = [];
        
        $request_method = filter_input(
                                    INPUT_SERVER,
                                    'REQUEST_METHOD',
                                    FILTER_SANITIZE_STRING);               
        
        if($request_method === 'POST')
        {       
            if('registerForm' === filter_input(
                                            INPUT_POST,
                                            'form_name', 
                                            FILTER_SANITIZE_SPECIAL_CHARS))
            {                                  
                $this->forename_ = filter_input(
                                            INPUT_POST,
                                            'forename', 
                                            FILTER_SANITIZE_SPECIAL_CHARS);

                $this->lastname_ = filter_input(
                                            INPUT_POST,
                                            'lastname', 
                                            FILTER_SANITIZE_SPECIAL_CHARS);

                $this->credential_ = filter_input(
                                            INPUT_POST,
                                            'credential', 
                                            FILTER_SANITIZE_STRING);            
                
                $this->credential_confirm_ = filter_input(
                                            INPUT_POST,
                                            'credential_confirm', 
                                            FILTER_SANITIZE_STRING);  
                
                $this->phone_number_ = filter_input(
                                            INPUT_POST,
                                            'phonenumber', 
                                            FILTER_SANITIZE_STRING);  
                
                $this->email_ = filter_input(
                                            INPUT_POST,
                                            'email', 
                                            FILTER_SANITIZE_EMAIL);
                
                $this->zipcode_ = filter_input(
                                            INPUT_POST,
                                            'zipcode', 
                                            FILTER_SANITIZE_STRING);
                
                $this->city_ = filter_input(
                                            INPUT_POST,
                                            'city', 
                                            FILTER_SANITIZE_STRING);
                
                $this->street_number_ = filter_input(
                                            INPUT_POST,
                                            'streetnumber', 
                                            FILTER_SANITIZE_STRING);
                
                $this->street_name_ = filter_input(
                                            INPUT_POST,
                                            'streetname', 
                                            FILTER_SANITIZE_STRING);                                
                
                $this->input_post_ = true;
                
                if(strlen($this->credential_) < self::CREDENTIAL_LENGTH)     {
                    $this->errors_[] = 'Das eingegebene Passwort ist zu kurz!';
                }
                
                if($this->credential_confirm_ !== $this->credential_){
                    $this->errors_[] = 'Die eingegebenen Passwörter stimmen nicht überein!';
                }
            }            
        }       
    }
    
    public function HasPost(): bool
    {
        return $this->input_post_;                     
    }    
    
    public function Render()
    {
        $errorstring = false;
        
        if(empty($this->errors_) === false){
            $errorstring = '<fieldset>';
            $errorstring .= '<legend>Folgende <span style="color:red;">Fehler</span> sind aufgetreten:</legend>';
            $errorstring .= '<p style="color:red">';
            $errorstring .= '<ul>'; 
            
            foreach($this->errors_ as $error)
            {
                $errorstring .= '<li>'.$error.'</li>';
            }
            
            $errorstring .= '</ul>'; 
            $errorstring .= '</p>'; 
            $errorstring .= '</fieldset>'; 
        }
        
        
        $messagestring = false;
        
        if(empty($this->messages_) === false){
            $messagestring = '<fieldset>';            
            $messagestring .= '<p style="color:blue">';
            $messagestring .= '<ul>'; 
            
            foreach($this->messages_ as $message)
            {
                $messagestring .= '<li>'.$message.'</li>';
            }
            
            $messagestring .= '</ul>'; 
            $messagestring .= '</p>'; 
            $messagestring .= '</fieldset>'; 
        }
        
        
        include(dirname(__FILE__).'/../views/register.view.php');
    }
    
    public function SetMessage(string $message): void
    {
        $this->messages_[] = $message;
    }
    
    public function SetError(string $error_string): void
    {
        $this->errors_[] = $error_string;
    }
    
    public function GetCredential(): string
    {        
        return $this->credential_;
    }        
    
    public function GetForename(): string
    {        
        return $this->forename_;
    }
    
    public function GetLastname(): string
    {        
        return $this->lastname_;
    }
    
    public function GetEmail(): string
    {        
        return $this->email_;
    }
    
    public function GetPhoneNumber(): string
    {        
        return $this->phone_number_;
    }
    
    public function GetCity(): string
    {        
        return $this->city_;
    }        
    
    public function GetStreetNumber(): string
    {        
        return $this->street_number_;
    }    
    
    public function GetStreetName(): string
    {        
        return $this->street_name_;
    }
}