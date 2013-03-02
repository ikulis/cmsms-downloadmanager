<?php
if (!isset($gCms)) exit;

$rowclass = 'row1';
$category_id = -1 ;
global $config;
while ($dbresult && $row = $dbresult->FetchRow())
{

	//	var_dump($row);
	if($showheaders && $category_id !=  $row['category_id'])
	{
		// adding new category header
		$onerow = new stdClass();
		$onerow->itemtype = 'header';
		$onerow->itemlevel = (int) $row['depth'];
		$onerow->description = $row['category_desc'];
		$onerow->alias = $row['category_alias'];
		$onerow->name = '<span id="dmc_'.$onerow->alias.'" >'. $row['category_name'] . '</span>';
		if( !empty($subcategories[$row['category_id']]['items'] ) )
		{
			if($depth === false || $onerow->itemlevel - $base_depth < $depth )
				// subcategories which are in set depth
				foreach( $subcategories[$row['category_id']]['items'] as $kcat => $vcat)
					$subcategories[$row['category_id']]['items'][$kcat]['link'] =
						'<a href="'.$_SERVER['REQUEST_URI'].'#dmc_'.$vcat['alias'].'">'.$vcat['name'].'</a>';
			else
				// subcategories which are deeper than set depth
				foreach( $subcategories[$row['category_id']]['items'] as $kcat => $vcat)
					$subcategories[$row['category_id']]['items'][$kcat]['link'] =
						$this->CreateCategoryLink($id, $returnid, $vcat['name'], $vcat['alias'],$depth);
		}
		$onerow->subcategories = $subcategories[$row['category_id']]['items'];

		$entryarray[] = $onerow;
		$category_id =  $row['category_id'] ;
	}

	// adding item
	$onerow = new stdClass();
	foreach($row as $key=>$value)
		$onerow->$key = $value;

	$onerow->itemtype = 'file';
	$onerow->itemlevel = (int) $row['depth'];
	$onerow->id = (int) $row['file_id'];
	$filename = $row['name'].($row['ext']!=''?'.'. $row['ext']:'');
	$onerow->name = $filename;
	$onerow->description = $row['description'];
	$onerow->thumb =  !empty($row['thumb_path'])  ? $this->CreateDownloadLink($id,$returnid,'<img src="'. $config['uploads_url'].'/'.str_replace( DIRECTORY_SEPARATOR, '/', $row['thumb_path']) .'" />',$row['alias']) : '';
	$onerow->thumb_path =  !empty($row['thumb_path'])  ? $config['uploads_url'].'/'.str_replace( DIRECTORY_SEPARATOR, '/', $row['thumb_path']) : '';
	$onerow->counter = $row['counter'];
	$onerow->counterlocale = $this->lang('counternbr', array($row['counter']));
	$onerow->detail = $this->CreateDetailLink($id,$returnid,$this->lang('details'),$row['alias']);
	$onerow->download = $this->CreateDownloadLink($id,$returnid,$filename,$row['alias']);
	$onerow->href = $this->CreateDownloadLink($id,$returnid,$filename,$row['alias'],true);
	$onerow->size = $this->ToHRSize($row['size']);
	$onerow->categories = $this->CreateCategoryLinks($id,$returnid,$row['file_id'], $files_categories );
	$onerow->accesstypes = $this->GetAccessTypes($row['accesstype']);
	$onerow->rowclass = $rowclass;

	$entryarray[] = $onerow;
	($rowclass=="row1"?$rowclass="row2":$rowclass="row1");
}
//var_dump($entryarray);
//var_dump($template);
$this->smarty->assign_by_ref('items', $entryarray);
$this->smarty->assign('itemcount', count($entryarray));
$this->smarty->assign('nodownloads', $this->lang('nodownloads'));
$this->smarty->assign('backtolistlink', '<a href="javascript:history.back()">'.$this->lang('backtolist').'</a>');


?>
