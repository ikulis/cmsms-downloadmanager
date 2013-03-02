<?php

if (!isset($gCms)) exit;
// check for permissions
if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}
$files_categories = $this->GetFileCategories();

$params['selector'] = $this->cleanSelector( html_entity_decode( $params['selector'] ));
$selector_sql = $this->selectorToSQL($params['selector']);
$selector_verbose = '';
if(!empty($selector_sql)){
    $replace = array();
    $categories = $this->GetCategories();
    foreach( $categories as $v )
        $replace[ $v['category_id']] = "'".$v['name']."'";
    $replace['|'] = ' OR ';
    $replace['&'] = ' AND ';
    $selector_verbose = strtr( $params['selector'] , $replace);
}

$params['filter'] = html_entity_decode( $params['filter'] ) ;
$filter = $db->qstr('%'.$params['filter'].'%' ,get_magic_quotes_gpc()) ;

$where_sql = $selector_sql;
if(strlen($filter)>4){
    if(!empty($where_sql) )
        $where_sql .=' AND ';
    $where_sql .= ' ( name LIKE '.$filter.
        ' OR ext LIKE '.$filter.
        ' OR description LIKE '.$filter.' )';
}

//Load files
$entryarray = array();
$query = 'select visible, file_id, alias,name, starts,expires, ext, description, counter from '.cms_db_prefix() .'module_downloadmanager_files '.
(!empty($where_sql)?' WHERE '.$where_sql.' ':'').'order by name asc';
$dbresult = $db->Execute($query);
$rowclass = 'row1';

$trueicon = $gCms->variables['admintheme']->DisplayImage('icons/system/true.gif', '','','','systemicon');
$falseicon = $gCms->variables['admintheme']->DisplayImage('icons/system/false.gif', '','','','systemicon');
while ($dbresult && $row = $dbresult->FetchRow())
{
	$onerow = new stdClass();
	$onerow->id = $row['file_id'];
//	$onerow->server_name = $row['server_name'];
//	$onerow->ext = $row['ext'];
//	$onerow->size = $row['size'];
//	$onerow->hash = $row['hash'];
	$onerow->description = $row['description'];
//	$onerow->created = $row['created'];
//	$onerow->expires = $row['expires'];
	$onerow->visible = $this->CreateLink($id, 'editfile', $returnid, $row['visible']?$trueicon:$falseicon, array('file_id'=>$row['file_id'], 'changevisibility'=>1,'visible'=>$row['visible']?0:1, 'selector'=>$params['selector']));
	$onerow->name = $this->CreateLink($id, 'editfile', $returnid, $row['name'].($row['ext']!=''?'.'. $row['ext']:''), array('file_id'=>$row['file_id'], 'selector'=>$params['selector']));
	$onerow->rowclass = $rowclass;
	$onerow->alias = $row['alias'];
	$onerow->counter = $row['counter'];
	
    if(empty($row['starts']) && empty($row['expires']))
        $onerow->available = $this->Lang('alltime');
    else
    {
        $row['starts'] = !empty($row['starts']) ? $row['starts'] : '...';
        $row['expires'] = !empty($row['expires']) ? $row['expires'] : '...';
        $onerow->available = $row['starts'].' - '.$row['expires'] ;
    }
	$onerow->changelink = $this->CreateLink($id, 'changefile', $returnid, '<img class="systemicon" src="'.$gCms->config['root_url'].'/modules/DownloadManager/images/icons/transport.png" alt="'. $this->lang('filechange').'" title="'. $this->lang('filechange').'"/>', array('file_id'=>$row['file_id'], 'name'=> base64_encode($row['name']), 'alias'=> base64_encode($row['alias']) , 'selector'=>$params['selector']));
	$onerow->incategories = !array_key_exists((int) $row['file_id'], $files_categories )?0:$files_categories[(int) $row['file_id']]['count'];
//	$onerow->categories = $this->CreateCategoryLinks($id, $returnid, $row['file_id'] , $files_categories );
	$onerow->editlink = $this->CreateLink($id, 'editfile', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/edit.gif', lang('edit'),'','','systemicon'), array('file_id'=>$row['file_id'], 'selector'=>$params['selector']));
	$onerow->deletelink = $this->CreateLink($id, 'deletefile', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon'), array('file_id'=>$row['file_id'], 'selector'=>$params['selector']), lang('deleteconfirm',$row['name'].($row['ext']!=''?'.'. $row['ext']:'')));
	$entryarray[] = $onerow;
	($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}

$this->smarty->assign_by_ref('items', $entryarray);
$this->smarty->assign('itemcount', count($entryarray));
$this->smarty->assign('addlink', $this->CreateLink($id, 'addfile', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/newobject.gif', $this->Lang('addfile'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'addfile', $returnid, $this->Lang('addfile'), array(), '', false, false, 'class="pageoptions"'));
$this->smarty->assign('scanlink', $this->CreateLink($id, 'scanfornewfile', $returnid, $gCms->variables['admintheme']->DisplayImage('icons/system/view.gif', $this->Lang('scanfornewfile'),'','','systemicon'), array(), '', false, false, '') .' '. $this->CreateLink($id, 'scanfornewfiles', $returnid, $this->Lang('scanfornewfiles'), array(), '', false, false, 'class="pageoptions"'));
$this->smarty->assign('tablevisibleheader', $this->Lang('visiblelabel'));
$this->smarty->assign('tablecounterheader', $this->Lang('counterlabel'));
$this->smarty->assign('tableavailablityheader', $this->Lang('availablity'));
$this->smarty->assign('category_selector', $this->Lang('selector'));
$this->smarty->assign('noselector', $this->Lang('noselector'));
$this->smarty->assign('nofilter', $this->Lang('nofilter'));
$this->smarty->assign('current', $this->Lang('current'));
$this->smarty->assign('filter', $this->Lang('filter'));
$this->smarty->assign('verbose', $this->Lang('verbose'));
$this->smarty->assign('value_category_selector', $params['selector']);
$this->smarty->assign('value_filter', $params['filter']);
$this->smarty->assign('value_category_selector_verbose', $selector_verbose);
$this->smarty->assign('input_category_selector', $this->CreateInputText($id, 'selector', $params['selector'], 10, 50));
$this->smarty->assign('input_filter', $this->CreateInputText($id, 'filter', $params['filter'], 10, 50));
$this->smarty->assign('input_category_selector_url', $this->CreateLink( $id, 'defaultadmin', $returnid , '',  $params , '', true));

#Display template
echo $this->ProcessTemplate('listfile.tpl');

?>