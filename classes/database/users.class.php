<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class UsersTable
{    
    private $mysqli_;
    private $user_name_;
    private $user_password_;
    private $user_id_;
    
    public function __construct($mysqli)
    {        
        $this->mysqli_ = $mysqli;
        $this->user_name_ = '';
        $this->user_password_ = '';
        $this->user_id_ = false;
    }
    
    public function SetUserName($user_name): void
    {
        $this->user_name_ = $user_name;
    }
    
    public function SetPassword($user_password): void
    {
        $this->user_password_ = $user_password;
    }
    
    public function GetUserName(): string
    {
        return $this->user_name_;
    }
    
    public function GetPassword(): string
    {
        return $this->user_password_;
    }
    
    public function GetSavedCredential(): array
    {                           
        $credential_from_email = false;
        
        
        if(empty($this->user_name_)){
            return array(); //empty
        }                
        
        if(filter_var($this->user_name_, FILTER_VALIDATE_EMAIL) !== false){
            $query = 
                "SELECT user_id, user_email, user_password, user_activated "
                . "FROM users "
                . "WHERE user_email = '".$this->user_name_."'";
            $credential_from_email = true;
        }
        else{
            $query = 
                "SELECT user_id, user_name, user_password "
                . "FROM users "
                . "WHERE user_name = '".$this->user_name_."'";
        }                
        
        $result = $this->mysqli_->query($query);
        
        if($result->num_rows === 0){
            return array(); //empty
        }
        
        $row = $result->fetch_assoc();        
        
        if($credential_from_email === true){
            if($row['user_activated'] == 0){
                return array(); //empty
            }
                                                
            $data = [
                'user_name' => $row['user_email'], 
                'user_password' => $row['user_password']];
        }
        else{        
            $data = [
                'user_name' => $row['user_name'], 
                'user_password' => $row['user_password']];
        }
                                
        $this->user_id_ = $row['user_id'];
                                                      
        return $data;
    }
    
    public function SaveTokens($identifier, $securitytoken): bool
    {        
        if(empty($this->user_id_)){            
            return false;
        }
        
        $query = 
                "UPDATE users "
               . "SET user_identifier_token = '" .$identifier. "', "
               . " user_security_token = '" . $securitytoken. "' "
               . "WHERE user_id = ". $this->user_id_;                                            
        
        if($this->mysqli_->query($query) === FALSE) {
            return false;
        }       
        
        return true;
    }
    
    public function IsCustomer(): bool
    {
        if(empty($this->user_id_)){            
            return false;
        }
        
        $query = 
                "SELECT  user_name, user_type, user_activated "
                . "FROM users "
                . "WHERE user_email = '$this->user_name_'"; // AND user_name IS NULL
        
        $result = $this->mysqli_->query($query);                               
        
        if($result === FALSE) {
            echo "Error: $this->mysqli_->error   <br>";
            return false;
        }
        
        $row = $result->fetch_assoc();
        
        //printf ("%s (%s)\n", $row['user_type'], $row['user_activated']);
        
        if($row['user_type'] == 2 && $row['user_activated'] == 1){
            return true;
        }
        
        return false;
    }
}