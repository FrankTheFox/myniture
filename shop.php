<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ShoppingCart
{
    private $cnt_articles_;
    
    public function __construct()
    {
        $this->cnt_articles_ = 0;
    }
    
    public function AddArticle()
    {
        $this->cnt_articles_ ++;
    }
    
    public function RemoveArticle()
    {
        if($this->cnt_articles_ > 0){
            $this->cnt_articles_ --;
        }        
    }
    
    public function ListItems() //for PHP Session!!!!
    {
        
    }
    
    public function GetCount()
    {
        return $this->cnt_articles_;
    }
}
 
session_start();

if(empty($_SESSION['cart'])){
    //we have a shopping cart
    $_SESSION['cart'] = new ShoppingCart();
}

//$_SESSION['cart']['id'] = session_id();
//$_SESSION['cart']['article_nr'] = 'A1012';
//$_SESSION['cart']['article_price'] = '150';

echo '/////////////////////////////////<br>';

$_SESSION['cart']->RemoveArticle();
echo $_SESSION['cart']->GetCount();
echo '<br>';

//echo $_SESSION['cart']['id'].'<br>';
//echo $_SESSION['cart']['article_nr'].'<br>';
//echo $_SESSION['cart']['article_price'].'<br>';

echo '/////////////////////////////////<br>';