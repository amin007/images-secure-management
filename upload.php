<?php
set_time_limit(20);
#include class
require 'i-tatarajah.php';
require 'class/DB_PDO.php';
require 'class/DB_Sql.php';
require 'class/File_Upload.php';

$img = new \Aplikasi\Kitab\File_Upload;
$result = $img->uploadImages($_FILES['image']);

//echo '<hr><pre>$result:'; print_r($result); echo '</pre><hr>';

if(!empty($result->info))
{
    foreach($result->info as $infoMsg)
	{
        echo $infoMsg . '<br>';
    }
}

if(!empty($result->ids))
{
	echo 'Your images can be viewed here:<br><br>';
    foreach($result->ids as $id)
	{
        echo '<br>image.php?'. $id;
    }
}