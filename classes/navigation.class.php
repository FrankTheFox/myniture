<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class Navigation
{
    private $data_;
    private $title_;
    
    public function __construct($data)
    {
        $this->data_ = $data;                
        $this->title_ = 'Navigation';
    }
    
    public function GetData()
    {
        return $this->data_;
    }
    
    public function GetTitle()
    {
        return $this->title_;
    }        
}