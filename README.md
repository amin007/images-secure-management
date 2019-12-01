# images-secure-management
how to prevent hackers from entering our website

##
* [Refer this post](https://stackoverflow.com/questions/38509334/full-secure-image-upload-script)
* [Old version](https://github.com/bjorno43/ImageSecure)

# what should we do?
* Disable PHP from running inside the upload folder using .httaccess.
* Do not allow upload if the file name contains string "php".
* Allow only extensions: jpg,jpeg,gif and png.
* Allow only image file type.
* Disallow image with two file type.
* Change the image name. Upload to a sub-directory not root directory.

## Also:
* Re-process the image using GD (or Imagick) and save the processed image. All others are just fun boring for hackers.
* As rr pointed out, use move_uploaded_file() for any upload.
* By the way, you'd want to be very restrictive about your upload folder.
Those places are one of the dark corners where many exploits happen.
This is valid for any type of upload and any programming language/server.
Check https://www.owasp.org/index.php/Unrestricted_File_Upload
* Level 1: Check the extension (extension file ends with)
* Level 2: Check the MIME type ($file_info = getimagesize($_FILES['image_file']; $file_mime = $file_info['mime'];)
* Level 3: Read first 100 bytes and check if they have any bytes in the following range: ASCII 0-8, 12-31 (decimal).
* Level 4: Check for magic numbers in the header (first 10-20 bytes of the file). You can find some of the files header bytes from here:
http://en.wikipedia.org/wiki/Magic_number_%28programming%29#Examples
* You might want to run "is_uploaded_file" on the $_FILES['my_files']['tmp_name'] as well. See
http://php.net/manual/en/function.is-uploaded-file.php

___
# step by step please
## HTML form:
```html
<form name="upload" action="upload.php" method="POST" enctype="multipart/form-data">
	<br>Select image to upload: <input type="file" name="image">
	<br><input type="submit" name="upload" value="upload">
</form>
```
## PHP file:
```php
<?php
$uploaddir = 'uploads/';

$uploadfile = $uploaddir . basename($_FILES['image']['name']);

if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile))
{
	echo "Image succesfully uploaded.";
}
else
{
	echo "Image uploading failed.";
}
?>
```
___
# First problem: File types
Attackers don't have to use the form on your website to upload files to your server. POST requests can be intercepted in a number of ways. Think about browser addons, proxies, Perl scripts. No matter how hard we try, we can't prevent an attacker from trying to upload something (s)he isn't supposed to. So all of our security has to be done serverside.

The first problem is file types. In the script above an attacker could upload anything (s)he wants, like a php script for example, and follow a direct link to execute it. So to prevent this, we implement Content-type verification:

```php
<?php
if($_FILES['image']['type'] != "image/png")
{
	echo "Only PNG images are allowed!";
	exit;
}

$uploaddir = 'uploads/';
$uploadfile = $uploaddir . basename($_FILES['image']['name']);

if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile))
{
	echo "Image succesfully uploaded.";
}
else
{
	echo "Image uploading failed.";
}
?>
```
___
Unfortunately this isn't enough. As I mentioned before, the attacker has full control over the request. Nothing will prevent him/her from modifying the request headers and simply change the Content type to "image/png". So instead of just relying on the Content-type header, it would be better to also validate the content of the uploaded file. Here's where the php GD library comes in handy. Using getimagesize(), we'll be processing the image with the GD library. If it isn't an image, this will fail and therefor the entire upload will fail:

```php
<?php
$verifyimg = getimagesize($_FILES['image']['tmp_name']);

if($verifyimg['mime'] != 'image/png')
{
	echo "Only PNG images are allowed!";
	exit;
}

$uploaddir = 'uploads/';
$uploadfile = $uploaddir . basename($_FILES['image']['name']);

if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile))
{
	echo "Image succesfully uploaded.";
}
else
{
	echo "Image uploading failed.";
}
?>
```
___

We're still not there yet though. Most image file types allow text comments added to them. Again, nothing prevents the attacker from adding some php code as a comment. The GD library will evaluate this as a perfectly valid image. The PHP interpreter would completely ignore the image and run the php code in the comment. It's true that it depends on the php configuration which file extensions are processed by the php interpreter and which not, but since there are many developers out there that have no control over this configuration due to the use of a VPS, we can't assume the php interpreter won't process the image. This is why adding a file extension white list isn't safe enough either.

The solution to this would be to store the images in a location where an attacker can't access the file directly. This could be outside of the document root or in a directory protected by a .htaccess file:

```
order deny,allow
deny from all
allow from 127.0.0.1
```
Edit: After talking with some other PHP programmers, I highly suggest using a folder outside of the document root, because htaccess isn't always reliable.

We still need the user or any other visitor to be able to view the image though. So we'll use php to retrieve the image for them:
```php
<?php
$uploaddir = 'uploads/';
$name = $_GET['name'];# Assuming the file name is in the URL for this example
readfile($uploaddir.$name);
?>
```
___
# Second problem: Local file inclusion attacks
Although our script is reasonably secure by now, we can't assume the server doesn't suffer from other vulnerabilities. A common security vulnerability is known as Local file inclusion. To explain this I need to add an example code:

```php
<?php
if(isset($_COOKIE['lang']))
{
	$lang = $_COOKIE['lang'];
}
elseif(isset($_GET['lang']))
{
	$lang = $_GET['lang'];
}
else
{
	$lang = 'english';
}

include("language/$lang.php");
?>
```

In this example we're talking about a multi language website. The sites language is not something considered to be "high risk" information. We try to get the visitors preferred language through a cookie or a GET request and include the required file based on it. Now consider what will happen when the attacker enters the following url:

www.example.com/index.php?lang=../uploads/my_evil_image.jpg

PHP will include the file uploaded by the attacker bypassing the fact that (s)he can't access the file directly and we're back at square one.

The solution to this problem is to make sure the user doesn't know the filename on the server. Instead, we'll change the file name and even the extension using a database to keep track of it:

```sql
CREATE TABLE `uploads` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`original_name` VARCHAR(64) NOT NULL,
	`mime_type` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
```

```php
<?php
if(!empty($_POST['upload']) && !empty($_FILES['image']) && $_FILES['image']['error'] == 0))
{
	$uploaddir = 'uploads/';
	# Generates random filename and extension
	function tempnam_sfx($path, $suffix)
	{
		do {
			$file = $path."/".mt_rand().$suffix;
			$fp = @fopen($file, 'x');
		} while(!$fp);

		fclose($fp);
		return $file;
	}

	# Process image with GD library
	$verifyimg = getimagesize($_FILES['image']['tmp_name']);

	# Make sure the MIME type is an image
	$pattern = "#^(image/)[^\s\n<]+$#i";

	if(!preg_match($pattern, $verifyimg['mime'])
	{
		die("Only image files are allowed!");
	}

	# Rename both the image and the extension
	$uploadfile = tempnam_sfx($uploaddir, ".tmp");

	# Upload the file to a secure directory with the new name and extension
	if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile))
	{

		# Setup a database connection with PDO
		$dbhost = "localhost";
		$dbuser = "";
		$dbpass = "";
		$dbname = "";

		# Set DSN
		$dsn = 'mysql:host='.$dbhost.';dbname='.$dbname;

		# Set options
		$options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		try {
			$db = new PDO($dsn, $dbuser, $dbpass, $options);
		}
		catch(PDOException $e)
		{
			die("Error!:" . $e->getMessage());
		}

		# Setup query
		$query = 'INSERT INTO uploads (name, original_name, mime_type) VALUES (:name, :oriname, :mime)';

		# Prepare query
		$db->prepare($query);

		# Bind parameters
		$db->bindParam(':name', basename($uploadfile));
		$db->bindParam(':oriname', basename($_FILES['image']['name']));
		$db->bindParam(':mime', $_FILES['image']['type']);

		# Execute query
		try {
		$db->execute();
		}
		catch(PDOException $e)
		{
			# Remove the uploaded file
			unlink($uploadfile);
			die("Error!: " . $e->getMessage());
		}
	}
	else
	{
		die("Image upload failed!");
	}
}
?>
```
___

# So now we've done the following:

* We've created a secure place to save the images
* We've processed the image with the GD library
* We've checked the image MIME type
* We've renamed the file name and changed the extension
* We've saved both the new and original filename in our database
* We've also saved the MIME type in our database
