<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
    <form class="form-table" action="/myniture/alv.php/art/<?php echo $this->article_slug_; ?>" method="post">
        <li class="list-group-item">
<p>    
        <fieldset>
        <legend>Status</legend>
        <input type="hidden" name="form_name" value="updateArticleForm"/>
        <input type="hidden" name="article_id" value="<?php echo $this->article_id_; ?>"/>
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
    <p class="block">
        <label for="article_category">Kategorie: </label>
        <input type="text" name="article_category" id="article_category" size="40" maxlength="72" value="<?php echo $this->article_category_name_; ?>">            
    </p>
    <p class="block">
        <label for="article_name">Name: </label>
        <input type="text" name="article_name" id="article_name" size="40" maxlength="72" value="<?php echo $this->article_name_; ?>">            
    </p>    
    <p class="block">
        <label for="article_description">Bemerkung: </label>
        <textarea name="article_description" id="article_description" cols="40" rows="12"><?php echo $this->article_description_; ?></textarea>         
    </p>
    <p class="block">
        <label for="article_name">Preis: </label>
        <input type="text" name="article_price" id="article_price" size="16" maxlength="16" value="<?php echo $this->article_price_; ?>">            
    </p>     
    <p class="submit">
        <input type="submit" name="updateArticle"  value="Änderungen speichern">
    </p>
        </li><p></p>
     
</form>