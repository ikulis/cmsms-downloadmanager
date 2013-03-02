<?php
if (!isset($gCms)) exit;
if (!$this->CheckPermission('Use DownloadManager'))
{
	echo $this->ShowErrors($this->Lang('needpermission', array('Use DownloadManager')));
	return;
}
if (!isset($params['file_id']) || empty($params['file_id']))
{
	echo $this->ShowErrors($this->Lang('nofile'));
	return;
}

include('function.uploadfile.php');

if($addfiledb)
{
	$params['file_id'] = (int) $params['file_id'];
	// removing the old file
	$query = 'SELECT server_name FROM ' . cms_db_prefix() . 'module_downloadmanager_files WHERE file_id= ?';
	$dbresult = $db->Execute($query, array($params['file_id']));
	$row = $dbresult->FetchRow();
	unlink($row['server_name']);
	if( !isset($params['changename']) || $params['changename'] == 0 )
	{
		$query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_files SET server_name = ?, type = ?, size = ?, hash = ?,  created = now() WHERE file_id = ?';
		$db->Execute($query, array( $f_contents['path'], $f_contents['type'], $f_contents['size'], $f_contents['md5'],$params['file_id']));
	}
	else
	{
		$query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_files SET name = ? , ext = ? , server_name = ?, type = ?, size = ?, hash = ?,  created = now() WHERE file_id = ?';
		$db->Execute($query, array($file_name , $f_contents['ext'], $f_contents['path'], $f_contents['type'], $f_contents['size'], $f_contents['md5'],$params['file_id']));
	}
	$params = array('tab_message'=> 'filechanged', 'active_tab' => 'files', 'selector'=>$params['selector']);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
else
{
$this->smarty->assign ('formid', $id);
$this->smarty->assign ('rootpath', $gCms->config['root_path']);
$this->smarty->assign('changefileinfo', $this->Lang('changefileinfo', array( base64_decode($params['name']), base64_decode($params['alias']) )));
$this->smarty->assign('uploadlabel', $this->Lang('uploadfile'));
$this->smarty->assign('uploadedlabel', $this->Lang('alreadyuploaded'));
$this->smarty->assign('changenamelabel', $this->Lang('changenamelabel'));
$this->smarty->assign('externallabel', $this->Lang('externalfile'));
$this->smarty->assign('hashlabel', $this->Lang('hashlabel'));

$this->smarty->assign ('inputfile', $this->CreateFileUploadInput($id,'file',' id="'.$id.'file" '));
$this->smarty->assign('inputuploaded', $this->CreateInputText($id, 'uploaded',$uploaded, 80,225,'disabled="disabled"'));
$this->smarty->assign('inputmd5', $this->CreateInputText($id, 'md5',$parms['md5'], 32,32, 'disabled="disabled"'));
$this->smarty->assign('inputexternal', $this->CreateInputText($id, 'external',$uploaded, 60,255, 'disabled="disabled"'));
$this->smarty->assign('inputchangename', $this->CreateInputCheckbox($id, 'changename', '1', 0, 'class="pagecheckbox"'));
$this->smarty->assign('selector', $this->CreateInputHidden($id, 'selector', $params['selector']));

$this->smarty->assign('startform', $this->CreateFormStart($id, 'changefile', $returnid, 'post', 'multipart/form-data'));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'file_id', (int) $params['file_id']));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
$this->smarty->assign('filetypeinput',  $this->ProcessTemplate('filetypeinput.tpl'));
echo $this->ProcessTemplate('changefile.tpl');
}

?>
