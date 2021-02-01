<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class Articles
{
    private $mysqli_;
    
    //State
    const STATE_RESERVED = '3';
    const STATE_SOLD = '2';
    const STATE_ONLINE = '1';
    const STATE_OFFLINE = '0';
    
    //Article Field Names
    const ARTICLE_NR = 'article_nr';
    const ARTICLE_ID = 'article_id';
    const ARTICLE_NAME = 'article_name';
    const ARTICLE_SLUG = 'article_slug';
    const ARTICLE_PRICE = 'article_price';
    const ARTICLE_STATE = 'article_state';
    const ARTICLE_DESCRIPTION = 'article_description';    
    
    public function __construct($mysqli)
    {
        $this->mysqli_ = $mysqli;
    }
    
    private function ProcessData($result)
    {        
        $data = [];
    
        while($row = $result->fetch_assoc()){
            if($row['article_state'] === Articles::STATE_SOLD){
                $row['article_state'] = 'verkauft';
            }
            elseif($row['article_state'] === Articles::STATE_ONLINE){
                $row['article_state'] = 'online';
            }
            elseif($row['article_state'] === Articles::STATE_OFFLINE){
                $row['article_state'] = 'offline';
            }
            else{
                $row['article_state'] = 'reserviert';
            }
            
            $data[] = $row;
        }
        return $data;
    }
    
    public function CountRecordsByCategory($category_slug): int
    {
        $query = 
                "SELECT "
                . "COUNT(*) "         
                . "FROM articles "
                . "INNER JOIN categories ON "
                . "articles.fk_category_nr = categories.category_nr "
                . "WHERE categories.category_nr = "
                . "(SELECT category_nr FROM categories WHERE category_slug = '"
                . $category_slug. "')";                
        
        $result =  $this->mysqli_->query($query);
        $row = $result->fetch_row();
        
        return $row[0];
    }
    
    public function GetByCategory($category_slug, $offset, $row_count)
    {               
        $query = 
                "SELECT "
                    . "articles.".self::ARTICLE_NR.", "
                    . "articles.".self::ARTICLE_ID.", "
                    . "articles.".self::ARTICLE_SLUG.", "
                    . "articles.".self::ARTICLE_PRICE.", "
                    . "articles.".self::ARTICLE_NAME.", "
                    . "articles.".self::ARTICLE_STATE." "                    
                . "FROM articles "
                . "INNER JOIN categories ON "
                . "articles.fk_category_nr = categories.category_nr "
                . "WHERE categories.category_nr = "
                . "(SELECT category_nr FROM categories WHERE category_slug = '"
                . $category_slug. "')";
                
        $query .=  ' LIMIT '.$offset.', '.$row_count;      
        $result = $this->mysqli_->query($query);    
               
        return $this->ProcessData($result);
    }        
    
    public function GetByDate($offset, $row_count)
    {                
        $query = 
                "SELECT "
                    . "articles.".self::ARTICLE_NR.", "
                    . "articles.".self::ARTICLE_ID.", "
                    . "articles.".self::ARTICLE_SLUG.", "
                    . "articles.".self::ARTICLE_NAME.", "
                    . "articles.".self::ARTICLE_PRICE.", "
                    . "articles.".self::ARTICLE_STATE." "                    
                . "FROM articles ORDER BY DATE(articles.article_date) DESC";
                
        $query .=  ' LIMIT '.$offset.', '.$row_count;                                                
        $result = $this->mysqli_->query($query);
        
        return $this->ProcessData($result);
    }
    
    public function GetByDetail($article_slug)
    {                
        $query = 
                "SELECT "
                    . "articles.".self::ARTICLE_NR.", "
                    . "articles.".self::ARTICLE_ID.", "
                    . "articles.".self::ARTICLE_SLUG.", "                                  
                    . "articles.".self::ARTICLE_NAME.", "
                    . "categories.category_name, "
                    . "articles.".self::ARTICLE_PRICE.", "
                    . "articles.".self::ARTICLE_STATE.", "                    
                    . "articles.".self::ARTICLE_DESCRIPTION." "
                . "FROM articles, categories "
                    . "WHERE articles.".self::ARTICLE_SLUG." = '" .$article_slug."'"                        
                . " AND articles.fk_category_nr = categories.category_nr";
        
        $result =  $this->mysqli_->query($query);
        
        echo $this->mysqli_->error.'<br>';
        
        return $this->ProcessData($result);
    }
    
    public function UpdateArticle(
                            $article_id,
                            $category_nr,
                            $article_state,
                            $article_name,
                            $article_material,                            
                            $article_price)
    {       
        if($article_state === 'Verkauft'){
            $article_state = Articles::STATE_SOLD;
        }
        elseif($article_state === 'Online'){
               $article_state = Articles::STATE_ONLINE;
        }
        elseif($article_state === 'Offline'){
           $article_state = Articles::STATE_OFFLINE;
        }
        else{
            $article_state =  Articles::STATE_RESERVED;
        }
                 
         $query = "UPDATE articles "
                 . "SET "
                 . "article_state = '".$article_state. "', "
                 . "article_name = '".$article_name. "', "                 
                 . "article_description = '".$article_description. "', "
                 . "article_price = '".$article_price. "', "
                 . "fk_category_nr = ".$category_nr. " "
                 . "WHERE article_id = '".$article_id."' ";                  
         
          $result =  $this->mysqli_->query($query);                                  
    }
    
    public function NewArticle(
                            $article_id,
                            $acategory_nr,
                            $article_state,
                            $article_name,                                               
                            $article_description,
                            $article_price)
    {
        if($article_state === 'Verkauft'){
            $article_state = Articles::STATE_SOLD;
        }
        elseif($article_state === 'Online'){
               $article_state = Articles::STATE_ONLINE;
        }
        elseif($article_state === 'Offline'){
           $article_state = Articles::STATE_OFFLINE;
        }
        else{
            $article_state =  Articles::STATE_RESERVED;
        }

        $article_slug = $this->PrepareSlug($article_name);
        
        $today = date("Y-m-d H:i:s", strtotime('+2 hour'));                      
        $query = "INSERT INTO "
                ."articles "
                ."(article_id, "
                . "article_name, "
                . "article_slug, "                
                . "article_price, "
                . "article_description, "
                . "article_date, "
                . "fk_category_nr) "
                ."VALUES "
                . "('".$article_id."', "
                . "'".$article_name."', "
                . "'".$article_slug."', "             
                . "'".$article_price."', "
                . "'".$article_description."', "
                . "'".$today."', "
                . "".$acategory_nr.")";                
        
         return $this->mysqli_->query($query);                 
    }
    
    public function DeleteArticle($article_id): bool
    {
        $query = "DELETE FROM articles WHERE article_id = '".$article_id."' ";                        
        $result =  $this->mysqli_->query($query);
        
        if($this->mysqli_->error){
            //$result->free_result();
            return false;
        }
        
        //$result->free_result();
        return true;
    }
    
    public function CountAllRecords(): int
    {
        $query = 
                "SELECT "
                . "count(*) "
                . "FROM "
                . "articles";
        
        $result =  $this->mysqli_->query($query);
        $row = $result->fetch_row();
        
        return $row[0];
    }
    
    public function ArticleIdExists($article_id): bool
    {        
        $query = 
                "SELECT "                    
                    . "articles.".self::ARTICLE_ID." "                   
                . "FROM articles "
                    . "WHERE articles.".self::ARTICLE_ID." = '" .$article_id."'";                                       
        
        $result =  $this->mysqli_->query($query);     
                               
        if($result->num_rows > 0)
        {
            return true;
        }       
        
        return false;
    }
    
    public function ArticleNameExists($article_name): bool
    {        
        $query = 
                "SELECT "                    
                    . "articles.".self::ARTICLE_NAME." "                   
                . "FROM articles "
                    . "WHERE articles.".self::ARTICLE_NAME." = '" .$article_name."'";                                       
        
        $result =  $this->mysqli_->query($query);     
                               
        if($result->num_rows > 0)
        {
            return true;
        }       
        
        return false;
    }
    
    
    public function PrepareSlug($name)
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