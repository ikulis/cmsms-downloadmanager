<?php
if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}
if (isset($params['cancel']) or !isset($params['file_id']) )
{
	$this->Redirect($id, 'defaultadmin', $returnid);
}

$file_id = '';
if (isset($params['file_id']))
{
	$file_id = $params['file_id'];
}
$checked = array();

foreach($params as $key => $var )
{
	$tmp = explode("_" , $key);
	if( $tmp[0] == "assoc") 
		$checked[] = (int) $tmp[1];
}
//~ var_dump($checked);

$assoc_before = array();
$query = "SELECT category_id FROM ".cms_db_prefix()."module_downloadmanager_files_category 
WHERE file_id = ?;";
$dbresult = $db->Execute($query,array($file_id));
while ($dbresult && $row = $dbresult->FetchRow())
	$assoc_before[] = (int) $row['category_id'];
//~ var_dump($assoc_before);

$query = "DELETE FROM ".cms_db_prefix()."module_downloadmanager_files_category 
WHERE file_id = ? AND category_id NOT IN ( ".implode(" , ",$checked)." );";
$db->Execute($query, array( $file_id));

$diff = array_diff(  $checked, $assoc_before  );
$query = "INSERT INTO ".cms_db_prefix()."module_downloadmanager_files_category ( category_id, file_id)  VALUES ( ". implode(" , ".$file_id." ), ( ",$diff).", ".$file_id." )"  ;
$db->Execute($query);

@$this->SendEvent('DownloadManagerFileEdited', array('file_id' => $file_id, 'name' => $name));
$params = array('tab_message'=> 'filemodified', 'active_tab' => 'files', 'selector'=>$params['selector']);
$this->UpdateAssociationAgregation( (int) $file_id);
$this->Redirect($id, 'defaultadmin', $returnid, $params);
?>