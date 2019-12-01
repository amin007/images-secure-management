<?php
namespace Aplikasi\Kitab;//echo __NAMESPACE__;
class File_Upload
{
#==================================================================================================
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
		$files = $this->reArrayFiles($files);
	}
#--------------------------------------------------------------------------------------------------
#==================================================================================================
}
