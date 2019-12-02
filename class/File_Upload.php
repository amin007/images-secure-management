<?php
namespace Aplikasi\Kitab;//echo __NAMESPACE__;
class File_Upload
{
#==================================================================================================
#--------------------------------------------------------------------------------------------------
	# set private variables in this class
	private $error = array();
	private $info = array();
	private $ids = array();
	private $obj;

	private $mtype;

	private $folder = F_PATH;
	private $htaccess = H_FILE;
	private $f_size = F_SIZE;
#--------------------------------------------------------------------------------------------------
	# Checks if required PHP extensions are loaded. Tries to load them if not
	private function check_phpExt()
	{
		if (!extension_loaded('fileinfo'))
		{
			# dl() is disabled in the PHP-FPM since php7 so we check if it's available first
			if(function_exists('dl'))
			{
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
				{
					return (!dl('fileinfo.dll')) ? false:true;
					//if (!dl('fileinfo.dll')) { return false; }
					//else { return true; }
				}
				else
				{
					return (!dl('fileinfo.so')) ? false:true;
					//if (!dl('fileinfo.so')) { return false; }
					//else { return true; }
				}
			}
			else { return false; }
		}
		else { return true; }
		#
	}
#--------------------------------------------------------------------------------------------------
	# Handles the uploading of images
	public function uploadImages($files)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		# Checks if the required PHP extension(s) are loaded
		if($this->check_phpExt())# check function line 7
		{
			//echo '<br> lepas ujian check_phpExt() . ';
			# Checks if db table exists. Creates it if nessesary
			$this->createNewTable($files);
		}
		else
		{
			array_push($this->error, "The PHP fileinfo extension isn't loaded and "
			. "ImageUpload was unable to load it for you.");
			$this->obj->error = $this->error;
			return $this->obj;
		}
		# endif $this->check_phpExt()
	}
#--------------------------------------------------------------------------------------------------
	# Checks if db table exists. Creates it if nessesary
	public function createNewTable($files)
	{
		//echo '<hr>Name class :' . __METHOD__ . '()<hr>';
		if($this->createTable())# refer function line 80
		{# Checks if a htaccess file should be created and creates one if needed
			$this->createNewHtaccess($files);
		}
		else
		{
			//echo '<br>2.2 gagal createTable daa ';
			if($this->error !== NULL){
				$this->obj->error = $this->error;
				return $this->obj;
			} else {
				// This should never happen, but it's here just in case
				array_push($this->error, "Unknown error! Failed to load ImageUpload class!");
				$this->obj->error = $this->error;
				return $this->obj;
			}
		}
		# endif $this->createTable()
	}
#--------------------------------------------------------------------------------------------------
	# Checks if the table already exists. If not, creates one
	private function createTable()
	{
		//echo '<hr>Nama class :' . __METHOD__ . '<hr>';
		# Check if table already exists
		$this->stmt = $this->dbh->prepare("SHOW TABLES LIKE '" .  DB_TABLE . "'");

		try{ $this->stmt->execute(); }
		catch(PDOException $e)
		{
			array_push($this->error, $e->getMessage());
			return false;
		}

		$cnt = $this->stmt->rowCount();

		if($cnt > 0) { return true; }
		else
		{
			# Create table
			$this->stmt = $this->dbh->prepare("
				CREATE TABLE `". DB_TABLE ."` (
					`id` INT(11) NOT NULL AUTO_INCREMENT,
					`name` VARCHAR(64) NOT NULL,
					`original_name` VARCHAR(64) NOT NULL,
					`mime_type` VARCHAR(20) NOT NULL,
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;");
			try{
				$this->stmt->execute();
				return true;
			}
			catch(PDOException $e){
				array_push($this->error, $e->getMessage());
				return false;
			}
		}
		#
	}
#--------------------------------------------------------------------------------------------------
	# Checks if a htaccess file should be created and creates one if needed
	public function createNewHtaccess($files)
	{
		//echo '<hr>Nama class :' . __METHOD__ . '<hr>';
		if($this->htaccess)
		{
			if(!$this->createHtaccess()){
				array_push($this->error, "Unable to create htaccess file.");
				$this->obj->error = $this->error;
				return $this->obj;
			}
		}

		# Re-arranges the $_FILES array
		$files = $this->reArrayFiles($files);# refer line 137
		$this->loopArrayFiles01($files);# refer line 206
	}
#--------------------------------------------------------------------------------------------------
	# Re-arranges the $_FILES array
	private function reArrayFiles($files)
	{
		$file_ary = array();
		$file_count = count($files['name']);
		$file_keys = array_keys($files);

		for ($i=0; $i<$file_count; $i++)
		{
			foreach ($file_keys as $key)
			{
				$file_ary[$i][$key] = $files[$key][$i];
			}
		}

		return $file_ary;
	}
#--------------------------------------------------------------------------------------------------
	# Checks the true mime type of the given file
	private function check_img_mime($tmpname)
	{
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mtype = finfo_file( $finfo, $tmpname );
		$this->mtype = $mtype;
		if(strpos($mtype, 'image/') === 0){ return true; }
		else { return false; }
		finfo_close( $finfo );
	}
#--------------------------------------------------------------------------------------------------
	# Checks if the image isn't to large
	private function check_img_size($tmpname)
	{
		$size_conf = substr(F_SIZE, -1);
		$max_size = (int)substr(F_SIZE, 0, -1);

		switch($size_conf)
		{
			case 'k':
			case 'K':
				$max_size *= 1024;
				break;
			case 'm':
			case 'M':
				$max_size *= 1024;
				$max_size *= 1024;
				break;
			default:
				$max_size = 1024000;
		}

		if(filesize($tmpname) > $max_size){ return false; }
		else { return true; }
		#
	}
#--------------------------------------------------------------------------------------------------
	# Creates a file with a random name
	private function tempnam_sfx($path, $suffix)
	{
		do {
			$file = $path."/".mt_rand().$suffix;
			$fp = @fopen($file, 'x');
		}
		while(!$fp);

		fclose($fp);
		return $file;
	}
#--------------------------------------------------------------------------------------------------
	# loop array files 01
	private function loopArrayFiles01($files)# refer line 134
	{
		foreach($files as $file)
		{# Checks if $file['tmp_name'] is empty. This occurs when a file is bigger than
		# allowed by the 'post_max_size' and/or 'upload_max_filesize' settings in php.ini
			if(!empty($file['tmp_name']))
			{# Checks the true MIME type of the file
				if($this->check_img_mime($file['tmp_name'])){# refer line 156
				# Checks the size of the the image
					if($this->check_img_size($file['tmp_name'])){# refer line 167
					# Creates a file in the upload directory with a random name
						# refer line 193 for $this->tempnam_sfx()
						$uploadfile = $this->tempnam_sfx($this->folder, ".tmp");
/*
						# Moves the image to the created file
						if (move_uploaded_file($file['tmp_name'], $uploadfile))
						{
							# Inserts the file data into the db
							$this->insertDatabase($uploadfile,$file);
							continue;
						}
						else
						{
							unlink($file['tmp_name']);
							array_push($this->info, 'Unable to move file: ' . $file['name']
							. ' to target folder. The file is removed!');
						}# end move_uploaded_file()
*/
					}
					else
					{
						array_push($this->info, 'File: ' . $file['name'] . ' exceeds the maximum'
						. ' file size of: ' . F_SIZE . 'B. The file is removed!');
					}# end $this->check_img_size()
				}
				else
				{
					unlink($file['tmp_name']);
					array_push($this->info, 'File: ' . $file['name'] . ' is not an image.'
					. ' The file is removed!');
				}# end $this->check_img_mime()
			}
			else
			{
				array_push($this->info, 'File: ' . $file['name'] . ' exceeds the maximum file'
				. ' size that this server allowes to be uploaded!');
			}
		}# end # Re-arranges the $_FILES array
		#
	}
#--------------------------------------------------------------------------------------------------
	# Inserts the file data into the db
	private function insertDatabase00($uploadfile,$file)
	{
		$this->stmt = $this->dbh->prepare("INSERT INTO `" . DB_TABLE
		. "` (name, original_name, mime_type) VALUES (:name, :oriname, :mime)");

		$this->bind(':name', basename($uploadfile));
		$this->bind(':oriname', basename($file['name']));
		$this->bind(':mime', $this->mtype);

		try{ $this->stmt->execute(); }
		catch(PDOException $e){
			array_push($this->error, $e->getMessage());
			$this->obj->error = $this->error;
			return $this->obj;
		}

		array_push($this->ids, $this->dbh->lastInsertId());
		array_push($this->info, 'File: ' . $file['name'] . ' was succesfully uploaded!');
		#
	}
#--------------------------------------------------------------------------------------------------
#==================================================================================================
}
