<?php
if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}


$category_id = '';
if (isset($params['category_id']))
{
	$category_id = $params['category_id'];
}

// Get the category details
$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_categories WHERE category_id = ?';
$row = $db->GetRow( $query, array( $category_id ) );

	//Reset all categories using this parent to have no parent (-1)
	$query = 'UPDATE '.cms_db_prefix().'module_downloadmanager_categories SET parent_id=?, modified_date='.$db->DBTimeStamp(time()).' WHERE parent_id=?';
	$db->Execute($query, array(-1, $category_id));

	//Now remove the category
	$query = "DELETE FROM ".cms_db_prefix()."module_downloadmanager_categories WHERE category_id = ?";
	$db->Execute($query, array($category_id));

//And remove it from any articles
//~ $query = "UPDATE ".cms_db_prefix()."module_news SET news_category_id = -1 WHERE news_category_id = ?";
	//~ $db->Execute($query, array($catid));

$this->UpdateHierarchyPositions();

//Update search index
$this->SearchDeleteCategory($category_id);

// add more fields as needed to the send event

@$this->SendEvent('DownloadManagerCategoryDeleted', array('category_id' => $category_id));

$params = array('tab_message'=> 'categorydeleted', 'active_tab' => 'categories');
$this->Redirect($id, 'defaultadmin', $returnid, $params);



?>