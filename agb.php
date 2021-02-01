<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('classes/navigation.class.php');

class Page
{
    private $title_;
    
    public function __construct()
    {
        if(session_status() === PHP_SESSION_NONE){            
            session_start();
        }
        
        $this->title_ = 'AGB';
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
?>
        </div>
        <div class="col-lg-7">
            <ul class="list-group" style="padding-bottom: 2%;">  
                <li class="list-group-item">
                    <h2>Allgemeine Geschäftsbedingungen für die Nutzung der Website (antik-eicklingen.de)</h2>
                </li>
            </ul>
        </li>
        </div>
    </div>
</div>
        
<?php
    require('views/footer.php');
    }
}

$page = new Page();
$page->Render();