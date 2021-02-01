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
    private $article_description_;
    private $article_price_;    
    private $article_state_;
    private $article_category_name_;    
    private $error_messages_;
    private $imagefiles_;
    private $thumbfile_;
    private $input_post_;
    private $form_action_;    
        
    private function __construct()
    {                                
        //empty
    }
    
    public function HasPost(): bool
    {
        return $this->input_post_;                     
    }
        
    public function GetCategoryName()
    {
        return $this->article_category_name_;
    }    
    
    public function SetAction($action)
    {
        $this->form_action_ = $action;
    }

    private function SetupWithForm($form_name)
    {
        $this->input_post_ = false;
        $this->form_action_ = false;
        $this->error_messages_ = false;
        $this->imagefiles_ = false;
        $this->thumbfile_ = false;
        
        if(filter_input(
                    INPUT_POST,
                    'form_name', 
                    FILTER_SANITIZE_STRING) === $form_name)
        {                                                           
            $this->input_post_ = true;
        }
    }
    
    public static function WithNewArticle()
    {
        $instance = new self();   
        $instance->SetupWithForm('newArticleForm');
        $instance->QueryValues();        
        
        return $instance;        
    }
    
    public function SetErrorMessage($message)
    {
        $this->error_messages_[] = $message;
    }
    
    public function QueryValues()
    {
        if($this->input_post_){           
            $this->article_id_ = filter_input(
                                        INPUT_POST,
                                        'article_id', 
                                        FILTER_SANITIZE_STRING);

            $this->article_category_name_ = filter_input(
                                                INPUT_POST,
                                                'article_category', 
                                                FILTER_SANITIZE_STRING);

            $this->article_state_ = filter_input(
                                            INPUT_POST,
                                            'article_state', 
                                            FILTER_SANITIZE_STRING);

            $this->article_name_ = filter_input(
                                            INPUT_POST,
                                            'article_name', 
                                            FILTER_SANITIZE_STRING);                                

            $this->article_description_ = filter_input(
                                            INPUT_POST,
                                            'article_description', 
                                            FILTER_SANITIZE_STRING);

            $this->article_price_ = filter_input(
                                            INPUT_POST,
                                            'article_price', 
                                            FILTER_SANITIZE_STRING);
            
            $this->imagefiles_[] = $_FILES['imagefile'];                                                                        
            $this->thumbfile_[] = $_FILES['thumbfile'];   
        }
    }
        
    public function Validate()
    {
        if($this->input_post_){ 
            if(empty($this->article_id_)){
                $this->error_messages_[] = 'Keine Artikelnummer angegeben.';
            }

            if(empty($this->article_category_name_)){
                $this->error_messages_[] = 'Keine Kategorie angegeben.';
            }

            if(empty($this->article_state_)){
                $this->error_messages_[] = 'Keinen Status angegeben.';
            }

            if(empty($this->article_name_)){
                $this->error_messages_[] = 'Keinen Artikelnamen angegeben.';
            }

            if(empty($this->article_description_)){
                $this->error_messages_[] = 'Keine Beschreibung angegeben.';
            }

            if(empty($this->article_price_)){
                $this->error_messages_[] = 'Keinen Preis angegeben.';
            }        

            if (is_array($this->imagefiles_) or ($this->imagefiles_ instanceof Traversable)){
                
                $file_count = count($this->imagefiles_[0]['name']);                                
                
                for($i = 0; $i < $file_count; $i++){                                                                                         
                    if('image/jpeg' !== $this->imagefiles_[0]['type'][$i]){
                        $this->error_messages_[] = 'Folgende Bildformate für Artikelbilder sind zulässig:  JPEG, JPG.';
                        break;
                    }                                          
                }
            }
            
            if(empty($this->thumbfile_)){
                $this->error_messages_[] = 'Kein Vorschaubild hochgeladen';
            }
            else{
                if('image/jpeg' !== $this->thumbfile_[0]['type']){
                    $this->error_messages_[] = 'Folgende Bildformate für das Vorschaubild sind zulässig:  JPEG, JPG.';
                }
            }                        
        }               
    }
    
    public function GetImageFiles()
    {
        return $this->imagefiles_;
    }
    
    public function GetThumbFile()
    {
        return $this->thumbfile_;
    }
    
    public function GetValues(): array
    {
        return [
            'article_id' => $this->article_id_,
            'article_name' => $this->article_name_,
            'article_description' => $this->article_description_,
            'article_price' => $this->article_price_,
            'article_state' => $this->article_state_,
            ];                          
    }
    
    public function HasErrorMessages(): bool
    {
        if(!empty($this->error_messages_)){
            return true;
        }
        
        return false;
    }
           
    public function Render()
    {
?>
<form enctype="multipart/form-data" class="form-table" action="<?php echo $this->form_action_; ?>" method="post">
    <li class="list-group-item">
        <h4>Einen neuen Artikel anlegen</h4>
    <hr>
<?php
if($this->input_post_ === true){
        if(!empty($this->error_messages_)){
?>    
    <p><span style="color:red;"><strong>Folgende Fehler sind aufgetreten:</strong></span></p>
    <ul>
<?php
        foreach($this->error_messages_ as $message)
        {
            ?><li><span style="color:red;"><?php echo $message; ?></span></li>
<?php
        }
?>       
    </ul>
    
<?php
     }
     else{
         ?><p>Der Artikel wurde angelegt.</p><?php
     }
     ?><hr><?php
}
?>    
    <p>            
        <fieldset>
        <legend>Status</legend>
        <input type="hidden" name="form_name" value="newArticleForm"/>        
        <input type="radio" name="article_state" id="article_state_offline" value="Offline" 
                <?php if($this->article_state_ === 'offline'){ echo 'checked="checked"';} ?> required="required">
        <label for="article_state_offline"> Offline</label><br>
        <input type="radio" name="article_state" id="article_state_online" value="Online" 
               <?php if($this->article_state_ === 'online'){ echo 'checked="checked"';} ?>>
        <label for="article_state_online"> Online</label><br>
        <input type="radio" name="article_state" id="article_state_reserved" value="Reserviert"
               <?php if($this->article_state_ === 'reserviert'){ echo 'checked="checked"';} ?>>
        <label for="article_state_reserved"> Reserviert</label><br>
        <input type="radio" name="article_state" id="article_state_sold" value="Verkauft"
               <?php if($this->article_state_ === 'verkauft'){ echo 'checked="checked"';} ?>>
        <label for="article_state_sold"> Verkauft</label><br>
        </fieldset>
    </p>
    <hr>
    <p class="block">
        <label for="article_category">Artikel Nr.: *</label>
        <input type="text" name="article_id" id="article_nr" size="4" maxlength="6" value="<?php echo $this->article_id_; ?>" required="required">
    </p>    
    <p class="block">
        <label for="article_category">Kategorie: *</label>
        <input type="text" name="article_category" id="article_category" size="40" maxlength="72" value="<?php echo $this->article_category_name_; ?>" required="required">            
    </p>
    <p class="block">
        <label for="article_name">Name: *</label>
        <input type="text" name="article_name" id="article_name" size="40" maxlength="72" value="<?php echo $this->article_name_; ?>" required="required">            
    </p>   
    <p class="block">
        <label for="article_description">Bemerkung: *</label>
        <textarea name="article_description" id="article_description" cols="40" rows="12" required="required"><?php echo $this->article_description_; ?></textarea>         
    </p>
    <p class="block">
        <label for="article_name">Preis: *</label>
        <input type="text" name="article_price" id="article_price" size="16" maxlength="16" value="<?php echo $this->article_price_; ?>" required="required">            
    </p>        
    <li class="list-group-item">
            
        <h4>Bild(er) hochladen</h4>
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
            <p><span  style="color: red;"><i class="fa fa-exclamation-triangle"></i> Die maximale Beschränkung für Dateiuploads ist auf 3MB gesetzt.</span></p>
            Diese Datei(en) hochladen:<br>
            <input style="cursor: pointer;" name="imagefile[]" type="file" multiple="multiple" />  
    <li class="list-group-item">            
        <h4>Vorschaubild hochladen</h4>
        <p>          
            <!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->                                     
            <p><span style="color: red;"><i class="fa fa-exclamation-triangle"></i> Die maximale Beschränkung für einen Dateiupload ist auf 3MB gesetzt.</span></p>
            <!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
            Diese Datei hochladen: <input style="cursor: pointer;" name="thumbfile" type="file" required="required"/>                           
        </p>
    
    <p>(*) Plichtfelder</p>
    <hr>
    <p class="submit">
        <input type="submit" name="newArticle"  value="Neu anlegen">
    </p>
     </li>
</form>
<?php
    }
}