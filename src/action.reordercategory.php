<?php
if (!isset($gCms)) exit;
if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}
if (isset($params['cancel']) )
{
	$params = array('tab' => 'categories');
	$this->Redirect($id, 'defaultadmin', $returnid, $params );
}
else if ( !isset($params['priority']) )
{

	#Put together a list of current categories...
	$entryarray = array();
	
	$query = "SELECT * FROM ".cms_db_prefix()."module_downloadmanager_categories ORDER BY hierarchy_priority";
	$dbresult = $db->Execute($query);
	
	$rowclass = 'row1';
	
	while ($dbresult && $row = $dbresult->FetchRow())
	{
		$onerow = new stdClass();
	
		$depth = count(split('\.', $row['hierarchy']));
	
		$onerow->id = $row['category_id'];
		$onerow->name = str_repeat('&nbsp;&gt;&nbsp;', $depth-1).$this->CreateInputText($id, 'priority['.$row['category_id'].']', (string)((int)$row['priority'] - 100) , 3, 3). ' '.$this->CreateLink($id, 'editcategory', $returnid, $row['name'], array('category_id' =>$row['category_id']));
		$onerow->rowclass = $rowclass;
		$entryarray[] = $onerow;
	
		($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
	}
	$this->smarty->assign('startform', $this->CreateFormStart($id, 'reordercategory', $returnid, 'post'));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	$this->smarty->assign_by_ref('items', $entryarray);
	$this->smarty->assign('itemcount', count($entryarray));
	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
	$this->smarty->assign('prioritytext', $this->lang('reorder_priority'));
	$this->smarty->assign('categorytext', $this->Lang("categoriestabheader"));
	#Setup links
	
	#Display template
	echo $this->ProcessTemplate('reordercategory.tpl');
}
else
{
	foreach( $params['priority'] as $key => $var )
	{
		$query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_categories  SET priority = ? WHERE category_id = ?';
		$db->Execute($query, array(((int)$var)%100+100,(int)$key));
	}
	$this->UpdateHierarchyPositions();
	$params = array('active_tab' => 'categories');
	$this->Redirect($id, 'defaultadmin', $returnid, $params );
}
?>