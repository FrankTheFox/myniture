<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>      
    <p>
        <li class="list-group-item">
<?php
    if(!empty($this->article_images_)){

        foreach($this->article_images_ as $image)
        {
                ?>        
    <p>
        <img src="/myniture/image/<?php echo $image ?>" width="150">
    </p>        
        <form name="deleteImage" id="deleteImage" action="/myniture/alv.php/art/<?php echo $this->article_slug_; ?>" method="post">
            <input type="hidden" name="form_name" value="deleteImageForm"/>
            <input type="hidden" name="article_id" value="<?php echo $this->article_id_; ?>"/>
            <input type="hidden" name="filename" value="<?php echo $image; ?>"/>
            <p>
                <input type="submit" name="deleteImage" id="article_image" value="Bild löschen">
            </p>
        </form>
<?php
        }
    }
?>
        </li>
    </p>