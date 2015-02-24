<html>
<head>
<title> SimpleUpload Class Example </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<head>
<body>
<h1> SimpleUpload Class Example</h1>
<h3>by Szymon Łukaszczyk</h3>

<form enctype="multipart/form-data" action="example.php" method="POST">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    Send this file: <input name="file" type="file" />
    <input type="submit" value="Send File" />
</form>
<pre>
<?php

include_once("SimpleUpload.php");
if( isset ($_FILES["file"])):

$upload = new SimpleUpload;
//~ $upload->setPath("./upload"); // sets upload path to ./upload
//~ $upload->setDebug(true);
$upload->setContents($_FILES["file"]);
//~ $upload->setRandomAddSize(false);  // no random ending
//~ $upload->setRandomAddSize(10); // 10 char random ending
//~ $upload->setMaxNameSize(10); //file without extension has 10 chars
//~ $upload->setNameFormating(false); // no cropping, no char conversion(You can have !@/ in file name)
//~ $upload->setRandomEnding(false);  // no random ending
//~ $upload->setMd5Making(false) // no md5 sum

if (!$upload->uploadFile(false))
		echo "Error occured";
else
{
		echo  "Success<br/>";
		print_r($upload->getContents());
}

endif;
?>


</pre>
</body>
</html>