<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class LoginForm
{
    private $identity_;
    private $credential_;   
    private $remember_;
    private $errors_;
    
    const CREDENTIAL_LENGTH = 8;
    const IDENTITIY_LENGTH = 5;
    
    public function __construct()
    {                                
        $this->identity_ = false;
        $this->credential_ = false;
        $this->remember_ = false;
        $this->errors_ = [];
        
        $request_method = filter_input(
                                    INPUT_SERVER,
                                    'REQUEST_METHOD',
                                    FILTER_SANITIZE_STRING);               
        
        if($request_method === 'POST'){            
            if('loginForm' === filter_input(
                                    INPUT_POST,
                                    'form_name', 
                                    FILTER_SANITIZE_SPECIAL_CHARS))
            {                                        
                $this->identity_ = filter_input(
                                            INPUT_POST,
                                            'identity', 
                                            FILTER_SANITIZE_SPECIAL_CHARS);

                $this->credential_ = filter_input(
                                            INPUT_POST,
                                            'credential', 
                                            FILTER_SANITIZE_SPECIAL_CHARS);

                $this->remember_ = filter_input(
                                            INPUT_POST,
                                            'remember', 
                                            FILTER_SANITIZE_SPECIAL_CHARS);            

                if(strlen($this->identity_) < self::CREDENTIAL_LENGTH)     {
                    $this->errors_[] = 'Passwort vergessen einzugeben, es ist zu kurz oder falsch!';
                }  

                if(strlen($this->identity_) < self::IDENTITIY_LENGTH){
                    $this->errors_[] = 'Benutzername vergessen einzugeben.';
                }                        
            }
            
        }        
    }
    
    public function SetError(string $error_string): void
    {
        $this->errors_[] = $error_string;
    }
    
    public function GetIdentity(): string
    {        
        return $this->identity_;
    }
    
    public function GetCredential(): string
    {        
        return $this->credential_;
    }
    
    public function GetRemember(): bool
    {        
        if($this->remember_){
            return true;
        }
        
        return false;
    }
    
    public function Render()
    {
        $errorstring = false;
        
        if(empty($this->errors_) === false){
            $errorstring = '<fieldset>';
            $errorstring .= '<legend>Folgende <span style="color:red;">Fehler</span> sind aufgetreten:</legend>';
            $errorstring .= '<p style="color:red">';
            
            foreach($this->errors_ as $error)
            {
                $errorstring .= $error.'<br>';
            }
            
            $errorstring .= '</p>'; 
            $errorstring .= '</fieldset>'; 
        }
                        
        include(dirname(__FILE__).'/../views/login.view.php');        
    }   
}