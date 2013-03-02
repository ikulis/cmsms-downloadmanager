<?php

if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}



$entryarray = array();
$query = 'select server_name from '.cms_db_prefix() .'module_downloadmanager_files';
$dbresult = $db->Execute($query);
$rowclass = 'row1';

$paths = array();
while ($dbresult && $row = $dbresult->FetchRow())
  $paths[] = realpath($row['server_name']);

$recurs  = (bool) $this->GetPreference('scan_recurs') ;

$scan_array = array();
$download_scan = explode(";",$this->GetPreference('scan'));

if(count($download_scan)>0)
  foreach( $download_scan as $f )
	$scan_array = array_merge( $scan_array ,$this->getDirectoryTree( $config['root_path'].DIRECTORY_SEPARATOR.$f, $recurs )) ;

$diff = array_diff($scan_array,$paths);
foreach( $diff as $k => $v)
  $diff[$k] = $this->CreateLink($id, 'addfile', $returnid, $v, array('file_path'=> base64_encode(
	substr($v,0,strlen($config['root_path']))==$config['root_path']?substr($v,strlen($config['root_path'])+1):$v
	)));

if( count($diff) == 0 ) 
{
  $params = array('tab_message'=> 'scannonew', 'active_tab' => 'files');
  $this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$this->smarty->assign('formid', $id);
$this->smarty->assign('label_newfiles', $this->Lang('scanresult', $this->GetPreference('scan'), count($diff)));

$this->smarty->assign('startform', $this->CreateFormStart($id, 'addfile', $returnid, 'post', 'multipart/form-data'));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
$this->smarty->assign('backlink', $this->CreateLink($id, 'defaultadmin', $returnid , '&laquo; '.$this->lang('back') ));
$this->smarty->assign_by_ref('diff', $diff);
echo $this->ProcessTemplate('scanfile.tpl');

?>