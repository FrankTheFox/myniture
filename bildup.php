<?php 

################################################################
$host   = $_SERVER["HTTP_HOST"];
$currentPage = $_SERVER["PHP_SELF"];
$self = basename($currentPage); #Dateiname
$fs_Page     = $_SERVER["SCRIPT_FILENAME"];
$fs_Page     = $_SERVER["SCRIPT_FILENAME"];
//$currentdir  = eregi_replace($self, "", $fs_Page);         # Pfad zum Ordner
$currentdir  = preg_replace('#'.$self.'#', "", $fs_Page);         # Pfad zum Ordner
$back = basename($currentdir);       # Name des Ordners ( f&uuml;r zur&uuml;ck)
################################################################

if(!isset ($deep)){
	$deep='../';
	$id = $_GET['id'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Bildup</title>
	
	<link rel="STYLESHEET" type="text/css" href="../stile_t.css">
</head>

<body  background="<?php  echo $deep; ?>pics/bg3.jpg" bgcolor="#FFDD81" text="#330000">
<?php 
}

#$uploaddir = '/srv/www/htdocs/web251/html/LUTZ/galerie/'; 
$bildpfad ='stockliste';
$updir =  '../stockliste';   
#
$galerie = basename ($updir);
#$uploadfile = $uploaddir.basename($_FILES['picup']['name']);
#$bild=eregi_replace('.jpg', '', $_FILES['picup']['name']);

$w=800;
$nr=1;
if(isset($_POST['nr'])) $nr  = $_POST['nr'];

if (isset ($_POST['tnx'])){        ####################### tn - exchange
	$tmpname = $updir.'/image/'.$id.'-'.$_POST['tnx'].'.jpg';
	echo'neues Galeriebild: '.$_POST['tnx'];
	echo'<p class="arial10">für ein neues den entsprechenden Radiobutton aktivieren</p>';
	img_resize( $tmpname , 150, $updir.'/thumb/' ,$id.'.jpg');
}elseif (isset($_FILES['picup'])){
	#$ctf = stream_context_set_default();
    $tmpname  = $_FILES['picup']['tmp_name'];
    #@img_resize( $tmpname , 600 , '../galerie/image' , 'galerie_'.$id.'.jpg');
	#img_resize( $tmpname , 450 , '../galerie/image' , $_FILES['picup']['name']);
	if($tmpname != ''){
		img_resize( $tmpname , $w , $updir.'/image' ,$id.'-'.$nr.'.jpg'); 
		if(isset($_POST['thumb'])) 
		img_resize( $tmpname , 200, $updir.'/thumb' ,$id.'.jpg');
	}
	
    #@img_resize( $tmpname , 60 , '../galerie' , 'galerie_'.$id.'_maxheight.jpg', 1);
#file_put_contents ( $updir.'/'.$bild.'.txt' , $text , FILE_TEXT, $ctf );		
#echo	$updir.'/'.$bild.'.txt <br>';
}else
    echo "Kein Bild hochgeladen";


function img_resize( $tmpname, $size, $save_dir, $save_name, $maxisheight = 0 )
    {
    $save_dir     .= ( substr($save_dir,-1) != "/") ? "/" : "";
    $gis        = getimagesize($tmpname);
    $type        = $gis[2];
    switch($type)
        {
        case "1": $imorig = imagecreatefromgif($tmpname); break;
        case "2": $imorig = imagecreatefromjpeg($tmpname);break;
        case "3": $imorig = imagecreatefrompng($tmpname); break;
        default:  $imorig = imagecreatefromjpeg($tmpname);
        }

        $x = imagesx($imorig);
        $y = imagesy($imorig);
        
        $woh = (!$maxisheight)? $gis[0] : $gis[1] ;    
        
        if($woh <= $size)
        {
        $aw = $x;
        $ah = $y;
        }
            else
        {
            if(!$maxisheight){
                $aw = $size;
                $ah = $size * $y / $x;
            } else {
                $aw = $size * $x / $y;
                $ah = $size;
            }
        }   
        $im = imagecreatetruecolor($aw,$ah);
    if (imagecopyresampled($im,$imorig , 0,0,0,0,$aw,$ah,$x,$y))
        if (imagejpeg($im, $save_dir.$save_name))
            return true;
            else
            return false;
			

    }

?>
<form enctype="multipart/form-data" action="<?php  echo $self; ?>?do=Bild&amp;id=<?php  echo $id; ?>" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="2100000">
<table class="arial12" width="920" border="0" cellspacing="2" cellpadding="5">
 <tr bgcolor="#808080">
    <td align="right" width="70" valign="top" >
	<p class="klein">aktuelles Galeriebild</p> 
	<?php 
echo'<img src="'.$updir.'/thumb/'.$id.'.jpg" width="75" hspace="3" vspace="3" alt="Titelbild '.$id.'" border="0" >';
	?>

	</td>
 <td width="*" align="left" valign="top">
 		
 <?php 
$checked= ' ';
for ($i=1; $i<=18; $i++){ //for ($i=1; $i<=11; $i++)
        #if ($i == $bild)continue;
        if (!file_exists($updir.'/image/'.$id.'-'.$i.'.jpg'))continue;
  echo'<input type="radio" name="tnx" value="'.$i.'">&nbsp;<img src="'.$updir.'/image/'.$id.'-'.$i.'.jpg" height="100" vspace="3" alt="'.$id.'-'.$i.'.jpg" border="0" ><a href="bildloesch.php?do=Bildwech&id='.$id.'&nr='.$i.'">&nbsp;<span class="false">x</span></a>  &nbsp;&nbsp;&nbsp; '; ######### Bild löschen = bildwech
$nr= $i+1;
}
if (!file_exists($updir.'/image/'.$id.'-1.jpg')){ 
	$nr=1;
	echo '<p><span class="false">Sie m&uuml;ssen ein Bild als Nr 1 hochladen!</span></p>';
	$checked= ' checked ';
}
?></td>
</tr> <tr>

    <td align="right" width="100" >
		Bild <?php  echo $id.'-'.$nr?> <br>
		<!-- <p class="klein">Max 2100k</p>  -->
	</td>
    <td align="left" >
		  <input name="picup" type="file">
		  <input type="hidden" name="nr" value="<?php  echo $nr?>">
    </td>
</tr>
<tr><td colspan="2"><img src="../pics/leer.gif" width="600" height="2" alt="" border="0"></td></tr>
 <tr>
    <td align="right" width="100" >
	Galeriebild
	<p class="klein">&Uuml;berschreibt aktuelles Galeriebild</p> 	
	</td>
    <td align="left" valign="top" >
		<input type="checkbox" name="thumb" value="1"<?php  echo $checked; ?>> <img src="../pics/leer.gif" alt="" border="0" width="110" height="10">
	 		<input type="submit" value="Hochladen">
    </td>
</tr></table>
</form>

Zum Artikel: <a target="_blank" href="<?php  echo $updir; ?>/stockliste.php?detail=<?php  echo $id; ?>"> <?php  if(isset($name)) {echo $name;} ?> bild=<?php  echo $nr; ?></a>
<p class="klein">Html: benutzen Sie STRG+C zum Kopieren</p><br>
<?php 
for ($i=1; $i<=18; $i++){ //for ($i=1; $i<=11; $i++)
        #if ($i == $bild)continue;
        if (!file_exists($updir.'/image/'.$id.'-'.$i.'.jpg'))continue;
# echo'<input type="text" width="90" name="'.$i.'" value="&lt;img src=&quot;'.$host.'/'.$bildpfad.'/'.$id.'.jpg' &quot; border=0 /&gt;"> <br>';
  echo '<p class="klein">&lt;img src="http://'.$host.'/'.$bildpfad.'/image/'.$id.'-'.$i.'.jpg"';
  echo' border=0 /&gt;</p><p>&nbsp;</p>';
}


?>

</body>
</html>
