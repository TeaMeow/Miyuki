<?php 
include '../src/miyuki.php'; 



$imagePath = $_FILES['image'];


$miyuki = new Miyuki($imagePath['tmp_name']);

if(!$miyuki->checkType('jpg, png, gif'))
    exit('type');
  
if(!$miyuki->checkFilesize(300))
    exit('Filesize'); 
    
if(!$miyuki->checkSize(3000, 3000))
    exit('Size');

$miyuki->setQuality(0.3);

?>
