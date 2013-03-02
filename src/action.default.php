<?php
	/*---------------------------------------------------------
	   DisplayModuleOutput($id, $params, $returnid, $message)
	   NOT PART OF THE MODULE API

	   This is an example of a simple method to display
	   something where a page or template has a tag calling
	   this module.
	   
	   Note that it uses a template, and is thus very powerful,
	   even if it's simple.
	  ---------------------------------------------------------*/

/*
    For separated methods, you'll always want to start with the following
    line which check to make sure that method was called from the module
    API, and that everything's safe to continue:
*/ 
if (!isset($gCms)) exit;
//var_dump($params);

$query = '';
$dbresult ='';
$entryarray = array();
$category_id = '' ;
$category_level = '';
$depth = isset( $params['depth'] )?(int)$params['depth']:false;

$query = '';
$queryprefix = 	'SELECT fc.category_id, fc.file_id, c.hierarchy, c.name AS category_name ,
		c.long_name AS category_long_name ,	c.depth, c.alias AS category_alias,
		c.description AS category_desc,
		f.*
		FROM '.cms_db_prefix().'module_downloadmanager_files_category fc
		LEFT JOIN '.cms_db_prefix().'module_downloadmanager_categories c USING(category_id)
		LEFT JOIN '.cms_db_prefix().'module_downloadmanager_files f USING(file_id)
		WHERE f.visible = 1 AND '.$this->GetFileStdWhere('f');
$query_depth = '';
$showheaders = true;

$base_depth = 0;

// get subcategories tree structure
$subcategories = $this->GetSubCategories();

/*
 * PARSING CATEGORY AND CATEGORYID
 *
 * if both are set categoryid have higher priority
*/
if( isset( $params['alias'] ) || isset( $params['categoryid'] ) )
{
	$dbresult = '';
	$query = 'SELECT category_id, hierarchy, depth, alias, default_template
		FROM '.cms_db_prefix().'module_downloadmanager_categories
		WHERE ';

	// choosing where condition
	if( isset( $params['categoryid'] ) )
		$dbresult = $db->Execute($query.' category_id = ? ',array((int)$params['categoryid']));
	else
		$dbresult = $db->Execute($query.' alias = ? ',array($params['alias']));

	// fetching category
	if ($dbresult && $row = $dbresult->FetchRow())
	{
		$category_id = (int) $row['category_id'];
		$category_depth = (int) $row['depth'];
		$category_hierarchy = $row['hierarchy'];
		$category_template = $row['default_template'];
	}

	if($category_id == '')
		echo $this->DisplayErrorPage($id, $params, $returnid, $this->lang('wrongcategory'));
	// we have category specified

	$query = $queryprefix.
		'AND c.hierarchy LIKE \'%'.str_pad($category_id, 5, '0', STR_PAD_LEFT).'%\'';

	// create link to upper category
	$t = &$subcategories[$category_id];
	$top='';
	if(!is_null($t['parent']))
	{
		//we have top category
		$top = $this->CreateCategoryLink($id, $returnid, $t['parent']['name'], $t['parent']['alias'],$depth);
	}
	else
	{
		// no upper category creating link to root
		$top = $this->CreateCategoryLink($id, $returnid,"","",$depth);
	}
	
	$this->smarty->assign('topcategorylink', $top);
}
else
{
	// all categories are sellected
	$query = $queryprefix;
	$category_depth = 0;
}

// adding depth condition

if( $depth !== false)
{
	// add depth condition
	$query_depth = ' AND '.($category_depth+$depth).' >= c.depth ';
}

// merging queries
$query .= $query_depth.'
		ORDER BY c.hierarchy_priority, fc.item_order ';



$dbresult = $db->Execute($query);

// also used by action selector
include('function.assingfiles.php');

$templatename = '';
if( isset( $params['template'] ) )	
    $templatename = 'list_'.$params['template']; // default is list action

if( $templatename != '' && $template = $this->GetTemplate($templatename))
{
	// the template exists, we're going to use it
	
}
else if( !empty($category_template) && $template = $this->GetTemplate($category_template))
{
	// using the default category template because it is set
}
else
{
	// no template specified, or template doesn't exist... we get the default one
	$templatename = $this->GetPreference('default_list_template', '');
	$template = $this->GetTemplate($templatename);
}
#Display template
echo $this->ProcessTemplateFromData($template);
?>
