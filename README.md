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
