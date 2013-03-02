<?php

if (!isset($gCms)) exit;

// check for permissions
if (!$this->CheckPermission('Use DownloadManager'))
{
	echo $this->ShowErrors($this->Lang('needpermission', array('Use DownloadManager')));
	return;
}

// types can be added or removed simply by changing the array below
$templatetypes = array('list','detail','form','email');

// we first retrieve the default templates, to create toggledefault links
// and because we don't want delete links for these
$default = array();
$orderedtemplates = array();
foreach($templatetypes as $onetype){
	$default[] = $this->GetPreference('default_'.$onetype.'_template', '');
	$orderedtemplates[$onetype] = array();
}

// then we retrieve all module templates, but we'll need to order them by type
$templatelist = $this->ListTemplates($this->GetName());

foreach($templatelist as $template)
{
	// we create the admin links
	$tpl = new StdClass();

	$tpl->name = substr(strstr($template, '_'), 1);
	$tpl->prefix = str_replace($tpl->name,"",$template);

	$tpl->namelink = $this->CreateLink($id, 'edittemplate', $returnid, $tpl->name, array('template_name'=>$tpl->name, 'prefix'=>$tpl->prefix));
	$tpl->duplicatelink = $this->CreateLink($id, 'edittemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/copy.gif',lang('copy'),'','','systemicon'), array('from'=>$tpl->name, 'prefix'=>$tpl->prefix, 'new'=>true));
	$tpl->editlink = $this->CreateLink($id, 'edittemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon'), array('template_name'=>$tpl->name, 'prefix'=>$tpl->prefix));

	if(in_array($template, $default))
	{
		// the template is used as a default template
		$tpl->toggledefault = $gCms->variables['admintheme']->DisplayImage('icons/system/true.gif',lang('true'),'','','systemicon');
		$tpl->deletelink = '';
	}
	elseif($this->CheckPermission('Modify Download Manager Template'))
	{
		$tpl->toggledefault = $this->CreateLink($id, 'toggledefaulttemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/false.gif',lang('settrue'),'','','systemicon'), array('template_name'=>$tpl->name, 'prefix'=>$tpl->prefix));
		$tpl->deletelink = $this->CreateLink($id, 'deletetemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif',lang('delete'),'','','systemicon'), array('template_name'=>$template), $this->Lang('prompt_deletetemplate', $tpl->name));
	}
	else
	{
		$tpl->toggledefault = '';
		$tpl->deletelink = '';
	}

	// add put the template in the right template type
	$tmpprefix = substr($tpl->prefix, 0, -1);
	if(isset($orderedtemplates[$tmpprefix]))	$orderedtemplates[$tmpprefix][] = $tpl;

}

// we then display each type one by one... listtemplate.tpl is repeated for each type
foreach($templatetypes as $onetype)
{
	$addlink = $this->CreateLink($id, 'edittemplate', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('add'.$onetype.'template'),'','','systemicon'), array('prefix'=>$onetype.'_'), '', false, false, '') .' '. $this->CreateLink($id, 'edittemplate', $returnid, $this->Lang('add'.$onetype.'template'), array('prefix'=>$onetype.'_'), '', false, false, 'class="pageoptions"');
	$templates = array();
	// now that the templates are ordered by type, we can assign the row class
	// (this could have been done in the template itself though...)
	$rowclass = 'row1';
	foreach($orderedtemplates[$onetype] as $template)
	{
		$template->rowclass = $rowclass;
		$templates[] = $template;
		($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
	}
	$this->smarty->assign_by_ref('items', $templates);
	$this->smarty->assign('title', $this->Lang($onetype.'templates'));
	$this->smarty->assign('addlink', $addlink);
	#Display template
	echo $this->ProcessTemplate('listtemplate.tpl');
}
