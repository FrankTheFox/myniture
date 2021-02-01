<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class Auth
{
    private $users_;   
    
    public function __construct($users)
    {        
        $this->users_ = $users;
    }     
    
    public function SetUsername($user_name)
    {        
        $this->users_->SetUsername($user_name);
    }
    
    private function GenerateToken(): string
    {
        $token = bin2hex(random_bytes(32));        
        return $token;
    }
    
    public function RememberMeCookie()
    {            
        $identifier_token = $this->GenerateToken();
        $security_token = $this->GenerateToken();
        $params = session_get_cookie_params();
        setcookie(
                'identifier',
                $identifier_token,
                time()+(3600*24*365),
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']);
        setcookie(
                'securitytoken',
                $security_token,
                time()+(3600*24*365),
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']);

        $data['identity'] = $identifier_token;
        $data['securitytoken'] = $security_token;

        return $data;
    }
    
    public function SetUserPassword($user_password)
    {        
        $this->users_->SetPassword($user_password);
    }
    
    public function Authenticate(): bool
    {
        $db_data = $this->users_->GetSavedCredential(); //database
        
        if(empty($db_data)){
            return false;
        }                
        
        if($db_data['user_name'] !== $this->users_->GetUsername())
        {    
            return false;
        }
        
        if(password_verify($this->users_->GetPassword(), $db_data['user_password']) === false){
            return false;
        }
        
        return true;
    }
    
    public function IsCustomer(): bool
    {
        $username = $this->users_->GetUsername();
        
        if(empty($username)){
            return false;
        }               
        
        return $this->users_->IsCustomer();
    }
        
    
}