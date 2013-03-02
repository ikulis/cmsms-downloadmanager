<?php
if(!isset($gCms))	exit;

$db =& $this->GetDb();
$file_id = '';
$file = false;

// load file data
if(isset($params['alias']))
{
	$alias = $params['alias'];

	// get the file id
	$query = 'SELECT file_id FROM '.cms_db_prefix().'module_downloadmanager_files WHERE alias = ? ';
	$dbresult = $db->Execute($query,array($params['alias']));
	if ($dbresult && $row = $dbresult->FetchRow())
	    $file_id = $row['file_id'];

	// get categoriess
	$files_categories = $this->GetFileCategories('file_id = '.$file_id);

	// run the assing files function
	$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files WHERE file_id = '.$file_id;
	$dbresult = $db->Execute($query);
	$showheaders = false;
	include('function.assingfiles.php');
	$this->smarty->assign_by_ref('item', $onerow);
}
if(!$onerow) // checking if file exists in database
{
	$this->DisplayDownloadErrorPage($this->lang('nofile'));
}



// we give the file info to smarty... so in both detail and form templates, file info will be available with $file->field...

if( isset( $params['template'] ) && $template = $this->GetTemplate('detail_'.$templatename) )
{
	// template is specified in smarty tag
	// the template exists, we're going to use it
}
elseif( $file->template_detail != '' && $template = $this->GetTemplate($file->template_detail) )
{
	//getting template from file info in database
	// idem
}
else
{
	// no template specified, or template doesn't exist... we get the default one
	$templatename = $this->GetPreference('default_detail_template', '');
	$template = $this->GetTemplate($templatename);
}

$this->smarty->assign('namelabel', $this->Lang('namelabel'));
$this->smarty->assign('aliaslabel', $this->Lang('aliaslabel'));
$this->smarty->assign('server_namelabel', $this->Lang('server_namelabel'));
$this->smarty->assign('extlabel', $this->Lang('extlabel'));
$this->smarty->assign('sizelabel', $this->Lang('sizelabel'));
$this->smarty->assign('typelabel', $this->Lang('typelabel'));
$this->smarty->assign('hashlabel', $this->Lang('hashlabel'));
$this->smarty->assign('descriptionlabel', $this->Lang('descriptionlabel'));
$this->smarty->assign('createdlabel', $this->Lang('createdlabel'));
$this->smarty->assign('expireslabel', $this->Lang('expireslabel'));
$this->smarty->assign('visiblelabel', $this->Lang('visiblelabel'));
$this->smarty->assign('useexpirationtext', $this->Lang('useexpiration'));
$this->smarty->assign('associatedlabel', $this->Lang('associated'));
$this->smarty->assign('accesstypelabel', $this->Lang('accesstype'));
$this->smarty->assign('template_detaillabel', $this->Lang('templatefordetail'));
$this->smarty->assign('template_formlabel', $this->Lang('templateforform'));
$this->smarty->assign('template_emaillabel', $this->Lang('templateforemail'));
$this->smarty->assign('feugroupslabel', $this->Lang('feugroupslabel'));

echo $this->ProcessTemplateFromData($template);
