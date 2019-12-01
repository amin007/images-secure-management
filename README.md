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
* Re-process the image using GD (or Imagick) and save the processed image. All others are just fun boring for hackers"
* As rr pointed out, use move_uploaded_file() for any upload"
* By the way, you'd want to be very restrictive about your upload folder. Those places are one of the dark corners where many exploits
happen. This is valid for any type of upload and any programming
language/server. Check
https://www.owasp.org/index.php/Unrestricted_File_Upload
* Level 1: Check the extension (extension file ends with)
* Level 2: Check the MIME type ($file_info = getimagesize($_FILES['image_file']; $file_mime = $file_info['mime'];)
* Level 3: Read first 100 bytes and check if they have any bytes in the following range: ASCII 0-8, 12-31 (decimal).
* Level 4: Check for magic numbers in the header (first 10-20 bytes of the file). You can find some of the files header bytes from here:
http://en.wikipedia.org/wiki/Magic_number_%28programming%29#Examples
* You might want to run "is_uploaded_file" on the $_FILES['my_files']['tmp_name'] as well. See
http://php.net/manual/en/function.is-uploaded-file.php