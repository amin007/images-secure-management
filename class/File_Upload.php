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
		if($this->createTable())
		{# Checks if a htaccess file should be created and creates one if needed

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
#--------------------------------------------------------------------------------------------------
#==================================================================================================
}
