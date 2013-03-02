<?php
if (!isset($gCms)) exit;
//var_dump($params);
$this->UpdateAssociationAgregation();
/*
 * PARSING selector
 *
 * Here come everything what has to do with selector,
 * if selector parameter is set the other parameters
 * are omnited
*/

if( empty( $params['selector'] ) )
	echo $this->DisplayErrorPage($id, $params, $returnid, $this->lang('nocategorysel'));

$showheaders = false;

// parsing the category selector
$categoryid  = html_entity_decode( $params['selector'] );

$sql_prefix = 'SELECT file_id FROM '.cms_db_prefix().'module_downloadmanager_files WHERE ';
$sql = $this->GetFileStdWhere().' AND '.$this->selectorToSQL($categoryid); // set the files where

$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files
		WHERE '.$sql.' ORDER BY name';

$files_categories = $this->GetFileCategories('file_id IN ('.$sql_prefix.$sql.')');

$dbresult = $db->Execute($query);

// also used by action default
include('function.assingfiles.php');

$templatename = '';
if( isset( $params['template'] ) )	$templatename = $params['template'];
// TEMPORARY : I ASSUME THE DEFAULT ACTION WILL BE THE LIST ACTION
if( $templatename != '' && $template = $this->GetTemplate('list_'.$templatename) )
{
	// the template exists, we're going to use it
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
