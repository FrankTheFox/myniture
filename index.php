<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('classes/database/articles.class.php');
require('classes/database/categories.class.php');
require('classes/navigation.class.php');

final class Page
{
    private $title_;
    private $config_;
    private $mysqli_;
    private $routes_;    
    
    public function __construct()
    {        
        if(session_status() === PHP_SESSION_NONE){            
            session_start();
        }                               
        
        require('data/config.inc.php');
        $this->config_ = new Config();
        $this->title_ = $this->config_::SITE_TITLE;
        $this->mysqli_ = new mysqli(
                                $this->config_::DB_HOST,
                                $this->config_::DB_USER,
                                $this->config_::DB_PASSWORD,
                                $this->config_::DB_DATABASE,
                                $this->config_::DB_PORT
                );
        $this->mysqli_->set_charset($this->config_::DB_CHARSET);
        
        $this->routes_[
            '#(?:^\/myniture)\/?((seite)\/([1-9][0-9]*))?$#'
            ] = [$this, 'ArticlesDataByDate'];
        
        $this->routes_[
            '#(?:^\/myniture)\/(index\.php)\/?((seite)\/([1-9][0-9]*))?$#'
            ] = [$this, 'ArticlesDataByDate'];
        
        $this->routes_[
            '#(?:^\/myniture\/(index\.php)\/(kat)(?:\/([a-z]+(?:-?[a-z])+)))(\/(seite)\/([1-9][0-9]*))?$#'
            ] = [$this, 'ArticlesDataByCategory'];
        
        $this->routes_[
    '       #(?:^\/myniture\/(index\.php)\/(art)(?:\/([a-z0-9\-]+(?:-?[a-z0-9\+\.])+-?)))$#'
            ] = [$this, 'ArticleDataByDetail'];
        
        
        $request_method = filter_input(
                                    INPUT_SERVER,
                                    'REQUEST_METHOD',
                                    FILTER_SANITIZE_STRING);                        
        
        if($request_method === 'POST'){ 
            if(filter_input(
                        INPUT_POST,
                        'form_name', 
                        FILTER_SANITIZE_STRING) === 'shoppingCartForm')
            {                
                $article_id = filter_input(
                                    INPUT_POST,
                                    'article_id', 
                                    FILTER_SANITIZE_STRING);
                
                $article_name = filter_input(
                                    INPUT_POST,
                                    'article_name', 
                                    FILTER_SANITIZE_STRING);
                
                $article_price = filter_input(
                                    INPUT_POST,
                                    'article_price', 
                                    FILTER_SANITIZE_STRING);
                
                //$_SESSION['cart'] .....
                header('Location: /myniture/index.php');
            }
        }
        
    }
    
    public function __destruct()
    {
        $this->mysqli_->close();
    }
    
    public function RenderNavigationData()
    {                
        $data[] = ['label' => 'Datenschutz', 'url' => 'datenschutz.php'];
        $data[] = ['label' => 'Impressum', 'url' => 'impressum.php'];
        $data[] = ['label' => 'Kontakt', 'url' => 'kontakt.php'];
        $data[] = ['label' => 'AGB', 'url' => 'agb.php'];
        
        if(isset($_SESSION['login']) && $_SESSION['login']){
            $data[] = ['label' => 'Administration', 'url' => 'alv.php'];
            $data[] = ['label' => 'Abmelden', 'url' => 'abmelden.php'];
        }
        else{
            $data[] = ['label' => 'Anmelden', 'url' => 'anmelden.php'];
        }         
        
        $navigation = new Navigation($data);
        $data = $navigation->GetData();
        $title = $navigation->GetTitle();
        unset($navigation);
        
?>
<ul class="list-group" style="padding-bottom: 2%;">                    
                <li class="list-group-item"><h2><?php echo $title; ?></h2></li>     
<?php
                   
        foreach($data as $item)
        {
?>
<li class="list-group-item">
<a href="/myniture/<?php echo $item['url']; ?> " style="color:black" ><?php echo $item['label']; ?> </a></li>           
<?php
        }
?>
</ul>
<?php
    }
    
    public function RenderCategoryData()
    {        
        $data = [];
        
        $categories = new Categories($this->mysqli_);
        $data = $categories->GetData();
        $articles = new Articles($this->mysqli_);
        
?>
<ul class="list-group" style="padding-bottom: 2%;">
                <li class="list-group-item"><h2>Kategorien</h2></li>
<?php

        foreach($data as $item)
        {    

?><li class="list-group-item"><a href="/myniture/index.php/kat/<?php echo $item['category_slug'] ?> " style="color:black" ><?php echo $item['category_name'].' ('.$articles->CountRecordsByCategory($item['category_slug']).')';?> </a></li><?php
        }
?>            </ul>
<?php
    }
    
    public function ArticlesDataByDate($matches = null): void
    {                                               
        ///////////////////////////////////////////////////////////////////
        //pagination
        $current_page = 1;     
        
        $articles = new Articles($this->mysqli_);        
        $count_records  = $articles->CountAllRecords();
                
        $articles_per_page = 5;        
        $count_pages = ceil($count_records / $articles_per_page);              
        
        if(!empty($matches[0][4])){           
            if($matches[0][4] > $count_pages){
                 echo 'Diese Seite gibt es nicht. Darum können wir leider keine Artikel anzeigen.<br>';
                 return;
            }
            $current_page =  $matches[0][4];
        }
        
        $offset = ($current_page - 1)  * $articles_per_page;
        //$start = $offset + 1;
        //$end = min(($offset + $articles_per_page), $count_records);                        
        
        $data = $articles->GetByDate($offset, $articles_per_page);               
        ////////////////////////////////////////                        
        
        foreach($data as $item)
        {
            $sold = false;
            ?>
            <li class="list-group-item"><p>Artikel Nr.: <?php echo $item['article_id'];
                if($item['article_state'] === 'verkauft'){
                    $sold = true;
                    ?><span style="color: #FF0000; margin-left: 10%;"><strong>verkauft!</strong></span>
<?php               } 
?>                
                </p><h3><a href="/myniture/index.php/art/<?php echo $item['article_slug'] ?> " style="color:black" > <?php echo $item['article_name']; ?></a></h3>
                <p><img src="/myniture/thumb/<?php echo $item['article_id'] ?>.jpg" width="150"></p>
<?php
            if($sold === false){
?>          <form method="post" novalidate="novalidate" id="shoppingCartArticle">
                <p>Preis: <strong><?php echo $item['article_price']; ?></strong> &euro;</p>    
                <input type="hidden" name="form_name" value="shoppingCartForm">
                <input type="submit" name="submit" value="In den Warenkorb">
                <input type="hidden" name="article_id" value="<?php echo $item['article_id']; ?>"/>
                <input type="hidden" name="article_name" value="<?php echo $item['article_name']; ?>"/>
                <input type="hidden" name="article_price" value="<?php echo $item['article_price']; ?>"/>
                </form>
<?php       }            
        }
        
?>        <li class="list-group-item"><span id="paging"><?php
        
        //links
        // The "back" link
        if($current_page > 1){
?><a href="/myniture/index.php/seite/1" title="Erste Seite">&laquo;</a> <a href="/myniture/index.php/seite/<?php echo ($current_page - 1); ?>" title="Vorherige Seite">&lsaquo;</a>
<?php
        }else{
?>          <span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>
<?php
        }
?>      Seite <?php echo $current_page; ?> von <?php echo $count_pages; ?> 
<?php
        
        // The "forward" link
        if($current_page < $count_pages){
?>          <a href="/myniture/index.php/seite/<?php echo $current_page + 1; ?>" title="Näschste Seite">&rsaquo;</a></a> <a href="/myniture/index.php/seite/<?php echo $count_pages; ?>" title="Letzte Seite">&raquo;</a>
<?php
        }
        else{
            ?>          <span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span></span></li>
<?php       
        }
         ///////////////////////////////////////////////////////////////////
    }
    
    public function ArticlesDataByCategory($matches)
    {        
        $current_page = 1;                                        
        $category_slug = $matches[0][3]; 
        
        ///////////////////////////////////////////////////////////////////
        //pagination
        
        $articles = new Articles($this->mysqli_);        
        $count_records  = $articles->CountRecordsByCategory($category_slug);
                
        $articles_per_page = 5;        
        $count_pages = ceil($count_records / $articles_per_page);              
        
        if(!empty($matches[0][6])){           
            if($matches[0][6] > $count_pages){
                 echo 'Diese Seite gibt es nicht. Darum können wir leider keine Artikel anzeigen.<br>';
                 return;
            }
            
            $current_page =  $matches[0][6];
        }
        
        $offset = ($current_page - 1)  * $articles_per_page;
        //$start = $offset + 1;
        //$end = min(($offset + $articles_per_page), $count_records);                        
        
        $data = $articles->GetByCategory($category_slug, $offset, $articles_per_page);
                
        foreach($data as $item)
        {
?>
            <li class="list-group-item"><p>Artikel Nr.: <?php echo $item['article_id']; 
                if($item['article_state'] === 'verkauft'){
                    ?><span style="color: #FF0000; margin-left: 10%;"><strong>verkauft!</strong></span></p>
<?php
                }
                else{
                    echo '</p>';
                }    
?>                       
                <h3><a href="/myniture/index.php/art/<?php echo $item['article_slug'] ?> " style="color:black" > <?php echo $item['article_name'] ?></a></h3>
                <p><img src="/myniture/thumb/<?php echo $item['article_id'] ?>.jpg" width="150"></p>
<?php
                if(!empty($item['article_price'])){
                        ?><p>Preis: <strong><?php echo $item['article_price']?></strong> &euro;</p>
<?php
                }
                ?>              </li><?php
        }
        
?>
        <li class="list-group-item"><span id="paging">
<?php
        
        // Pagination links
        // The "back" link
        if($current_page > 1){
?><a href="/myniture/index.php/kat/<?php echo $category_slug; ?>/seite/1" title="Erste Seite" class="page-link">&laquo;</a> <a href="/myniture/index.php/kat/<?php echo $category_slug; ?>/seite/<?php echo ($current_page - 1); ?>" title="Vorherige Seite">&lsaquo;</a>
<?php
        }else{
?>          <span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>
<?php
        }
?>      Seite <?php echo $current_page; ?> von <?php echo $count_pages; ?> 
<?php
        
        // The "forward" link
        if($current_page < $count_pages){
?>          <a href="/myniture/index.php/kat/<?php echo $category_slug; ?>/seite/<?php echo $current_page + 1; ?>" title="Näschste Seite">&rsaquo;</a></a> <a href="/myniture/index.php/kat/<?php echo $category_slug; ?>/seite/<?php echo $count_pages; ?>" title="Letzte Seite">&raquo;</a>
<?php
        }
        else{
            ?>          <span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span></span></li>
<?php       
        }
         ///////////////////////////////////////////////////////////////////
    }
    
    public function ArticleDataByDetail($matches)
    {
        $articles = new Articles($this->mysqli_);
        $data = $articles->GetByDetail($matches[0][3]);
        unset($articles);
                
        foreach($data as $item)
        {
?>
            <li class="list-group-item"><p>Artikel Nr.: <?php echo $item['article_id']; 
                if($item['article_state'] === 'verkauft'){
                    ?><span style="color: #FF0000; margin-left: 10%;"><strong>verkauft!</strong></span></p>
<?php
                }
                else{
                    echo '</p>';
                }    
?>                       
                <h3><?php echo $item['article_name'] ?></h3>
                <p><img src="/myniture/image/<?php echo $item['article_id'] ?>-1.jpg" width="250"></p>
<?php
                $i = 2;
        
                if($handle = opendir(dirname(__FILE__).'/image/')){
                    while(false !== ($filename = readdir($handle)))
                    {
                        if ($filename != "." && $filename != "..") {                    
                            if($item['article_id'].'-'.$i.'.jpg' === $filename){                                       
                                ?><p><img src="/myniture/image/<?php echo $filename ?>" width="150"></p><?php
                                $i++;
                            }     
                        }
                    }                

                    closedir($handle);
                    $handle = false;
                }
?>
                <p><?php echo $item['article_description'] ?></p>
<?php
                if(!empty($item['article_price'])){
                        ?><p>Preis: <strong><?php echo $item['article_price']?></strong> &euro;</p>
            </li>
<?php
            }              
        }  
    }
    
    public function Route()
    {        
        $uri = filter_input(INPUT_SERVER, "REQUEST_URI", FILTER_SANITIZE_URL);
        $uri = utf8_decode(urldecode($uri));
        $matches = [];
        
        foreach($this->routes_ as $pattern => $fn)
        {
            $result = preg_match_all(
                    $pattern, 
                    $uri, 
                    $matches ,
                    PREG_SET_ORDER);

            if($result === 1){
                $fn($matches);
                break;
            }
        }
    }        
    
    public function Render()
    {        
        require('views/header.php');
        require('views/jumbotron.php');
?>
<div class="container">
    <div class="row">                
        <div class="col-lg-5">
<?php
            $this->RenderNavigationData();
            $this->RenderCategoryData();            
            
?>            </ul>
        </div>
        <div class="col-lg-7">
            <ul class="list-group" style="padding-bottom: 2%;">                    
                <li class="list-group-item"><h2>Artikel</h2></li>
<?php                       
            $this->Route();      
?> 
            </ul>
        </div>
    </div>

<?php
        require('views/leaflet.php');   
        
        /*
        require('classes/contactform.class.php');
        $form = new ContactForm();

        if($form->Validate() === false){
            //Log file            
        }
        else{
            $form->SendMail();
        }
        
        $form->Render();   
        */     
        require('views/footer.php');
?>
     </div>
<?php
    } 

}

$page = new Page();
$page->Render();