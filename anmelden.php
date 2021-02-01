<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require('classes/database/categories.class.php');
require('classes/database/articles.class.php');
require('classes/navigation.class.php');

final class Page
{
    private $login_form_;
    private $register_form_;
    private $config_;
    private $mysqli_;
    private $user_table_;
    private $auth_;
    
 
    
    public function __construct()
    {
        if(session_status() === PHP_SESSION_NONE){            
            session_start();
        }
        else{
            if(isset($_SESSION['login'])){
                header('location: /myniture/alv.php');
            }
        }
                            
        require('data/config.inc.php');
        require('classes/loginform.class.php');
        require('classes/registerform.class.php');
        require('classes/database/users.class.php');
        require('classes/auth.class.php');
        
        $this->config_ = new Config();
        $this->title_ = $this->config_::SITE_TITLE;
        $this->mysqli_ = new mysqli(
                                $this->config_::DB_HOST,
                                $this->config_::DB_USER,
                                $this->config_::DB_PASSWORD,
                                $this->config_::DB_DATABASE,
                                $this->config_::DB_PORT);
        
        ///////////////////////////////
        
        if($this->mysqli_->errno){
            die("Failed to connect to MySQL: " . $this->mysqli_->connect_error());
        }
        
        ///////////////////////////////               
        
        $this->mysqli_->set_charset($this->config_::DB_CHARSET);            
        $this->login_form_ = new LoginForm();
        $this->register_form_ = new RegisterForm();
        $this->user_table_ = new UsersTable($this->mysqli_);
        $this->auth_ = new Auth($this->user_table_);
        
        $this->auth_->SetUserName($this->login_form_->GetIdentity());
        $this->auth_->SetUserPassword($this->login_form_->GetCredential());
        
        if($this->auth_->Authenticate() === true){
            $_SESSION['login'] = true;

            if($this->login_form_->GetRemember()){
                $data = $this->auth_->RememberMeCookie();              
                $this->user_table_->SaveTokens($data['identity'], $data['securitytoken']);                
            }
            
            if($this->auth_->IsCustomer() === false){
                header('Location: /myniture/alv.php');
                die();
            }
            else{
                header('Location: /myniture/index.php');
                die();
            }                        
        }
        else{
            $request_method = filter_input(
                                    INPUT_SERVER,
                                    'REQUEST_METHOD',
                                    FILTER_SANITIZE_STRING);               
        
            if($request_method === 'POST'){            
                $this->login_form_->SetError('Der Benutzername oder das Passwort ist falsch. Bitte vergessen Sie auch nicht Ihr Konto zu aktivieren!');
            }
        }
    }
    
    public function __destruct()
    {
        //session_write_close();
        if($this->mysqli_){
            $this->mysqli_->close();
        }        
    }
    
    private function RenderNavigation()
    {
        //require('classes/navigation.class.php');
        
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
    
    private function RenderCategories()
    {                        
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
    
    
    private function RenderRegisterForm()
    {
        $title = 'Registrieren';
?>          
        <ul class="list-group" style="padding-bottom: 2%;">
            <li class="list-group-item"><h2><?php echo $title; ?></h2></li>
            <li class="list-group-item">
            <!-- Save -->
            <?php 
            if($this->register_form_->HasPost()){
                //$hash = md5(rand(0,1000));
                //$random_number = rand(1000,5000);
                //$random_number = substr(md5(mt_rand()),0,15);
                $user_activation_hash = sha1(uniqid(mt_rand(), true)); 
                $email = $this->register_form_->GetEmail();                                                
                $to      = $email; // Send email to our user  
                $subject = 'Bestätigung Ihres Kontos'; // Give the email a subject  
                $from="zentrale@antik-eicklingen.de";
                $headers = 'From: info@antik-eicklingen.de' . "\r\n" .
    'MIME-Version: 1.0' ."\r\n" .
    'Content-Type: text/plain; charset=\"utf-8\"' . "\r\n" .
    'Reply-To: info@antik-eicklingen.de' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
                $message = 
'Danke für die Registierung. Ihr Konto wurde erfolgreich eingerichtet.' . "\n" . 'Um die Registrierung abzuschließen, klicken Sie auf den Link, der an die von Ihnen bereitgestellte E-Mailadresse:' ."\n" .'http://www.antik-eicklingen.de/myniture/konto-bestaetigung.php?email='.$email.'&code='.$user_activation_hash;
                
                /*
                 * 
                 * -f is a parameter to the mailer (usually sendmail). 
                 * Sets the name of the ``from'' person (i.e., the sender of the
                 mail).  -f can only be used by ``trusted'' users (normally
                 root, daemon, and network) or if the person you are trying to
                 become is the same as the person you are.
                 */                
                
                $street_name = $this->register_form_->GetStreetName();    
                $street_number = $this->register_form_->GetStreetNumber();
                $forename = $this->register_form_->GetForename();
                $lastname = $this->register_form_->GetLastname();
                $phone = $this->register_form_->GetPhoneNumber();
                $credential = $this->register_form_->GetCredential();                
                $hashed_password = password_hash($credential, PASSWORD_BCRYPT);                                
                $sql = "SELECT user_email FROM users WHERE user_email = '".$email."'";                
                $result = $this->mysqli_->query($sql);
                
                if($result === false){
                    $this->register_form_->SetError('Ein interner Fehler ist aufgetreten!');
                }
                else{
                    if($result->num_rows > 0){
                        $this->register_form_->SetError('Diese E-Mail-Adresse ist leider schon vergeben');
                    }
                    else{
                        $sql = "INSERT INTO users "
                        . "(user_password, user_email, user_type, user_activated, user_registered, user_activation_hash) "
                        . "VALUES ('".$hashed_password."', '".$email."', '2', 0, '".date("Y-m-d H:i:s")."', '". $user_activation_hash ."')";
                                
                        $result = $this->mysqli_->query($sql);
                        
                        if($result === true){
                            if(mail($to, $subject, $message,  $headers, "-f info@antik-eicklingen.de") !== false){
                                $this->register_form_->SetMessage('Die E-Mail mit dem Bestätigungslink wurde gesendet. Es ist möglich, dass die E-Mail als Spam eingestuft worden ist und deshalb automatisch in Ihren Spam-Ordner verschoben wurde.');
                            }                        
                        }
                        else{
                            $this->register_form_->SetError('Ein interner Fehler ist aufgetreten (Benutzer konnte nicht eingefügt werden)!');
                        }
                    }
                }                                                                                                                                                 
            }
            
            $this->register_form_->Render(); ?>
            </li>
        </ul>
<?php
    }
    
    private function RenderLoginForm()
    {
        //$this->auth_->SetUserName($this->form_->GetIdentity());
        //$this->auth_->SetUserPassword($this->form_->GetCredential());
        
        if($this->auth_->Authenticate() === false){ 
            
                    $title = 'Anmelden';
?>
    <ul class="list-group" style="padding-bottom: 2%;">
        <li class="list-group-item"><h2><?php echo $title; ?></h2></li>
        <li class="list-group-item">
<?php
            //if(!empty($this->form_->GetIdentity())){
            //    $this->form_->SetError('Passwort oder Benutzername ist falsch!');
            //}
            
            $this->login_form_->Render();                        
            ?>
        </li>                                
    </ul>
<?php
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
            $this->RenderNavigation();
            $this->RenderCategories();
?>
        </div>
        <div class="col-lg-7">
<?php
            $this->RenderLoginForm();
            $this->RenderRegisterForm();
?>
        </div>                              
    </div>
<?php
        require('views/leaflet.php');
        //require('views/contactform.php');
        require('views/footer.php');
?>
</div>
<?php
    }
}

$page = new Page();
$page->Render();