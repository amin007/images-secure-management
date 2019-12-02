<?php
#include class
require 'i-tatarajah.php';
require 'class/DB_PDO.php';
require 'class/DB_Sql.php';
require 'class/File_Upload.php';

# for list Image File in database
#--------------------------------------------------------------------------------------------------
if(isset($_GET['imageId'])):
	$imageID = (int)$_GET['imageId'];# check if int
	showOneImage($imageID);
elseif(isset($_GET['downloadId'])):
	$imageID = (int)$_GET['downloadId'];# check if int
	downloadImage($imageID);
elseif(isset($_GET['delId'])):
	echo 'Are you sure to delete this file?<br>';
	$imageID = (int)$_GET['delId'];# check if int
	deleteImage($imageID);
//elseif():
else:
	# for upload file
	$howMany = 2; # choose how much
	viewUploadFile($howMany);

	# for list Image File in database
	listAllImage();
endif;
#--------------------------------------------------------------------------------------------------

# for function only
#--------------------------------------------------------------------------------------------------
function viewUploadFile($no = 1)
{
	?>
<form name="upload" action="upload.php" method="POST" enctype="multipart/form-data">
<?php for($count = 0; $count < $no; $count++):?>
<br>Select image to upload: <input type="file" name="image[]" multiple>
<?php endfor; ?>
<br><input type="submit" name="upload" value="upload">
</form>
	
	<?php
	#
}
#--------------------------------------------------------------------------------------------------
function showTable($tajukjadual,$row)
{
	echo "\n" . '<table border="1" class="excel" id="example">';
	echo "\n" . '<h3>'. $tajukjadual . '</h3>';
	$printed_headers = false; # mula bina jadual
	#-----------------------------------------------------------------
	for ($kira=0; $kira < count($row); $kira++)
	{
		if ( !$printed_headers ) # papar tajuk medan sekali sahaja:
		{
			echo "\n" . '<thead><tr><th>#</th>';
			foreach ( array_keys($row[$kira]) as $tajuk )
			{
				echo "\n" . '<th>' . $tajuk . '</th>';
			}
			echo "\n" . '</tr></thead>';
			$printed_headers = true;
		}
	# papar data $row ------------------------------------------------
	echo "\n" . '<tr><td align="center">'. ($kira+1) . '</td>';
		foreach ( $row[$kira] as $key=>$data )
		{
			//gaya_url_0($key,$data);
			gaya_url_1($key,$data);
		}
		echo '</tr>' . "\n";
	}#-----------------------------------------------------------------
	echo "\n" . '</table>';
	#
}
#--------------------------------------------------------------------------------------------------
function gaya_url_0($key,$data)
{
	echo "\n<td>$data</td>";
}
#--------------------------------------------------------------------------------------------------
function gaya_url_1($key,$data)
{
	$k0 = URL . '?imageId=' . $data;
	$k1 = URL . '?downloadId=' . $data;
	$k2 = URL . '?delId=' . $data;

	if ($data == null):
		echo "\n<td>&nbsp;</td>";
	elseif($key == 'id'):
		?><td><?php
		pautanTD('_blank',$k0,null,$data,null);
		pautanTD('_blank',$k1,null,'Download',null);
		pautanTD('_blank',$k2,null,'Delete',null);
		?></td><?php
	else: 
		echo "\n<td>$data</td>";
	endif;
}
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
function listAllImage()
{
	$sql = new \Aplikasi\Kitab\DB_Sql;
	list($result['images'],$meta) = $sql->listImage();
	if(!empty($result))
	{
		//echo '<pre>'; print_r($result); echo '</pre>';
		echo 'Your images can be viewed here:<br>';
		foreach ($result as $myTable => $row)
		{
			if ( count($row)==0 ) echo '';
			else
			{
				showTable($myTable,$row);
			}# if ( count($row)==0 )
		}# endforeach//*/
	}
	else
	{
		echo 'Tiada data';
	}
	#
}
#--------------------------------------------------------------------------------------------------
function showOneImage($imageID)
{
	$sql = new \Aplikasi\Kitab\DB_Sql;
	list($data,$meta) = $sql->findImage($imageID);
	if(!empty($data))
	{
		//echo '<pre>'; print_r($data); echo '</pre>';
		$newFile = $data[0]['original_name'];
		$name = $data[0]['name'];
		$typeFile = $data[0]['mime_type'];

		# Send headers and file to visitor for display
		header('Content-Type: ' . $typeFile);
		readfile(F_PATH . '/' . $name);
	}
	else
	{
		echo 'Tiada data';
	}
	#
}
#--------------------------------------------------------------------------------------------------
function downloadImage($imageID)
{
	$sql = new \Aplikasi\Kitab\DB_Sql;
	list($data,$meta) = $sql->findImage($imageID);
	if(!empty($data))
	{
		//echo '<pre>'; print_r($data); echo '</pre>';
		$newFile = $data[0]['original_name'];
		$name = $data[0]['name'];
		$typeFile = $data[0]['mime_type'];

		# Send headers and file to visitor for download
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=' . basename($newFile) );
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize(F_PATH . '/' . $name) );
		header('Content-Type: ' . $typeFile);
		readfile(F_PATH . '/' . $name);
	}
	else
	{
		echo 'Tiada data';
	}
	#
}
#--------------------------------------------------------------------------------------------------
function deleteImage($imageID)
{
	$sql = new \Aplikasi\Kitab\DB_Sql;
	list($data,$meta) = $sql->findImage($imageID);
	if(!empty($data))
	{
		//echo '<pre>'; print_r($data); echo '</pre>';
		$newFile = $data[0]['original_name'];
		$name = $data[0]['name'];
		$typeFile = $data[0]['mime_type'];
		//unlink(F_PATH . '/' . $name);
		# delete sql
		//list($data,$meta) = $sql->deleteImage($imageID);
		#
		$info = 'File: ' . $newFile . ' succesfully deleted.';
		echo $info;
	}
	else
	{
		echo 'Tiada data';
	}
	#
}
#--------------------------------------------------------------------------------------------------