<?php

if (!isset($gCms)) exit;

// check for permissions
if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}

$tab ="";
if(isset($params['active_tab']))
	$tab = $params['active_tab'];

echo $this->StartTabHeaders();
  echo $this->SetTabHeader("files",$this->Lang("filestabheader"),($tab=="files"));
  echo $this->SetTabHeader("categories",$this->Lang("categoriestabheader"),($tab=="categories"));
  echo $this->SetTabHeader("templates", lang("templates"),($tab=="templates"));
  echo $this->SetTabHeader("preferences", lang("preferences"),($tab=="preferences"));
echo $this->EndTabHeaders();

$this->smarty->assign('tableIDColHeader', 'ID');
$this->smarty->assign('tableNameColHeader', lang('name'));
$this->smarty->assign('tableDescriptionColHeader', lang('description'));
$this->smarty->assign('tableDefaultColHeader', lang('default'));
$this->smarty->assign('tableActionsHeader', $this->lang('actions'));

echo $this->StartTabContent();
echo $this->StartTab('files', $params);
	include('tab.defaultadmin.files.php');
echo $this->EndTab();
echo $this->StartTab('categories', $params);
	include('tab.defaultadmin.categories.php');
echo $this->EndTab();
echo $this->StartTab('templates', $params);
	include('tab.defaultadmin.templates.php');
echo $this->EndTab();
echo $this->StartTab('preferences', $params);
	include('tab.defaultadmin.preferences.php');
echo $this->EndTab();
echo $this->EndTabContent();
?>
