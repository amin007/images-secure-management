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
        //echo '<br>image.php?'. $id;
        echo '<br>';
		$k0 = URL . '?imageId=' . $id;
		//pautanTD('_blank',$k0,null,$id,null);
		picture('_blank',$k0,null,$id,null);
    }
	echo '<br><a href="index.php">Back To Index</a>';
}

# for function only
#--------------------------------------------------------------------------------------------------
function pautanTD($target, $href, $class, $data, $iconFA)
{
	$t = ($target == null) ? '':' target="' . $target . '"';
	$data = ($data == '0' or $data == null) ? '&nbsp;':$data;
	$iconFA = ($iconFA == null) ? '':$iconFA;
	?><a<?php echo $t ?> href="<?php echo $href ?>" class="<?php
	echo $class ?>"><?php echo $data ?></a>|<?php
}
#--------------------------------------------------------------------------------------------------
function picture($target, $href, $class, $data, $iconFA)
{
	$t = ($target == null) ? '':' target="' . $target . '"';
	$data = ($data == '0' or $data == null) ? '&nbsp;':$data;
	$iconFA = ($iconFA == null) ? '':$iconFA;
	?><a<?php echo $t ?> href="<?php echo $href ?>" class="<?php
	echo $class ?>"><img height="50%" width="50%" src="<?php echo $href ?>"><?php
	echo 'data = ' . $data ?></a>|<?php
}
#--------------------------------------------------------------------------------------------------
