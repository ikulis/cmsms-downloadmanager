<?php
if (!isset($gCms)) exit;


if (!$this->CheckPermission('Use DownloadManager'))
{
	echo $this->ShowErrors($this->Lang('needpermission', array('Use DownloadManager')));
	return;
}

if (isset($params['cancel']))
{
	$params = array('active_tab' => 'files');
	$this->Redirect($id, 'defaultadmin', $returnid);
}
$addfiledb = false;
$server_name = '';
$alias = '';
$ext = '';
$description = '';
$size = '';
$hash = '';
$useexp = '';
$accesstype = (isset($params['accesstype']))?$params['accesstype']:0;
$template_detail = (isset($params['template_detail']))?$params['template_detail']:'';
$template_form = (isset($params['template_form']))?$params['template_form']:'';
$template_email = (isset($params['template_email']))?$params['template_email']:'';
$feugroups = (isset($params['feugroups']))?$params['feugroups']:array();
$description = (isset($params['description']))?$params['description']:'';
$make_thumb = (isset($params['make_thumb']))? (bool) $params['make_thumb']:$this->GetPreference('thumbs_auto', true);
$thumb_path = (!empty($params['thumb_path']))? $params['thumb_path']:false;
$expires = NULL;
$inputuploadedenabled = true;

if (isset($params['useexp']))
	{$expires = $params['expiredate_Year'].'-'.$params['expiredate_Month'].'-'.$params['expiredate_Day'].' '.$params['expiredate_Hour'].':'.$params['expiredate_Minute'].':'.$params['expiredate_Second'];}
$visible = 0;
if (isset($params['visible']))
	$visible = $params['visible'];

include('function.uploadfile.php');
if($addfiledb) // new file
{
	// generate thumb
	if( $thumb_path )
		$f_contents['thumb'] = $thumb_path;
	else if( function_exists('gd_info') // check if gd support
		&& in_array( $f_contents['type'], array('image/png','image/jpeg','image/gif') ) // check if supported image
		&& $make_thumb // check should be done
		 ){// 
		$f_contents['thumb'] = $this->CreateThumbnail($f_contents['path'] );
	}

	$file_id = $db->GenID(cms_db_prefix()."module_downloadmanager_files_seq");
	// adding alias
	$i = 1;
	$alias = $n = $this->CreateAlias($file_name);
	while(!$this->CheckAlias('files', $alias))
		$alias = $n."-".$i++;
		
	//insert master info
	$query = 'INSERT INTO '.cms_db_prefix().'module_downloadmanager_files(file_id,alias, name, server_name, ext, type, size, hash, description, created, expires, visible, accesstype, template_detail, template_form, template_email,thumb_path) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
	$db->Execute($query, array($file_id,$alias, $file_name , $f_contents['path'], 
	$f_contents['ext'], $f_contents['type'], $f_contents['size'], $f_contents['md5'], 
	$description, date ("Y-m-d G:i:s") , $expires, $visible, $accesstype, $template_detail, $template_form, $template_email,$f_contents['thumb']));
	
	// insers associations		
	if( isset($params['assoc']) )
	{
		$query = "INSERT INTO ".cms_db_prefix()."module_downloadmanager_files_category ( category_id, file_id)  VALUES ( ". implode(" , ".$file_id." ), ( ",array_keys($params['assoc']) ).", ".$file_id." )"  ;
		$db->Execute($query);
	}
	
	// update feu groups association
	$query = "DELETE FROM ".cms_db_prefix()."module_downloadmanager_filegroups WHERE file_id=?";
	$db->Execute($query, array($file_id));
	foreach($feugroups as $group){
		$query = "INSERT INTO ".cms_db_prefix()."module_downloadmanager_filegroups SET file_id=?, group_id=?";
		$db->Execute($query, array($file_id, $group));
	}
	
	// add more fields as needed to the send event
	@$this->SendEvent('DownloadManagerFileAdded', array('file_id' => $file_id, 'name' => $name));

	$this->UpdateAssociationAgregation( (int) $file_id);
	if($visible)
		$this->SearchAddFile( (int) $file_id, $alias.' '.$file_name.' '.$description, $expires);
	$params = array('tab_message'=> 'fileadded', 'active_tab' => 'files');
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

if (isset($params['file_path']))
{
	$uploaded = base64_decode($params['file_path']);
	$inputuploadedenabled = false;
}
#Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'addfile', $returnid, 'post', 'multipart/form-data'));
$this->smarty->assign('endform', $this->CreateFormEnd());

// associations
global $gCms;
$query = "SELECT  category_id, long_name  FROM ".cms_db_prefix()."module_downloadmanager_categories  ORDER BY hierarchy_priority; ";
$dbresult = $db->Execute($query);
$rowclass = 'row1';
$entryarray = array();
while ($dbresult && $row = $dbresult->FetchRow())
{
	$onerow = new stdClass();
	$onerow->name = $row['long_name'];
	$onerow->inputassoc =  $this->CreateInputCheckbox($id, 'assoc['.$row['category_id'].']', '1', 0, 'class="pagecheckbox"');
	$onerow->rowclass = $rowclass;
	$entryarray[] = $onerow;
	($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}

$this->smarty->assign_by_ref('associations', $entryarray);
$this->smarty->assign ('categorytext', $this->Lang("assocationstabheader"));
$this->smarty->assign ('rootpath', $gCms->config['root_path']);
$this->smarty->assign ('formid', $id);
$this->smarty->assign ('inputfile', $this->CreateFileUploadInput($id,'file',' id="'.$id.'file" '.($inputuploadedenabled?'':'disabled="disabled"')));
$this->smarty->assign('inputuploaded', $this->CreateInputText($id, 'uploaded',$uploaded, 60,225, !$inputuploadedenabled?'':'disabled="disabled"'));
$this->smarty->assign('inputuploadedenabled', $inputuploadedenabled);
$this->smarty->assign('inputmd5', $this->CreateInputText($id, 'md5','', 32,32, 'disabled="disabled"'));
$this->smarty->assign('inputexternal', $this->CreateInputText($id, 'external', '', 60,255, 'disabled="disabled"'));
$this->smarty->assign('inputserver_name', $this->CreateInputText($id, 'server_name', $server_name, 30, 30));
$this->smarty->assign('inputext', $this->CreateInputText($id, 'ext', $ext, 30, 5));
$this->smarty->assign('inputsize', $this->CreateInputText($id, 'size', $size, 30, 10));
$this->smarty->assign('inputhash', $this->CreateInputText($id, 'hash', $hash, 30, 32));
$this->smarty->assign('inputdescription', $this->CreateTextArea( $this->GetPreference('admin_wysiwyg', true), $id, $description, 'description', 'pagesmalltextarea', '', '', '', '300', '5'));
$this->smarty->assign('expiredateprefix', $id.'expiredate_');
$this->smarty->assign('inputexp', $this->CreateInputCheckbox($id, 'useexp', '1', $useexp, 'class="pagecheckbox"'));
$this->smarty->assign('inputvisible', $this->CreateInputCheckbox($id, 'visible', '1', $visible, 'class="pagecheckbox"'));
$this->smarty->assign('inputmake_thumb', $this->CreateInputCheckbox($id, 'make_thumb', '1', $make_thumb, 'class="pagecheckbox"'));
$this->smarty->assign('inputaccesstype', $this->CreateInputDropdown($id, 'accesstype',$this->GetAccessTypeCombo(),-1,$accesstype));
$this->smarty->assign('inputfeugroups', $this->CreateInputSelectList($id, 'feugroups[]',$this->GetGroupsCombo(),$feugroups));
$this->smarty->assign('inputtemplate_form', $this->CreateInputDropdown($id, 'template_form',$this->GetTemplatesCombo("form"),-1,$template_form));
$this->smarty->assign('inputtemplate_detail', $this->CreateInputDropdown($id, 'template_detail',$this->GetTemplatesCombo("detail"),-1,$template_detail));
$this->smarty->assign('inputtemplate_email', $this->CreateInputDropdown($id, 'template_email',$this->GetTemplatesCombo("email"),-1,$template_email));
$this->smarty->assign('inputthumb_path', $this->CreateInputDropdown($id, 'thumb_path',$this->GetThumbnailsDropdown($thumb_value)));

// ASSING ALL LABELS
$this->smarty->assign('uploadlabel', $this->Lang('uploadfile'));
$this->smarty->assign('uploadedlabel', $this->Lang('alreadyuploaded'));
$this->smarty->assign('externallabel', $this->Lang('externalfile'));
$this->smarty->assign('server_namelabel', $this->Lang('server_namelabel'));
$this->smarty->assign('extlabel', $this->Lang('extlabel'));
$this->smarty->assign('sizelabel', $this->Lang('sizelabel'));
$this->smarty->assign('hashlabel', $this->Lang('hashlabel'));
$this->smarty->assign('descriptionlabel', $this->Lang('descriptionlabel'));
$this->smarty->assign('createdlabel', $this->Lang('createdlabel'));
$this->smarty->assign('expireslabel', $this->Lang('expireslabel'));
$this->smarty->assign('visiblelabel', $this->Lang('visiblelabel'));
$this->smarty->assign('make_thumblabel', $this->Lang('make_thumblabel'));
$this->smarty->assign('useexpirationtext', $this->Lang('useexpiration'));
$this->smarty->assign('accesstypelabel', $this->Lang('accesstype'));
$this->smarty->assign('template_detaillabel', $this->Lang('templatefordetail'));
$this->smarty->assign('template_formlabel', $this->Lang('templateforform'));
$this->smarty->assign('template_emaillabel', $this->Lang('templateforemail'));
$this->smarty->assign('feugroupslabel', $this->Lang('feugroupslabel'));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
$this->smarty->assign('thumb_pathlabel', $this->Lang('thumblabel'));

$this->smarty->assign('filetypeinput',  $this->ProcessTemplate('filetypeinput.tpl'));
echo $this->ProcessTemplate('addfile.tpl');
?>
