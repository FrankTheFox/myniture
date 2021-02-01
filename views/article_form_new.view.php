<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>

<form enctype="multipart/form-data" class="form-table" action="/myniture/alv.php/art/neu/anlegen" method="post">
<li class="list-group-item">
    <h4>Einen neuen Artikel anlegen</h4>
<hr><p>            
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
        <input type="text" name="article_id" id="article_id" size="4" maxlength="6" value="<?php echo $this->article_id_; ?>" required="required">
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
        <p><span style="color: red;"><i class="fa fa-exclamation-triangle"></i> Die maximale Beschränkung für Dateiuploads ist auf 3MB gesetzt.</span></p>
        Diese Datei(en) hochladen:<br>
        <input style="cursor: pointer;" name="imagefile[]" type="file" multiple="multiple" />
    </li>
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