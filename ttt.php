<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require('classes/database/articles.class.php');
require('classes/database/categories.class.php');
require('classes/articleform.class.php');


final class Page
{
    private $title_;
    private $form_;
    private $config_;
    private $mysqli_;
    
    public function __construct()
    {
        if(session_status() === PHP_SESSION_NONE){            
            session_start();
        }
        
        $this->title_ = 'ttt';
        $this->form_ = ArticleForm::WithNewArticle();
        $this->form_->SetAction('/myniture/ttt.php');
        
        require('data/config.inc.php');
        
        $this->config_ = new Config();
        $this->title_ = $this->config_::SITE_TITLE;
        $this->mysqli_ = new mysqli(
                                $this->config_::DB_HOST,
                                $this->config_::DB_USER,
                                $this->config_::DB_PASSWORD,
                                $this->config_::DB_DATABASE,
                                $this->config_::DB_PORT);
        $this->mysqli_->set_charset($this->config_::DB_CHARSET);     
    }
    
    public function __destruct()
    {
        $this->mysqli_->close();
    }
    
    public function Render()
    {
        include('views/header.php');
        $this->form_->Validate();
        $this->Save();
        $this->form_->Render();
        include('views/footer.php');
    }
    
    public function Save(): void
    {                               
        if($this->form_->HasPost()){
            if($this->form_->HasErrorMessages()){
                return;
            }
            
            $categories = new Categories($this->mysqli_);
            $categorie_nr = $categories->GetCategoryNr($this->form_->GetCategoryName());
            
            if(empty($categorie_nr)){
                $categories->AddCategory($this->form_->GetCategoryName());
                $categorie_nr = $categories->GetCategoryNr($this->form_->GetCategoryName());
            }            
            
            $article_values = $this->form_->GetValues();
            
            $articles = new Articles($this->mysqli_);
            
            if($articles->ArticleIdExists($article_values['article_id'])){
                $this->form_->SetErrorMessage('Der Artikel exisitiert bereits.');
                return;
            }
            
            if($articles->ArticleNameExists($article_values['article_name'])){
                $this->form_->SetErrorMessage('Der Artikelname exisitiert bereits.');
                return;
            }
            
            $articles->NewArticle(
                                $article_values['article_id'],
                                $categorie_nr,
                                $article_values['article_state'],
                                $article_values['article_name'],
                                $article_values['article_description'],
                                $article_values['article_price']);                                                                                                   
            
            //thumb            
            $thumb_filename = $this->form_->GetThumbFile();
            
            $result = move_uploaded_file(
                             $thumb_filename[0]['tmp_name'], 
                             __DIR__.'/thumb/'.$article_values['article_id'].'.jpg');
            ///////////////////////////////////
            
            //images 
            $image_files = $this->form_->GetImageFiles();
            $file_count = count($image_files[0]['tmp_name']);
            
            $j = 1;
                
            for($i = 0; $i < $file_count; $i++){                                                                                         
                $result = move_uploaded_file(
                             $image_files[0]['tmp_name'][$i], 
                             __DIR__.'/image/'.$article_values['article_id'].'-'.$j.'.jpg');
                                
                $j++;
            }                                                                                                       
        }                        
    }
}

$page = new Page();
$page->Render();