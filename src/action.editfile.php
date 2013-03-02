<?php
if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}
if (isset($params['cancel']))
{
    $params = array('active_tab' => 'files', 'selector'=>$params['selector']);
	$this->Redirect($id, 'defaultadmin', $returnid,$params);
}

// setting defaults
$file_id = isset($params['file_id'])?(int)$params['file_id']:'';
$server_name = '';
$ext = isset($params['ext'])?$params['ext']:'';
$size = '';
$thumb_value ='';
$hash = '';
$created = '';
$expire = '';
$start = '';
$visible = (isset($params['visible']))?$visible = $params['visible']:0;
$type = '';
$useexp = 0;
$alias = (isset($params['alias']))?$params['alias']:'';
$accesstype = (isset($params['accesstype']))?$params['accesstype']:0;
$counter = (!isset($params['counter']) || $params['counter']<0)?0:$params['counter'];
$template_detail = (isset($params['template_detail']))?$params['template_detail']:'';
$template_form = (isset($params['template_form']))?$params['template_form']:'';
$template_email = (isset($params['template_email']))?$params['template_email']:'';
$feugroups = (isset($params['feugroups']))?$params['feugroups']:array();
$description = (isset($params['description']))?$params['description']:'';
$thumb_value = (isset($params['thumb_path']))?$params['thumb_path']:'';

if (isset($params['useexp']))
	$expires = $params['expiredate_Year'].'-'.$params['expiredate_Month'].'-'.$params['expiredate_Day'].' '.$params['expiredate_Hour'].':'.$params['expiredate_Minute'].':'.$params['expiredate_Second'];
else
	$expires = NULL;
if (isset($params['usestart']))
    $starts = $params['startdate_Year'].'-'.$params['startdate_Month'].'-'.$params['startdate_Day'].' '.$params['startdate_Hour'].':'.$params['startdate_Minute'].':'.$params['startdate_Second'];
else
    $starts = NULL;


$name = '';
if(isset($params['changevisibility']))
{
    if(empty($file_id) || !is_numeric($file_id))
    {
	echo $this->ShowErrors($this->Lang('nofile'));
	return;
    }
    
    $query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_files SET visible = ? WHERE file_id = ?';
    $db->Execute($query, array($visible, $file_id));
    @$this->SendEvent('DownloadManagerFileEdited', array('file_id' => $file_id, 'name' => $name));
    $params = array('active_tab' => 'files', 'selector'=>$params['selector']);
    $this->Redirect($id, 'defaultadmin', $returnid, $params);
}
else if (isset($params['name']))
{
	$name = $params['name'];
	if ($name != '' && $alias != '')
	{
        
		// checking alias
		$alias = $this->CreateAlias($alias);
		if(!$this->CheckAlias('files', $alias,$params['file_id'],'file_id'))
			echo $this->ShowErrors($this->Lang('wrongalias'));
		else
		{
			$query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_files SET alias = ?,  name = ?,  ext = ?,  description = ?,  expires = ?,starts = ?, visible = ?, accesstype=?, template_detail=?, template_form=?, template_email=?, counter = ?, thumb_path = ? WHERE file_id = ?';
			$db->Execute($query, array($alias, $name, $ext, $description,  $expires,$starts, $visible, $accesstype, $template_detail, $template_form, $template_email,$counter, $thumb_value, $file_id));

			// update feu groups association
			$query = "DELETE FROM ".cms_db_prefix()."module_downloadmanager_filegroups WHERE file_id=?";
			$db->Execute($query, array($file_id));
			foreach($feugroups as $group){
				$query = "INSERT INTO ".cms_db_prefix()."module_downloadmanager_filegroups SET file_id=?, group_id=?";
				$db->Execute($query, array($file_id, $group));
			}

			// update search index
			$this->SearchDeleteFile($file_id);
			if($visible)
				$this->SearchAddFile( $file_id, $alias.' '.$name.' '.$description, $expires);
			// add more fields as needed to the send event
			@$this->SendEvent('DownloadManagerFileEdited', array('file_id' => $file_id, 'name' => $name));
			$params = array('tab_message'=> 'filemodified', 'active_tab' => 'files', 'selector'=>$params['selector']);
			$this->UpdateAssociationAgregation( (int) $file_id);
			$this->Redirect($id, 'defaultadmin', $returnid, $params);
		}
	}
	else
		echo $this->ShowErrors($this->Lang('nonamegiven'));
}

$query = 'select * from '.cms_db_prefix() .'module_downloadmanager_files WHERE file_id= ?';
$row = $db->GetRow($query, array($file_id));
//~ var_dump($row);
if ($row)
{
	$name = $row['name'];
	$alias = $row['alias'];
	$server_name = $row['server_name'];
	$ext = $row['ext'];
	$size = $row['size'];
	$hash = $row['hash'];
	$description = $row['description'];
	$created = $row['created'];
	if( !is_null($row['expires']) )
	{
		$useexp = 1;
		$expire = strtotime($row['expires']);
	}
    if( !is_null($row['starts']) )
    {
        $usestart = 1;
        $start = strtotime($row['starts']);
    }
	$visible = $row['visible'];
	$type = $row['type'];
	$counter = $row['counter'];
	$accesstype = $row['accesstype'];
	$template_detail = $row['template_detail'];
	$template_form = $row['template_form'];
	$template_email = $row['template_email'];
	$thumb_value = $row['thumb_path'];
	$feugroups = $this->GetFileGroups($file_id);
}


$entryarray = array();
$query = "SELECT  c.category_id, c.long_name , fc.file_id FROM ".cms_db_prefix()."module_downloadmanager_categories c
LEFT OUTER JOIN  ( 
	SELECT * FROM ".cms_db_prefix()."module_downloadmanager_files_category fc
	WHERE file_id = ?
	) fc
ON fc.category_id = c.category_id
ORDER BY c.hierarchy_priority; ";
$dbresult = $db->Execute($query,array($file_id));
$rowclass = 'row1';
//~ var_dump($dbresult);
while ($dbresult && $row = $dbresult->FetchRow())
{
	$onerow = new stdClass();
	$onerow->name = $row['long_name'];
	//~ $row['category_id'];
	$onerow->deletelink = $this->CreateLink($id, 'do_deleteassoc', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', $this->Lang('delete'),'','','systemicon'), array('file_id'=>$file_id,'category_id'=>$row['category_id']), $this->Lang('areyousure'));
	if( is_null( $row['file_id'] ))
		$onerow->inputassoc =  $this->CreateInputCheckbox($id, 'assoc_'.$row['category_id'], '1', 0, 'class="pagecheckbox"');
	else
		$onerow->inputassoc = $this->CreateInputCheckbox($id, 'assoc_'.$row['category_id'], '1', 1, 'class="pagecheckbox"');
	$onerow->rowclass = $rowclass;
	$entryarray[] = $onerow;
	($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}
$this->smarty->assign_by_ref('associations', $entryarray);

$this->smarty->assign('startformassoc', $this->CreateFormStart($id, 'do_updateassoc', $returnid));
$this->smarty->assign('enformassoc', $this->CreateFormEnd());


#Display template
$this->smarty->assign('startform', $this->CreateFormStart($id, 'editfile', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());

$this->smarty->assign('inputname', $this->CreateInputText($id, 'name', $name, 30, 255));
$this->smarty->assign('inputalias', $this->CreateInputText($id, 'alias', $alias, 30, 255));
//~ $this->smarty->assign('inputserver_name', $this->CreateInputText($id, 'server_name', $server_name, 30, 30));
$this->smarty->assign('inputserver_name',  $server_name);
$this->smarty->assign('inputext', $this->CreateInputText($id, 'ext', $ext, 30, 5));
$this->smarty->assign('inputcounter', $this->CreateInputText($id, 'counter', $counter, 30, 5));
//~ $this->smarty->assign('inputsize', $this->CreateInputText($id, 'size', $size, 30, 10));
$size = ( $size<1024?  $size." B": ( $size<1048576 ?   ((int) ($size/1024))." KB" : ((int) ($size/1048576 ))." MB" ));
$this->smarty->assign('inputsize',  $size );
//~ $this->smarty->assign('inputhash', $this->CreateInputText($id, 'hash', $hash, 30, 32));
$this->smarty->assign('inputhash', $hash);
$this->smarty->assign('inputdescription', $this->CreateTextArea($this->GetPreference('admin_wysiwyg', true), $id, $description, 'description', 'pagesmalltextarea', '', '', '', '20', '5'));
$this->smarty->assign('inputexp', $this->CreateInputCheckbox($id, 'useexp', '1', $useexp, 'class="pagecheckbox"'));
$this->smarty->assign('inputstart', $this->CreateInputCheckbox($id, 'usestart', '1', $usestart, 'class="pagecheckbox"'));
$this->smarty->assign('inputvisible', $this->CreateInputCheckbox($id, 'visible', '1', $visible, 'class="pagecheckbox"'));
$this->smarty->assign('inputtype', $type);
$this->smarty->assign('inputcreated', $created);
$this->smarty->assign('inputexpires', $expire);
$this->smarty->assign('expiredateprefix', $id.'expiredate_');
$this->smarty->assign('inputstarts', $start);
$this->smarty->assign('startdateprefix', $id.'startdate_');
$this->smarty->assign('inputaccesstype', $this->CreateInputDropdown($id, 'accesstype',$this->GetAccessTypeCombo(),-1,$accesstype));
$this->smarty->assign('inputthumb_path', $this->CreateInputDropdown($id, 'thumb_path',$this->GetThumbnailsDropdown($thumb_value),-1,$thumb_value));
$this->smarty->assign('inputfeugroups', $this->CreateInputSelectList($id, 'feugroups[]',$this->GetGroupsCombo(),$feugroups));
$this->smarty->assign('inputtemplate_form', $this->CreateInputDropdown($id, 'template_form',$this->GetTemplatesCombo("form"),-1,$template_form));
$this->smarty->assign('inputtemplate_detail', $this->CreateInputDropdown($id, 'template_detail',$this->GetTemplatesCombo("detail"),-1,$template_detail));
$this->smarty->assign('inputtemplate_email', $this->CreateInputDropdown($id, 'template_email',$this->GetTemplatesCombo("email"),-1,$template_email));

// ASSING ALL LABELS

$this->smarty->assign('namelabel', $this->Lang('namelabel'));
$this->smarty->assign('aliaslabel', $this->Lang('aliaslabel'));
$this->smarty->assign('server_namelabel', $this->Lang('server_namelabel'));
$this->smarty->assign('extlabel', $this->Lang('extlabel'));
$this->smarty->assign('sizelabel', $this->Lang('sizelabel'));
$this->smarty->assign('typelabel', $this->Lang('typelabel'));
$this->smarty->assign('hashlabel', $this->Lang('hashlabel'));
$this->smarty->assign('counterlabel', $this->Lang('counterlabel'));
$this->smarty->assign('descriptionlabel', $this->Lang('descriptionlabel'));
$this->smarty->assign('createdlabel', $this->Lang('createdlabel'));
$this->smarty->assign('expireslabel', $this->Lang('expireslabel'));
$this->smarty->assign('startlabel', $this->Lang('startslabel'));
$this->smarty->assign('visiblelabel', $this->Lang('visiblelabel'));
$this->smarty->assign('useexpirationtext', $this->Lang('useexpiration'));
$this->smarty->assign('usestarttext', $this->Lang('usestart'));
$this->smarty->assign('thumb_pathlabel', $this->Lang('thumblabel'));
$this->smarty->assign('associatedlabel', $this->Lang('associated'));
$this->smarty->assign('accesstypelabel', $this->Lang('accesstype'));
$this->smarty->assign('template_detaillabel', $this->Lang('templatefordetail'));
$this->smarty->assign('template_formlabel', $this->Lang('templateforform'));
$this->smarty->assign('template_emaillabel', $this->Lang('templateforemail'));
$this->smarty->assign('feugroupslabel', $this->Lang('feugroupslabel'));
$this->smarty->assign('extratab', $this->EndTab().$this->StartTab('extra', $params) );

$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'file_id', $file_id));
$this->smarty->assign('selector', $this->CreateInputHidden($id, 'selector', $params['selector']));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

$tab = '';
echo $this->StartTabHeaders();
	echo $this->SetTabHeader("overall",$this->Lang("overalltabheader"),($tab=="overall")) ;
	echo $this->SetTabHeader("assocations",$this->Lang("assocationstabheader"),($tab=="assocations"));
	echo $this->SetTabHeader("extra",lang("advanced"),($tab=="extra")) ;
echo $this->EndTabHeaders();

echo $this->StartTabContent();
	echo $this->StartTab('overall', $params) ;
	echo $this->ProcessTemplate('editfile.tpl');
	echo $this->EndTab() ;
	echo $this->StartTab('assocations', $params) ;
	echo $this->ProcessTemplate('editfile_assoc.tpl');
	echo $this->EndTab() ;
echo $this->EndTabContent();


?>

