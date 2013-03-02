<?php
if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}
if (isset($params['cancel']))
{
	$params = array('active_tab' => 'categories');
	$this->Redirect($id, 'defaultadmin', $returnid,$params);
}


$category_id = (isset($params['category_id']))? intval($params['category_id']) : -1;
$description = (isset($params['description']))? $params['description']:'';
$parent_id = (isset($params['parent']))?intval($params['parent']):'';
$alias = (isset($params['alias']))?$this->CreateAlias($params['alias']):'';
$default_template= (isset($params['default_template']))?$params['default_template']:'';
$name = '';

if (isset($params['category_id']))
{
	// we are editting category
	if (isset($params['name']))
	{
		// saving changes
		$name = $params['name'];

		// checking alias
		if(!$this->CheckAlias('files', $alias))
			echo $this->ShowErrors($this->Lang('wrongalias'));
		else if ($name != '' )
		{
			$time = $db->DBTimeStamp(time());
			$query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_categories SET name = ?, description = ?, parent_id = ?, modified_date=? , alias=?, default_template=? WHERE category_id = ?';
			$db->Execute($query, array($name, $description, $parent_id, $time, $alias,$default_template, $category_id));
			$this->UpdateHierarchyPositions();
			// update search index
			$this->SearchDeleteCategory($category_id);
			$this->SearchAddCategory( (int) $category_id, $alias.' '.$name.' '.$description);
			// add more fields as needed to the send event
			@$this->SendEvent('DownloadManagerCategoryEdited', array('category_id' => $category_id, 'name' => $name));
			$params = array('tab_message'=> 'categoryupdated', 'active_tab' => 'categories');
			$this->Redirect($id, 'defaultadmin', $returnid, $params);
		}
		else
		{
			echo $this->ShowErrors($this->Lang('nonamegiven'));
		}
	}
	// getting category data for edition
	$query = 'select alias, category_id, name, description, parent_id,default_template from '.cms_db_prefix() .'module_downloadmanager_categories WHERE category_id= ?';
	$row = $db->GetRow($query, array($category_id));

	if ($row)
	{
		$name = $row['name'];
		$description = $row['description'];
		$parent_id = $row['parent_id'];
		$alias = $row['alias'];
		$default_template = $row['default_template'];
	}
	$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'category_id', $category_id));
	

}
else if (isset($params['name']))
{
	// we are adding new category
	$name = $params['name'];
	
	// adding alias
	$i = 1;
	$alias = $n = $this->CreateAlias($name);
	while(!$this->CheckAlias('categories', $alias))
		$alias = $n."-".$i++;
		
	$catid = $db->GenID(cms_db_prefix()."module_downloadmanager_categories_seq");
	$time = $db->DBTimeStamp(time());
	$query = 'INSERT INTO '.cms_db_prefix().'module_downloadmanager_categories (category_id, name, alias, description, parent_id, create_date, modified_date,default_template) VALUES (?,?,?,?,?,'.$time.','.$time.',?)';
	$parms = array($catid,$name,$alias,$description,$parent_id,$default_template);
	$db->Execute($query, $parms);
	$this->UpdateHierarchyPositions();
	$this->SearchAddCategory( (int) $catid, $alias.' '.$name.' '.$description);
	@$this->SendEvent('DownloadManagerCategoryAdded', array('category_id' => $category_id, 'name' => $name));

	$params = array('tab_message'=> 'categoryadded', 'active_tab' => 'categories');
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}


// stating form
$this->smarty->assign('startform', $this->CreateFormStart($id, 'editcategory', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());

// inputs
$this->smarty->assign('inputname', $this->CreateInputText($id, 'name', $name, 30, 255));
$this->smarty->assign('inputalias', $this->CreateInputText($id, 'alias', $alias, 30, 255));
$this->smarty->assign('inputdescription', $this->CreateInputText($id, 'description', $description, 30, 255));
$this->smarty->assign('inputparent_id',  $this->CreateParentDropdown($id, $category_id, $parent_id));
$this->smarty->assign('inputdefault_template', $this->CreateInputDropdown($id, 'default_template',$this->GetTemplatesCombo("list"),-1,$default_template));

$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
// labels
$this->smarty->assign('namelabel', $this->Lang('namelabel'));
$this->smarty->assign('aliaslabel', $this->Lang('aliaslabel'));
$this->smarty->assign('descriptionlabel', $this->Lang('descriptionlabel'));
$this->smarty->assign('parent_idlabel', $this->Lang('parent_idlabel'));
$this->smarty->assign('default_templatelabel', $this->Lang('defaulttemplate'));


#Display template
echo $this->ProcessTemplate('editcategory.tpl');
?>

