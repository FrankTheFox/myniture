<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('classes/database/articles.class.php');
require('classes/navigation.class.php');
require('classes/database/categories.class.php');

class Page
{
    private $config_;
    private $mysqli_;
    
    public function __construct()
    {        
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
    }
    
    public function __destruct()
    {
        $this->mysqli_->close();
    }
    
    public function RenderNavigation()
    {                        
        $data[] = ['label' => 'Datenschutz', 'url' => 'datenschutz.php'];
        $data[] = ['label' => 'Impressum', 'url' => 'impressum.php'];
        $data[] = ['label' => 'Kontakt', 'url' => 'kontakt.php'];
        $data[] = ['label' => 'Anmelden', 'url' => 'anmelden.php'];        
        
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
    
    
    public function RenderCategories()
    {        
        $data = [];
        
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

?><li class="list-group-item"><a href="/myniture/index.php/kat/<?php echo $item['category_slug'] ?> " style="color:black" ><?php echo $item['category_name'].' ('.$articles->CountRecordsByCategory($item['category_slug']).')'; ?> </a></li><?php
        }
?>            </ul>
<?php
    }
    
    
    public function RenderPrivacy()
    {
        $title = 'Datenschutzerklärung nach der DSGVO';
?>
    <ul class="list-group" style="padding-bottom: 2%;">
        <li class="list-group-item"><h2><?php echo $title; ?></h2></li>
        <li class="list-group-item">
<?php
            require('views/privacy.php');
?>
        </li>                                
    </ul>
<?php
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
            $this->RenderNavigation();
            $this->RenderCategories();
?>
        </div>
        <div class="col-lg-7">
<?php
            $this->RenderPrivacy();
?>
        </div>
    </div>
<?php
        require('views/leaflet.php');
        //require('views/contactform.view.php');
        require('views/footer.php');
?>
</div>
<?php
    }
}

$page = new Page();
$page->Render();