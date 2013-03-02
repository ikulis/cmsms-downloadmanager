<?php
if (!isset($gCms)) exit;
if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}

if (isset($params['cancel']) )
{
	$params = array('active_tab' => 'categories');
	$this->Redirect($id, 'defaultadmin', $returnid, $params );
}
else if ( !isset($params['priority']) &&  isset($params['category_id']))
{

	#Put together a list of current categories...
	$entryarray = array();
	
	$query = "SELECT fc.file_id, f.name, fc.item_order FROM ".cms_db_prefix()."module_downloadmanager_files_category fc LEFT JOIN ".cms_db_prefix()."module_downloadmanager_files f ON fc.file_id = f.file_id  WHERE fc.category_id = ? ORDER BY fc.item_order, fc.file_id; ";
//~ echo $query;
	$dbresult = $db->Execute($query, array($params['category_id']) );
	//~ $dbresult = $db->Execute($query);
	
	$rowclass = 'row1';
	//~ var_dump($dbresult);
	while ($dbresult && $row = $dbresult->FetchRow())
	{
		$onerow = new stdClass();
	
		$depth = count(explode('\.', $row['hierarchy']));
	
		$onerow->id = $row['file_id'];
		$onerow->name = str_repeat('&nbsp;&gt;&nbsp;', $depth-1).$this->CreateLink($id, 'editfile', $returnid, $row['name'], array('file_id' =>$row['file_id']));
		$onerow->priority = $this->CreateInputText($id, 'priority['.$row['file_id'].']', (string)(int)$row['item_order'] , 3, 3);
		$onerow->rowclass = $rowclass;
		$entryarray[] = $onerow;
	
		($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
	}
	$this->smarty->assign('startform', $this->CreateFormStart($id, 'reorder', $returnid, 'post'));
	$this->smarty->assign('endform', $this->CreateFormEnd());
	$this->smarty->assign_by_ref('items', $entryarray);
	$this->smarty->assign('itemcount', count($entryarray));
	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));
	$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'category_id', $params['category_id']));
	$this->smarty->assign('prioritytext', $this->lang('reorder_priority'));
	$this->smarty->assign('itemtext', lang("files"));
	#Setup links
	
	#Display template
	echo $this->ProcessTemplate('reorderitem.tpl');
}
else if ( isset($params['priority']) &&  isset($params['category_id']))
{
	foreach( $params['priority'] as $key => $var )
	{
		$query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_files_category  SET item_order= ? WHERE file_id = ? AND category_id = ?';
		$db->Execute($query, array(((int)$var)%100,(int)$key,(int)$params['category_id']));
	}
	$params = array('active_tab' => 'categories');
	$this->Redirect($id, 'defaultadmin', $returnid, $params );
}
?>