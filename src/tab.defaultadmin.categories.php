<?php

if (!isset($gCms)) exit;

// check for permissions
if (!$this->CheckPermission('Use DownloadManager'))
{
	echo $this->ShowErrors($this->Lang('needpermission', array('Use DownloadManager')));
	return;
}

// Put together a list of current categories...
$entryarray = array();
$category_file_nb = $this->GetCategoryFileNb();

$query = "SELECT * FROM ".cms_db_prefix()."module_downloadmanager_categories ORDER BY hierarchy_priority";
$dbresult = $db->Execute($query);

$rowclass = 'row1';

while ($dbresult && $row = $dbresult->FetchRow())
{
	$onerow = new stdClass();

	$depth = count(explode('\.', $row['hierarchy']));
	$fnb = isset($category_file_nb[$row['category_id']])?(int)$category_file_nb[$row['category_id']]:0;
	
	$onerow->id = $row['category_id'];
	$onerow->alias = $row['alias'];
	$onerow->name = str_repeat('&nbsp;&gt;&nbsp;', $depth-1).$this->CreateLink($id, 'editcategory', $returnid, $row['name'], array('category_id' =>$row['category_id']));
	$onerow->priority = $row['priority']+0;
	$onerow->editlink = $this->CreateLink($id, 'editcategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon'), array('category_id'=>$row['category_id']));
	$onerow->deletelink = $this->CreateLink($id, 'deletecategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon'), array('category_id'=>$row['category_id']), lang('deleteconfirm',$row['name']));
	$onerow->reorderlink = '';
	$onerow->reorderlink = $this->CreateLink($id, 'reorder', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/reorder.gif', lang('reorder'),'','','systemicon'), array('category_id'=>$row['category_id']));
	$onerow->filenb = $fnb;
	$onerow->filenbtxt = $this->Lang('filenb',$fnb);

	$onerow->rowclass = $rowclass;

	$entryarray[] = $onerow;

	($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}

$this->smarty->assign_by_ref('items', $entryarray);
$this->smarty->assign('itemcount', count($entryarray));

#Setup links
$this->smarty->assign('addlink', $this->CreateLink($id, 'addcategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/newfolder.gif', $this->Lang('addcategory'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'editcategory', $returnid, $this->Lang('addcategory'), array(), '', false, false, 'class="pageoptions"'));
$this->smarty->assign('reordercategorylink', $this->CreateLink($id, 'reordercategory', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/reorder.gif', lang('reorder'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'reordercategory', $returnid, lang('reorder'), array(), '', false, false, 'class="pageoptions"'));

$this->smarty->assign('categorytext', $this->Lang('category'));
$this->smarty->assign('aliastext', $this->Lang('aliaslabel'));
#Display template
echo $this->ProcessTemplate('listcategory.tpl');

?>