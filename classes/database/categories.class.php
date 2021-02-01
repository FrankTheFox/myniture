<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class Categories
{
    private $mysqli_;
    private $title_;
        
    const CATEGORY_NAME = 'category_name';
    const CATEGORY_SLUG = 'category_slug';
    
    public function __construct($mysqli)
    {
        $this->mysqli_ = $mysqli;
        $this->title_ = 'Kategorien';
    }
    
    public function GetCategoryNr($category_name)
    {
        $query = "SELECT "
                . "categories.category_nr "
                . "FROM categories "
                . "WHERE categories.category_name  = '" .$category_name."'";
        
        $result =  $this->mysqli_->query($query);
        $row = $result->fetch_row();                
        
        return $row[0];
    }
    
    public function GetData()
    {                        
        $query = 
                "SELECT "
                    . "categories.".self::CATEGORY_NAME.", "
                    . "categories.".self::CATEGORY_SLUG." "                              
                . "FROM categories ORDER BY categories.".self::CATEGORY_NAME." ";                    
        
        $result = $this->mysqli_->query($query);
        
        $data = [];
        
        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }
           
        return $data;        
    }
    
    public function GetTitle()
    {
        return $this->title_;
    }
    
    public function AddCategory($name)
    {
        $slug = $this->PrepareSlug($name);
        
        $query = "INSERT INTO categories (category_name, category_slug) "
                   . "VALUES "
                   . " ('".$name."', '" .$slug . "') ";
           
        $result =  $this->mysqli_->query($query);                   
    }        
    
    private function PrepareSlug($name)
    {
        $slug = trim($name);
        $slug = str_replace(' ', '',$slug);
        $slug = mb_strtolower($slug);

        $search = array(
                "ç","æ","œ","á",
                "é","í","ó","ú",
                "à","è","ì","ò",
                "ù","ä","ë","ï",
                "ö","ü","ÿ","â",
                "ê","î","ô","û",
                "å","ø","ß","/",
                ",","und");

        $replace = array(
                "c","ae","oe","a",
                "e","i","o","u",
                "a","e","i","o",
                "u","ae","e","i",
                "oe","ue","y","a",
                "e","i","o","u",
                "a","o","ss","-",
                "-","-");      

        $slug = str_replace($search, $replace, $slug);

        return $slug;
    }
}