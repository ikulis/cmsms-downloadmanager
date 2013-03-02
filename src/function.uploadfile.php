<?php

include_once("lib/simple-upload/SimpleUpload.php");
$uploaded = '';
$file = '';
$f_contents = '';
$file_name = '';
if(isset($_FILES[$id."file"]) && $params['radiou'] == 'new')
{
	// we have newly uploaded file
	$file = $_FILES[$id."file"];
	if( $file['size'] == 0)
	{
		echo $this->ShowErrors($this->Lang("error")." ".$this->Lang('nothinguploaded') );
		return;
	}
	
	if ( is_array($file) )
	{
		$dir = $this->cms->config['root_path'].DIRECTORY_SEPARATOR.$this->GetPreference('dir').DIRECTORY_SEPARATOR;
		try {
			$upload = new SimpleUpload;
			$upload->setPath($dir);
			$upload->setContents($file);
			if (!$upload->uploadFile())
			{
				echo $this->ShowErrors($this->Lang("error")." ". $this->Lang('unabletomove'));
				return;
			}
			$f_contents = $upload->getContents();
		} catch (Exception $e) {
			echo $this->ShowErrors($this->Lang("error")." ".$e->getMessage() );
			return;
		}
	}
	// setting things for database
	$file_name = substr( $file["name"] , 0  , strrpos( $file["name"] ,  $f_contents['ext'] )-1);
	$f_contents['path'] = realpath($f_contents['path']);
	$addfiledb = true;

}
else if (isset($params['uploaded']) && $params['radiou'] == 'old')
{
	$uploaded = $this->cms->config['root_path'].DIRECTORY_SEPARATOR.$params['uploaded'];
	if( !file_exists($uploaded) )
	{
		echo $this->ShowErrors($this->Lang("filedoesntexists",array($uploaded)));
		return;
	}
	$f_contents['path'] = realpath($uploaded);

	$path_parts = pathinfo($f_contents['path']);
	$f_contents['ext'] = $path_parts['extension'];
	$file_name = substr( $path_parts['basename'] , 0  , strrpos( $path_parts['basename'] , $path_parts['extension'] )-1);
	$f_contents['type'] = mime_content_type($uploaded);
	$f_contents['size'] = filesize($uploaded);
	$f_contents['md5'] = md5_file($uploaded);
	$addfiledb = true;
}
else if (isset($params['external']) && $params['radiou'] == 'external')
{

  if(!function_exists('curl_exec'))
  {
	echo $this->ShowErrors($this->Lang("curldoesntexists"));
	return;
  }
  if(strpos( $params['external']  , 'http://') === 0 )
	$params['external'] = substr($params['external'],7);

  $remoteFile = 'http://'.$params['external'];
  $ch = curl_init($remoteFile);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //not necessary unless the file redirects (like the PHP example we're using here)
  $data = curl_exec($ch);
  curl_close($ch);
  if ($data === false) 
  {
	echo $this->ShowErrors($this->Lang("curlfailed"));
	return;
  }
  if (preg_match('/^HTTP\/1\.[01] (\d\d\d)/', $data, $matches)) {
	$status = (int)$matches[1];
	if($status == 404)
	{
	  echo $this->ShowErrors($this->Lang("filedoesntexists", array($remoteFile)));
	  return;
	}
  }
  
  $f_contents['path'] = $remoteFile;
  if (preg_match('/Content-Length: (\d+)/', $data, $matches)) {
	$f_contents['size'] = (int)$matches[1];
  }
  if (preg_match('/Content-Type: ([\w\/_\-+]+)/', $data, $matches)) {
	$f_contents['type'] = $matches[1];
  }

  $f_contents['md5'] = $params['md5'];
  $path_parts = pathinfo($f_contents['path']);
  $f_contents['ext'] = $path_parts['extension'];
  $file_name = substr( $path_parts['basename'] , 0  , strrpos( $path_parts['basename'] , $path_parts['extension'] )-1);
  $addfiledb = true;
}
?>