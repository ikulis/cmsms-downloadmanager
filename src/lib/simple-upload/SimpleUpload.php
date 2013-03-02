<?php 
/*
    class SimpleUpload - allow easy upload with php and web forms
    Copyright (C) 2008 Szymon Łukaszczyk

    This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/
/**
* \details  allow easy upload with php and web forms
* \author  Szymon Łukaszczyk <szymon.lukaszczyk@gmail.com>
* \since  April/2007
*/
class  SimpleUpload 
{
	private  $dir = "."; ///< place where our download will be 
	private $file_c = 0; ///< all file contents
	private  $max_name_size = 20; ///< max name size without extension
	private $random_size= 5; ///< size of the random part, if false -> no random part
	private $random_end =''; ///< last generated random ending
	private $name_formating =  true; ///< if true, file is cropped to $max_name_size and all non [a-zA-Z0-9.] are removed
	private $make_md5 =  true; ///< if to make md5 sum at the end
	private $debug = false;
	
/**
* \brief   sets randomize seed
*/
	public function __construct(  )
	{
		$this->file_c = array();
		mt_srand((double) microtime() * 1000000);
		$this->_debug($this->file_c);
	}

/* 
	private properties 
*/
/**
* \brief gets path to the uploaded file
*/
	private function getFilePath()
	{
		return $this->file_c["path"];
	}
/**
* \brief   sets the upload path
*/
	private function setFilePath()
	{
		$this->file_c["path"]  =  str_replace( "//", "/", $this->dir."/".$this->file_c["full_name"] ) ;
	}
/* 
	private functions 
*/
/**
* \brief  debug function
*/	
	private function _debug( $var, $txt = '' )
	{
		if( $this->debug )
			var_dump($var);
	}

/**
* \brief   separates the file nama from extension
* \param name got from $_FILES['name]
*/
	private function setNames(  $name )
	{
		$ext = ''; $file = '';
		$arrName = explode(".",$name);
		$arrSize = count($arrName);
		
		if( $arrName[0] == '' ) // name begins with dot
		{
			array_shift($arrName);
			$arrName[0] = ".".$arrName[0];
			$arrSize = count($arrName);
		}	
		if( $arrSize > 2 && in_array( $arrName[$arrSize-2].".".$arrName[$arrSize-1],  array('tar.gz', 'tar.bz', 'tar.bz2') ))  // double extensions
		{
			$ext = $arrName[$arrSize-2].".".$arrName[$arrSize-1];
			unset( $arrName[$arrSize-2] , $arrName[$arrSize-1] );
			$file = implode(".", $arrName);
		}
		else if( $arrSize > 1 ) // there is dot in name
		{
			$ext = $arrName[$arrSize-1];
			unset( $arrName[$arrSize-1] );
			$file = implode(".", $arrName);
		}
		else // no dot in name
		{
			$file = $name;
		}
		
		if($this->name_formating)
		{
			$ext  = $this->formatStr($ext);
			$file  = $this->formatStr($file);
		}
		$this->file_c["name"] = $file;
		$this->file_c["ext"] = $ext;
		
		$this->setFilePath();
	}
/**
* \brief   generates the random part and sets the $this->file_c["full_name"] right
* \return  false if $this->random_size === false 
*/
	private function makeRandom() 
	{
		if( $this->random_size=== false )
			return false;
		
		$this->random_end = "";
		$arr = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		for ($i = 0; $i < $this->random_size; $i++) 
			$this->random_end .= $arr[mt_rand()&strlen($arr)];
			
		$this->file_c["full_name"]  = $this->file_c["name"]."_".$this->random_end.".".$this->file_c["ext"];
		$this->setFilePath();
	}


/**
* \brief   removes all non [a-zA-Z0-9.]  chars
* \param var str to format
* \return  formated string
*/
	private function formatStr($var)
	{
		$var = strtr($var,"ĄąŚśĘęÓóŁłŻżŹźĆćŃń","AaSsEeOoLlZzZzCcNn"); ///<
		$var = preg_replace("/([^a-zA-Z0-9\.])/", "_", $var ) ; ///<
		$var = mb_strtolower($var); ///<
		return $var;
	}
/**
* \brief   crops the name to $this->max_name_size
* \return  false if $this->name_formating is false
*/
	private function formatName()
	{
		if( !$this->name_formating )
			return false;
		$size = strlen($this->file_c["name"]);		
		if ( $size+($this->random_size ? (int)$this->random_size-1 : 0) > $this->max_name_size )
			$this->file_c["name"] = substr($this->file_c["name"],
				0,
				$this->max_name_size-($this->random_size ? (int)$this->random_size-1 : 0) ); 

		$this->makeRandom();
	}
	


/* 
	public properties 
*/
/**
* \brief   sets debugging
*/
	function setDebug( $b )
	{
		$this->debug = (bool) $b;
	}
	
/**
* \brief set all contents about the file
* \details  example: $upload->setContents( $_FILES["file"] );
* \param file $_FILES["file"]
* \exception Exception new Exception('The file was not uploaded to /tmp directory')
* \exception Exception new Exception('The uploaded file exceeds the upload_max_filesize '.ini_get("upload_max_filesize").' directive in php.ini.')
* \exception Exception new Exception('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.')
* \exception Exception new Exception('The uploaded file was only partially uploaded.')
* \exception Exception new Exception('No file was uploaded.')
* \exception Exception new Exception('Missing a temporary folder.')
* \exception Exception new Exception('Failed to write file to disk.')
* \exception Exception new Exception('File upload stopped by extension.')
* \return  false if $file is not valid
*/
	 public function setContents( $file )
	{
		if(  !(@is_uploaded_file($file["tmp_name"]) ))
			throw new Exception('The file was not uploaded to /tmp directory');
		if( !isset($file["name"]) || !isset($file["tmp_name"])   || !isset($file["type"] ) || !isset($file["size"]) || !isset($file["error"])  )
			return false;
		$file["size"] = (int) $file["size"];
		if( $file["error"] != UPLOAD_ERR_OK )
			switch( $file["error"] ) 
			{
				case UPLOAD_ERR_INI_SIZE: throw new Exception('The uploaded file exceeds the upload_max_filesize '.ini_get("upload_max_filesize").' directive in php.ini.'); break;
				case UPLOAD_ERR_FORM_SIZE: throw new Exception('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.'); break;
				case UPLOAD_ERR_PARTIAL: throw new Exception('The uploaded file was only partially uploaded.'); break;
				case UPLOAD_ERR_NO_FILE: throw new Exception('No file was uploaded.'); break;
				case UPLOAD_ERR_NO_TMP_DIR: throw new Exception('Missing a temporary folder.'); break;
				case UPLOAD_ERR_CANT_WRITE: throw new Exception('Failed to write file to disk.'); break;
				case UPLOAD_ERR_EXTENSION: throw new Exception('File upload stopped by extension.'); break;
			}
		$this->_debug($this->file_c);
		unset( $file["error"]);
		//~ if(!is_array( $this->file_c ) )
			//~ $this->file_c = array();
		foreach( $file as $key => $var) 
			$this->file_c[$key] = $var;
		$this->_debug($this->file_c);
		$this->setNames($file["name"]);
		$this->_debug($this->file_c);
	}
	
/**
* \brief   get data about uploaded file
* \return  
*/
	 public function getContents()
	{
		$ret = $this->file_c;
		$ret['path'] =realpath($ret['path']);
		unset( $ret["tmp_name"]);
		return $ret;
	}

/**
* \brief  sets $this->random_size
* \param int (1-32) number of random chars of false
*/
	 public function setRandomAddSize( $int)
	{
		if( (int) $int > 33 )
			$this->random_size= 32;
		elseif( (int) $int > 0 )
			$this->random_size= (int) $int;
		else
			$this->random_size= false;
	}

/**
* \brief   sets the $this->dir
* \param path to upload dir
*/
	 public function setPath($path)
	{
		$this->dir = $path;
		$this->testPath();
		$this->setFilePath();
	}
/**
* \brief   tests $this->dir
* \exception Exception new Exception('Path not writable.')
*/
	public function testPath()
	{
		$path = $this->dir;
		$time = time();
		$rand = mt_rand();

		if ( ! @file_put_contents($path.$time.$rand , ".." ) )
			throw new Exception("Path '$path' not writable");
		@unlink($path.$time.$rand );
		$this->setFilePath();
	}
/**
* \brief  sets $this->max_name_size
* \param max_name_size  int bigger than 5
*/
	public  function setMaxNameSize($max_name_size)
	{
		if ((int)$max_name_size > 5)
			$this->max_name_size = (int)$max_name_size;
		else
			$this->max_name_size = 20;
	}
/**
* \brief   sets $this->name_formating
* \param format  bool
*/
	public  function setNameFormating( $format)
	{
		 $this->name_formating = (bool) $format	;
	}
/**
* \brief   sets $this->random_end
* \param format  bool
*/
	public  function setRandomEnding( $format)
	{
		 $this->random_end = (bool) $format;
	}
/**
* \brief   sets $this->make_md5
* \param make bool
*/
	public  function setMd5Making( $make)
	{
		 $this->make_md5 = (bool) $make	;
	}
/* 
	public functions ///<
*/
/**
* \brief uploads file
* \details 
		formats the file name, generates new random names,  makes md5 sum  
		all according to configuration 
* \param move  if true uses move_uploaded_file() else copy()
* \exception Exception new Exception('File ('.$this->file_c["full_name"].') you are trying to upload exists on serwer')
* \return  (bool)  true if succes else false
*/
	public function uploadFile( $move = false)
	{
		$ret = false;
		$this->setFilePath();
		$this->testPath();
		$this->formatName();
		
		if( file_exists($this->file_c["path"]) )
			if( $this->random_size!== false) 
				while( file_exists($this->file_c["path"]) )
					$this->makeRandom() ;
			else
				throw new Exception('File ('.$this->file_c["full_name"].') you are trying to upload exists on serwer');
		
		if( !(bool) $move)
		{
			if (copy($this->file_c["tmp_name"],$this->file_c["path"]))
				$ret = true;
		} else {
			if (move_uploaded_file($this->file_c["tmp_name"],$this->file_c["path"]))
				$ret = true;
		}
		$this->_debug($ret);
		$this->_debug($move);
		if( $ret && $this->make_md5)
			$this->file_c['md5'] = md5_file( $this->file_c["path"] );
		return $ret;
	}	
	


}

?>
