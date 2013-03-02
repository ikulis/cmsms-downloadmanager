<?php
require_once(dirname(__FILE__)."/../../include.php");

global $gCms;
if(is_null($gCms))
	$gCms = cmsms();

$dm =& $gCms->modules['DownloadManager']['object'];
if(is_null($dm))
	$dm = &CMSModule::GetModuleInstance('DownloadManager');

$db =& $gCms->GetDb();

$file = new StdClass();
$hasperm = false;
$file_path  = '';
$file_name = '';
$file_id = '';
// make checking when downloading by alias
if( isset($_GET['alias']) )
{
	$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files WHERE alias = ? AND '.$dm->GetFileStdWhere();
	$dbresult = $db->Execute($query,array($_GET['alias']));
	if ($dbresult && $row = $dbresult->FetchRow())
	{
		foreach($row as $key=>$value)
			$file->$key = $value;
		$file_path =  $row['server_name'];
		$file_name =  $row['name'].($row['ext']!=''?'.'. $row['ext']:'');
		$file_id = $row['file_id'];
	}
	else
	{
		$dm->DisplayDownloadErrorPage($dm->lang('noalias'));
	}

	// lets see if we accessed by FEU
	if( $file->accesstype >= 1 )
	{
		// we did indeed
		$hasperm = $dm->CheckFEUPerm($row['file_id']);
	}
	else if( $file->accesstype == 0 )
		// free downloads for all
		$hasperm = true;

}
// making checking when downloading by email
else if( isset($_GET['hashkey']))
{
	$hasperm = true;
	$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files f
		RIGHT JOIN '.cms_db_prefix().'module_downloadmanager_downloads c USING (file_id)
		WHERE hashkey = ? AND '.$dm->GetFileStdWhere('f');
	$dbresult = $db->Execute($query, array($_GET['hashkey']));
	if ($dbresult && $row = $dbresult->FetchRow())
	{
		foreach($row as $key=>$value)
			$file->$key = $value;

		$file_path =  $row['server_name'];
		$file_name =  $row['name'].($row['ext']!=''?'.'. $row['ext']:'');
		$file_id =  $row['file_id'];

		$db->Execute('UPDATE '.cms_db_prefix().'module_downloadmanager_downloads
			SET downloaded = now() WHERE hashkey = ?', array($_GET['hashkey']) );
	}
	else
		$dm->DisplayDownloadErrorPage($dm->lang('nohash'),'');
}
else
	$dm->DisplayDownloadErrorPage($dm->lang('wrongparams'));

if(!$hasperm)
{
	header("HTTP/1.0 403 Forbidden");
    echo "HTTP/1.0 403 Forbidden";
	exit;
}

if( $file_path == '' || $file_name == '' )
	$dm->DisplayDownloadErrorPage($dm->lang('wrongdata'));

if(strpos($file_path , 'http://') === 0 || 
	strpos($file_path , 'https://') === 0 )
{
  header('Location: '.$file_path);
  exit;
}
else if( !file_exists($file_path ))
	$dm->DisplayDownloadErrorPage($dm->lang('filenotfound',$file_name));

$db->Execute('UPDATE '.cms_db_prefix().'module_downloadmanager_files
			SET counter = counter+1 WHERE file_id = ?', array($file_id) );

if( ! $dm->checkFileAllow($file_path) )
{
    header("HTTP/1.0 403 Forbidden");
    echo "HTTP/1.0 403 Forbidden";
    exit;
}
// all checked -> downloading
include_once("lib/vnc_httpdownload/class.httpdownload.php");
$object = new httpdownload;
$object->set_byfile($file_path);
$object->filename = $file_name;
$object->download();
?>
