<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */






final class ArticleForm
{    
    private $article_id_;
    private $article_name_;
    private $article_slug_;
    private $article_description_;
    private $article_price_;
    private $article_images_;
    private $article_state_;
    private $article_category_name_;    
    private $error_message_;
    
    public function GetErrorMessage()
    {
        return $this->error_message_; 
    }
    
    private function AttachImages()
    {
        if($handle = opendir(dirname(__FILE__).'/image/')){
            while(false !== ($filename = readdir($handle)))
            {                        
                if(preg_match('#^'.$this->article_id_.'\-[0-9]+?\.jpg$#', $filename)){                            
                    $this->article_images_[] = $filename;
                    //echo '('.$this->article_id_.')'.$filename.'<br>';
                }                                  
            }
            
            closedir($handle);           
        }
    }
    
    private function SetupWithData($data)
    {                
        $this->article_name_ = $data['article_name'];
        $this->article_slug_ = $data['article_slug'];
        $this->article_description_ = $data['article_description'];
        $this->article_price_ = $data['article_price'];
        $this->article_state_ = $data['article_state'];
        $this->article_id_ = $data['article_id'];
        $this->article_category_name_ = $data['category_name'];       
        $this->AttachImages();
    }
    
    private function SetupWithId($article_id)
    {                        
        $this->article_name_ = false;
        $this->article_slug_ = false;
        $this->article_description_ = false;
        $this->article_price_ = false;
        $this->article_state_ = false;
        $this->article_id_ = $article_id;
        $this->article_category_name_ = false;        
        $this->AttachImages();
    }
    
    private function SetupWithNothing()
    {
        $this->article_name_ = false;
        $this->article_slug_ = false;
        $this->article_description_ = false;
        $this->article_price_ = false;
        $this->article_state_ = false;
        $this->article_id_ = false;
        $this->article_category_name_ = false;        
        $this->article_images_ = false;
    }
    
    public static function WithData($data)
    {
        $instance = new self();
        $instance->SetupWithData($data);
        return $instance;
        
    }
    
    public static function WithId($article_id)
    {
        $instance = new self();
        $instance->SetupWithId($article_id);
        return $instance;        
    }
    
    public static function MakeNew()
    {
        $instance = new self();  
        $instance->SetupWithNothing();
        return $instance;        
    }
    
    private function __construct()
    {                        
        //empty
    }

    public function RenderEdit()
    {                                                               
        require('views/article_form_delete_images.php');
        require('views/article_form_edit.view.php');               
    }
    
    public function RenderNew()
    {                                                                     
        require('views/article_form_new.view.php');               
    }
    
    public function RemoveImage($filename)
    {
        if(($key = array_search($filename, $this->article_images_)) !== false) {
            unset($this->article_images_[$key]);
        }
    }
        
    public function __destruct()
    {
        
    }
    
    public function GetSlug()
    {
        return $this->article_slug_;
    }
    
    public function GetId()
    {
        return $this->article_id_;
    }
}


class Page
{    
    private $config_;
    private $mysqli_;
    private $routes_;
    private $error_message_;
    private $imageUploadForm_messages_;
    
    public function __construct()
    {                
        $this->error_message_ = false;
        
        if(session_status() === PHP_SESSION_NONE){            
            session_start();
        }
        
        if(!isset($_SESSION['login']) || !$_SESSION['login']){
            header('Location: /myniture/anmelden.php');
            die();
        }
        
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
    
        
        $this->routes_[
            '#(?:^\/myniture)\/(alv\.php)\/?((seite)\/([1-9][0-9]*))?$#'
            ] = [$this, 'ArticlesDataByDate'];
        
        $this->routes_[
            '#(?:^\/myniture)\/(alv\.php)\/(kat)(?:\/([a-z]+(?:-?[a-z])+))(\/(seite)\/([1-9][0-9]*))?$#'
            ] = [$this, 'ArticlesDataByCategory'];
        
        $this->routes_[
    '       #(?:^\/myniture\/(alv\.php)\/(art)(?:\/([a-z0-9\-]+(?:-?[a-z0-9\+\.])+-?)))$#'
            ] = [$this, 'ArticleDataByDetail'];
        
        $this->routes_[
    '       #(?:^\/myniture\/(alv\.php)\/(art)\/(neu)\/(anlegen)\/?)$#'
            ] = [$this, 'NewArticleDataByDetail'];
        
        
        require('classes/database/articles.class.php');
        require('classes/database/categories.class.php');
        
        ///////////////////////////////////////////////////////////
        //Form evaluation
        ///////////////////////////////////////////////////////////
        
        $request_method = filter_input(
                                    INPUT_SERVER,
                                    'REQUEST_METHOD',
                                    FILTER_SANITIZE_STRING);                        
        
        if($request_method === 'POST'){                        
            if(filter_input(
                        INPUT_POST,
                        'form_name', 
                        FILTER_SANITIZE_STRING) === 'deleteArticleForm')
            {
                $article_id = filter_input(
                                    INPUT_POST,
                                    'article_id', 
                                    FILTER_SANITIZE_STRING);
                
                $articles = new Articles($this->mysqli_);
                //delete article
                $result = $articles->DeleteArticle($article_id);
                                
                if($result === true){
                    //delete images
                    if($handle = opendir(dirname(__FILE__).'/image/')){
                        while(false !== ($filename = readdir($handle)))
                        {                        
                            if(preg_match('#^'.$article_id.'(\-[0-9]+)?\.jpg$#', $filename)){                                                                                                                         
                                $result = unlink(dirname(__FILE__).'/image/'.$filename);                                
                            }                                  
                        }

                        closedir($handle);           
                    }                                                              
                    
                    //delete thumb
                    if($handle = opendir(dirname(__FILE__).'/thumb/')){
                        while(false !== ($filename = readdir($handle)))
                        {                        
                            if(preg_match('#^'.$article_id.'\.jpg$#', $filename)){                                                                                                                         
                                $result = unlink(dirname(__FILE__).'/thumb/'.$filename);                                
                                break;
                            }                                  
                        }

                        closedir($handle);           
                    }                                        
                }
            }
            
            if(filter_input(
                        INPUT_POST,
                        'form_name', 
                        FILTER_SANITIZE_STRING) === 'deleteImageForm')
            {
                $filename = filter_input(
                                    INPUT_POST,
                                    'filename', 
                                    FILTER_SANITIZE_STRING);
                
                $article_id = filter_input(
                                    INPUT_POST,
                                    'article_id', 
                                    FILTER_SANITIZE_STRING);
                
                
                //delete specific file
                unlink(dirname(__FILE__).'/image/'.$filename);                                                                                                
            }
            
            
            if(filter_input(
                        INPUT_POST,
                        'form_name', 
                        FILTER_SANITIZE_STRING) === 'updateArticleForm')
            {
                $article_id = filter_input(
                                            INPUT_POST,
                                            'article_id', 
                                            FILTER_SANITIZE_STRING);
                
                $article_category = filter_input(
                                            INPUT_POST,
                                            'article_category', 
                                            FILTER_SANITIZE_STRING);
                
                $article_state = filter_input(
                                            INPUT_POST,
                                            'article_state', 
                                            FILTER_SANITIZE_STRING);
                
                $article_name = filter_input(
                                            INPUT_POST,
                                            'article_name', 
                                            FILTER_SANITIZE_STRING);                                
                
                $article_description = filter_input(
                                            INPUT_POST,
                                            'article_description', 
                                            FILTER_SANITIZE_STRING);
                
                $article_price = filter_input(
                                            INPUT_POST,
                                            'article_price', 
                                            FILTER_SANITIZE_STRING);                                
                
                $categories = new Categories($this->mysqli_);
                $category_nr = $categories->GetCategoryNr($article_category);
                
                if(empty($category_nr)){
                    $categories->AddCategory($article_category);
                }
                
                $category_nr = $categories->GetCategoryNr($article_category);
                
                $articles = new Articles($this->mysqli_);
                $articles->UpdateArticle(
                                    $article_id,
                                    $category_nr,
                                    $article_state,
                                    $article_name,                                   
                                    $article_description,
                                    $article_price);
            }
            
            if(filter_input(
                        INPUT_POST,
                        'form_name', 
                        FILTER_SANITIZE_STRING) === 'imageUploadForm')
            {                
                
                $article_id = filter_input(
                                            INPUT_POST,
                                            'article_id', 
                                            FILTER_SANITIZE_STRING);
                
                $article_category = filter_input(
                                            INPUT_POST,
                                            'article_category', 
                                            FILTER_SANITIZE_STRING);
                
                $article_state = filter_input(
                                            INPUT_POST,
                                            'article_state', 
                                            FILTER_SANITIZE_STRING);
                
                $article_name = filter_input(
                                            INPUT_POST,
                                            'article_name', 
                                            FILTER_SANITIZE_STRING);                                
                
                $article_description = filter_input(
                                            INPUT_POST,
                                            'article_description', 
                                            FILTER_SANITIZE_STRING);
                
                $article_price = filter_input(
                                            INPUT_POST,
                                            'article_price', 
                                            FILTER_SANITIZE_STRING);
                
                if(empty($article_id)){
                    $this->imageUploadForm_messages_[] = 'Keine Artikelnummer angegeben.';
                }
                
                if(empty($article_category)){
                    $this->imageUploadForm_messages_[] = 'Keine Kategorie angegeben.';
                }
                
                if(empty($article_state)){
                    $this->imageUploadForm_messages_[] = 'Keinen Status angegeben.';
                }
                
                if(empty($article_name)){
                    $this->imageUploadForm_messages_[] = 'Keinen Artikelnamen angegeben.';
                }
                
                if(empty($article_description)){
                    $this->imageUploadForm_messages_[] = 'Keine Beschreibung angegeben.';
                }
                
                if(empty($article_price)){
                    $this->imageUploadForm_messages_[] = 'Keinen Preis angegeben.';
                }
                
                if(empty($this->imageUploadForm_messages_)){
                    
                    $file_count = count($_FILES['article_images']['tmp_name']);
                    
                    $j = 1;
                    
                    for($i = 0; $i < $file_count; $i++)
                    {
                        echo $_FILES['imagefile']['tmp_name'][$j].'<br';
                    }                                    
                
                }
                
                
                //images                
                $i = 1;
        
                if($handle = opendir(dirname(__FILE__).'/image/')){
                    while(false !== ($filename = readdir($handle)))
                    {                        
                        if(preg_match('#^'.$article_id.'(\-[0-9]+)?\.jpg$#', $filename)){                                                                                                                         
                            $i++;
                        }                                  
                    }

                    closedir($handle);           
                }  
                                                                        
                $success = true;                                
                
                foreach($_FILES['article_images']['tmp_name'] as $idx => $tmp_name)
                {
                    if(!empty($_FILES['article_images']['error'][$idx])){
                        $this->imageUploadForm_messages_[] = 'Während des Hochladens der Dateien hat es einen Fehler gegeben.';
                        $success = false;
                        break;
                    }
                    
                    if($_FILES['article_images']['type'][$idx] !== 'image/jpeg'){
                        $this->imageUploadForm_messages_[] = 'Folgernde Bildformate sind für Artikelbilder zulässig: JPEG.';
                        $success = false;
                        break;
                    }
                    
                    $result = move_uploaded_file(
                            $_FILES['article_images']['tmp_name'][$idx],
                            __DIR__.'/image/'.$article_id.'-'.$i++.'.jpg');                                                            
                }                                                
                    
                if(!empty($this->imageUploadForm_messages_))
                {
                    echo '/////////////////////////////////<br>';
                    echo $this->imageUploadForm_messages_.'<br>';
                }
                
                
                                                                                                                                                                                                                       
            }
            
            if(filter_input(
                    INPUT_POST,
                    'form_name', 
                    FILTER_SANITIZE_STRING) === 'newArticleForm')
            {
                $article_id = filter_input(
                                            INPUT_POST,
                                            'article_id', 
                                            FILTER_SANITIZE_STRING);
                
                $article_category = filter_input(
                                            INPUT_POST,
                                            'article_category', 
                                            FILTER_SANITIZE_STRING);
                
                $article_state = filter_input(
                                            INPUT_POST,
                                            'article_state', 
                                            FILTER_SANITIZE_STRING);
                
                $article_name = filter_input(
                                            INPUT_POST,
                                            'article_name', 
                                            FILTER_SANITIZE_STRING);                                
                
                $article_description = filter_input(
                                            INPUT_POST,
                                            'article_description', 
                                            FILTER_SANITIZE_STRING);
                
                $article_price = filter_input(
                                            INPUT_POST,
                                            'article_price', 
                                            FILTER_SANITIZE_STRING);
                
                
                if(empty($article_id) || 
                   empty($article_category) ||
                   empty($article_state) ||
                   empty($article_name) ||
                   empty($article_description) ||
                   empty($article_price))
                {
                    
                    $this->error_message_ = '<p>Bitte folgende Daten in das Formular eintragen:</p>    
<p><ul><li><strong>Artikel Nr.</strong></li>
    <li><strong>Kategorie</strong></li>
    <li><strong>Status</strong></li>
    <li><strong>Name</strong></li>
    <li><strong>Beschreibung</strong></li>
    <li><strong>Preis</strong></li></ul>
</p><p>Sie haben entweder nichts eingegeben oder eine Angabe vergessen.</p>';                                
                }
                else{                                        
                    $categories = new Categories($this->mysqli_);                                        
                    $category_nr = $categories->GetCategoryNr($article_category);
                
                    if(empty($category_nr)){
                        $categories->AddCategory($article_category);
                        $category_nr = $categories->GetCategoryNr($article_category);
                    }                                    
                                        
                    $articles = new Articles($this->mysqli_);
                    $article_slug = $articles->PrepareSlug($article_name);
                    $result = $articles->NewArticle(
                                    $article_id,
                                    $category_nr,
                                    $article_state,
                                    $article_name,
                                    $article_slug,                                    
                                    $article_description,
                                    $article_price);                     
                     
                     if($result === false){
                         $this->error_message_ = '<span style="color:red">Artikel ist schon vorhanden!</span>';
                     }                                             
                }                                                                
            }
            
            if(filter_input(
                        INPUT_POST,
                        'form_name', 
                        FILTER_SANITIZE_STRING) === 'thumbUploadForm')
            {                                 
                $article_id = filter_input(
                                        INPUT_POST,
                                        'article_id', 
                                        FILTER_SANITIZE_STRING);
                
                $filename = $article_id;
                
                if($_FILES['imagefile']['type'] === 'image/jpeg'){
                    $filename .= '.jpg';
                }                                                                                                       
                   
                $result = move_uploaded_file(
                             $_FILES['imagefile']['tmp_name'], 
                             __DIR__.'/thumb/'.$_FILES['imagefile']['name']);                   
                rename(
                     __DIR__.'/thumb/'.$_FILES['imagefile']['name'], 
                     __DIR__.'/thumb/'.$filename);
            }     
        }
        
        
        ///////////////////////////////////////////////////////////
    }
    
    public function __destruct()
    {
        //session_write_close();
        $this->mysqli_->close();
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
    
    
    public function RenderShopAction()
    {                    
        require('classes/shopaction.class.php');
                
        $data[] = ['label' => 'Neuen Artikel anlegen', 'url' => 'alv.php/art/neu/anlegen'];        
        
        $shop_action = new ShopAction($data);
        $data = $shop_action->GetData();
        $title = $shop_action->GetTitle();
        
        ?>
<ul class="list-group" style="padding-bottom: 2%;">                    
                <li class="list-group-item"><h2><?php echo $title; ?></h2></li>     
<?php
                   
        foreach($data as $item)
        {
?>
<li class="list-group-item"><a href="/myniture/<?php echo $item['url']; ?> " style="color:black" ><?php echo $item['label']; ?> </a></li>           
<?php
        }
?>
</ul>
<?php
    }
    
    
    public function RenderNavigation()
    {                    
        require('classes/navigation.class.php');
        
        //$data[] = ['label' => 'Datenschutz', 'url' => 'datenschutz.php'];
        //$data[] = ['label' => 'Impressum', 'url' => 'impressum.php'];
        //$data[] = ['label' => 'Kontakt', 'url' => 'kontakt.php'];
        $data[] = ['label' => 'Neuen Artikel anlegen', 'url' => 'alv.php/art/neu/anlegen'];       
        
        if(isset($_SESSION['login']) && $_SESSION['login']){
            $data[] = ['label' => 'Abmelden', 'url' => 'abmelden.php'];
        }
        else{
            $data[] = ['label' => 'Abmelden', 'url' => 'anmelden.php'];
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
<li class="list-group-item"><a href="/myniture/<?php echo $item['url']; ?> " style="color:black" ><?php echo $item['label']; ?> </a></li>           
<?php
        }
?>
</ul>
<?php
    }
        
    private function RenderCategories()
    {        
        //require('classes/database/categories.class.php');
        
        $categories = new Categories($this->mysqli_);
        $data = $categories->GetData();
        $title = $categories->GetTitle();
        
        $articles = new Articles($this->mysqli_);
       
?>
<ul class="list-group" style="padding-bottom: 2%;">
                <li class="list-group-item"><h2><?php echo $title; ?></h2></li>
<?php

        foreach($data as $item)
        {    

?><li class="list-group-item"><a href="/myniture/alv.php/kat/<?php echo $item['category_slug'] ?> " style="color:black" ><?php echo $item['category_name'].' ('.$articles->CountRecordsByCategory($item['category_slug']).')'; ?></a></li><?php
        }
?>            </ul>
<?php
    }
    
    public function ArticlesDataByDate($matches = null)
    {
        ///////////////////////////////////////////////////////////////////
        //                        
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
            $article_id =  $item['article_id'];
            ?>
            <li class="list-group-item"><p>Artikel Nr.: <?php echo $article_id;
                if($item['article_state'] === 'verkauft'){
                    ?><span style="color: #FF0000; margin-left: 10%;"><strong>verkauft!</strong></span></p>
<?php
                }
                else{
                    echo '</p>';
                }
?>                    
                <h3><a href="/myniture/alv.php/art/<?php echo $item['article_slug'] ?> " style="color:black" > <?php echo $item['article_name']; ?></a></h3>
                <p><img src="/myniture/thumb/<?php echo $article_id; ?>.jpg" width="150"></p>
<?php
            if(!empty($item['article_price'])){
                ?><p>Preis: <strong><?php echo $item['article_price']; ?></strong> &euro;</p>                                        
        
<?php
                }
?>
                <form name="deleteArticle" id="deleteArticle" action="/myniture/alv.php"   method="post">
                <input id="article_id" name="article_id" type="hidden" value="<?php echo $article_id; ?>">
                <input type="hidden" name="form_name" value="deleteArticleForm"/>
                <p></i><input type="submit" name="deleteArticle" value="Artikel löschen"></p>
                <!-- <button type="submit" class="btn btn-success">
    <i class="fa fa-arrow-circle-right fa-lg"></i> Artikel löschen</button> -->
                </form>
            </li>
                <?php
        }
?>
            <div id="paging"><p>
<?php
        
        //links
        // The "back" link
        if($current_page > 1){
?><a href="/myniture/alv.php/seite/1" title="Erste Seite">&laquo;</a> <a href="/myniture/alv.php/seite/<?php echo ($current_page - 1); ?>" title="Vorherige Seite">&lsaquo;</a>
<?php
        }else{
?>          <span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>
<?php
        }
?>      Seite <?php echo $current_page; ?> von <?php echo $count_pages; ?> 
<?php
        
        // The "forward" link
        if($current_page < $count_pages){
?>          <a href="/myniture/alv.php/seite/<?php echo $current_page + 1; ?>" title="Näschste Seite">&rsaquo;</a></a> <a href="/myniture/alv.php/seite/<?php echo $count_pages; ?>" title="Letzte Seite">&raquo;</a>
<?php

        }
        else{
?>          <span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span></p></div>
<?php       
        }
         ///////////////////////////////////////////////////////////////////
        
        
    }
    
    public function ArticleDataByDetail($matches)
    {
        $article_name = $matches[0][3];
        
        //require('classes/database/articles.class.php');               
        
        $articles = new Articles($this->mysqli_);
        $data = $articles->GetByDetail($article_name);                         
                
        foreach($data as $item)
        {
            $article_id =  $item['article_id'];
?>                                             
            <li class="list-group-item"><h4>Artikel Nr.: <?php echo $article_id;?></h4></li>             
<?php            
            $form = ArticleForm::WithData($item);
            $form->RenderEdit();
?>
            
            <li class="list-group-item">
            
            <h4>Bild(er) hochladen</h4>
            <p>

            <form enctype="multipart/form-data" action="/myniture/alv.php/art/<?php echo $form->GetSlug(); ?>" method="POST">
                <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
                <input type="hidden" name="form_name" value="imageUploadForm"/>
                <p><span class="fa fa-exclamation-triangle" style="color: red;"> Die maximale Beschränkung für Dateiuploads ist auf 3MB gesetzt.</span></p>
                <input type="hidden" name="article_id" value="<?php echo $article_id;?>"/>
                <!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
                
                <input type="file" name="article_images[]" multiple />
                <input type="submit" value="Hochladen" />
            </form>
            </p>
            </li><p></p>
            <li class="list-group-item">
            
            <h4>Vorschaubild hochladen</h4>
            <p>

            <form enctype="multipart/form-data" action="/myniture/alv.php/art/<?php echo $form->GetSlug(); ?>" method="POST">
                <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
                <input type="hidden" name="form_name" value="thumbUploadForm"/>
                <input type="hidden" name="article_id" value="<?php echo $item['article_id'];?>"/>
                <p><span style="color: red;"><i class="fa fa-exclamation-triangle"></i> Die maximale Beschränkung für einen Dateiupload ist auf 3MB gesetzt.</span></p>
                <!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
                <input name="imagefile" type="file" />
                <input type="submit" value="Hochladen" />
            </form>
            </p>
            </li><p></p>
<?php            
        }  
    }
    
    public function ArticlesDataByCategory($matches)
    {                                                          
        $category_slug = $matches[0][3];  
        
        ///////////////////////////////////////////////////////////////////
        //pagination
        $current_page = 1;      
        
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
            $article_id = $item['article_id'];
?>
            <li class="list-group-item"><p>Artikel Nr.: <?php echo $article_id; 
                if($item['article_state'] === 'verkauft'){
                    ?><span style="color: #FF0000; margin-left: 10%;"><strong>verkauft!</strong></span></p>
<?php
                }
                else{
                    echo '</p>';
                }                                             
?>                   
                
                <h3><a href="/myniture/alv.php/art/<?php echo $item['article_slug'] ?> " style="color:black" > <?php echo $item['article_name'] ?></a></h3>
                <p><img src="/myniture/thumb/<?php echo $article_id; ?>.jpg" width="150"></p>
<?php
                if(!empty($item['article_price'])){
                        ?><p>Preis: <strong><?php echo $item['article_price']?></strong> &euro;</p>
<?php
            }
?>
                <hr>
                <form name="deleteArticle" id="deleteArticle" action="/myniture/alv.php/kat/<?php echo $category_slug; ?>"   method="post">
                <input id="article_id" name="article_id" type="hidden" value="<?php echo $article_id; ?>">
                <input type="hidden" name="form_name" value="deleteArticleForm"/>
                <p><input type="submit" name="deleteArticle" value="Artikel löschen"></p>
                </form>
            </li>            
 <?php                                   
        }        
        ?><div id="paging"><p>
<?php        
        //links
        // The "back" link
        if($current_page > 1){
?><a href="/myniture/alv.php/kat/<?php echo $category_slug; ?>/seite/1" title="Erste Seite"><i class="fas fa-chevron-left"></i></a> <a href="/myniture/alv.php/kat/<?php echo $category_slug; ?>/seite/<?php echo ($current_page - 1); ?>" title="Vorherige Seite">&lsaquo;</a>
<?php
        }else{
?>          <span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>
<?php
        }
?>      Seite <?php echo $current_page; ?> von <?php echo $count_pages; ?> 
<?php
        
        // The "forward" link
        if($current_page < $count_pages){
?>          <a href="/myniture/alv.php/kat/<?php echo $category_slug; ?>/seite/<?php echo $current_page + 1; ?>" title="Näschste Seite">&rsaquo;</a></a> <a href="/myniture/alv.php/kat/<?php echo $category_slug; ?>/seite/<?php echo $count_pages; ?>" title="Letzte Seite">&raquo;</a>
<?php
        }
        else{
            ?><span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span></p></div>
<?php       
        }
         ///////////////////////////////////////////////////////////////////
    }
    
    public function NewArticleDataByDetail()
    {        
        
        
        $form = ArticleForm::MakeNew();
?>
    
<?php
        if(!empty($this->error_message_)){
            echo $this->error_message_;
        }

        $form->RenderNew();
        
        /*
?>
    
    <li class="list-group-item">
            
            <h4>Bild(er) hochladen</h4>
            <p>

            <form enctype="multipart/form-data" action="/myniture/alv.php/art/<?php echo $form->GetSlug(); ?>" method="POST">
                <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
                <input type="hidden" name="form_name" value="imageUploadForm"/>
                <p><span class="fa fa-exclamation-triangle" style="color: red;"> Die maximale Beschränkung für Dateiuploads ist auf 3MB gesetzt.</span></p>
                <input type="hidden" name="article_id" value="<?php echo $form->GetId();?>"/>
                <!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
                Diese Datei(en) hochladen:<br>
<?php
                for($i = 0; $i <= 10; $i++)
                {
?>                  input name="imagefile[]" type="file" /><br><?php           
                }?>               
                <input type="submit" value="Send File" />
            </form>
            </p>
            </li>
            <li class="list-group-item">
            
            <h4>Vorschaubild hochladen</h4>
            <p>

            <form enctype="multipart/form-data" action="/myniture/alv.php/art/<?php echo $form->GetSlug(); ?>" method="POST">
                <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
                <input type="hidden" name="form_name" value="thumbUploadForm"/>
                <input type="hidden" name="article_id" value="<?php echo $form->GetId();?>"/>
                <p><span style="color: red;"><i class="fa fa-exclamation-triangle"></i> Die maximale Beschränkung für einen Dateiupload ist auf 3MB gesetzt.</span></p>
                <!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
                Diese Datei hochladen: <input name="imagefile" type="file" />
                <input type="submit" value="Send File" />
            </form>
            </p>
            </li>
<?php
        */
        //$articles = new Articles($this->mysqli_);
    }
    
    
    public function Render()
    {
        require('views/header.php');
        //require('views/jumbotron.php');                
        
        ?>            
            <p style="padding-bottom: 2rem;"></p>
<div class="container">
    <div class="row">                
        <div class="col-lg-5">
            
<?php
            $this->RenderNavigation();
            
            //if(isset($_SESSION['login']) && $_SESSION['login']){
            //    $this->RenderShopAction();
            //}            
                        
            $this->RenderCategories();            
?>
        </div>
        <div class="col-lg-7">
            <ul class="list-group" style="padding-bottom: 2%;">                    
                <li class="list-group-item"><h2>Bearbeiten</h2></li>
<?php
            //$this->form_->Render();
$this->Route();     
?>
                 </ul>
        </div>
        </div>
    </div>
<?php
        //require('views/leaflet.php');
        //require('views/contactform.php');
        //require('views/footer.php');
?>
</div>
<?php
    }
}

$page = new Page();
$page->Render();