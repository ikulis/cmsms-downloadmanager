<?php
if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager') )
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}

$file_id = '';
if (isset($params['file_id']))
{
	$file_id = $params['file_id'];
}

// remove the file
$query = 'SELECT server_name FROM ' . cms_db_prefix() . 'module_downloadmanager_files WHERE file_id= ?';
$dbresult = $db->Execute($query, array($file_id));
if ($dbresult)
{
	$row = $dbresult->FetchRow();
	if(! $this->checkFileAllow($filepath) )
        unlink($row['server_name']);
}

//Update search index
$this->SearchDeleteFile($file_id);

//Now remove the item from database
$query = 'DELETE from ' . cms_db_prefix() . 'module_downloadmanager_files WHERE file_id= ?';
$db->Execute($query, array($file_id));
$query = 'DELETE from ' . cms_db_prefix() . 'module_downloadmanager_files_category WHERE file_id= ?';
$db->Execute($query, array($file_id));


// add more fields as needed to the send event
@$this->SendEvent('DownloadManagerFileDeleted', array('file_id' => $file_id));

$params = array('tab_message'=> 'filedeleted', 'active_tab' => 'files', 'selector'=>$params['selector']);
$this->Redirect($id, 'defaultadmin', $returnid, $params);



?>

