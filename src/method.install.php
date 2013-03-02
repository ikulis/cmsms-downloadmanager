<?php
#-------------------------------------------------------------------------
# Module: DownloadManager 
# Author: Szymon Łukaszczyk
# Project page: http://dev.cmsmadesimple.org/projects/downloadmanager/
#-------------------------------------------------------------------------
# Method: Install
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
if (!isset($gCms)) exit;


$db =& $gCms->GetDb();
$taboptarray = array('mysql' => 'TYPE=MyISAM');

$this->CreatePermission('Use DownloadManager', 'Use DownloadManager');

$dir = 'uploads/images/DownloadManagerThumbs/';
if( !is_dir('../'.$dir) )
{
	mkdir(str_replace('/', DIRECTORY_SEPARATOR, '../'.$dir));
}
if( !is_writable('../'.$dir) )
{
	$tmp = lang('errordirectorynotwritable'). ' > ' . $dir;
	return $tmp;
}

// Files
$dict = NewDataDictionary($db);
$flds =
	'file_id I KEY,
	accesstype I,
	name C(30) NOTNULL,
	alias C(64) UNIQUE,
	server_name X NOTNULL,
	ext C(8)  NOTNULL,
	type C(30)  NOTNULL,
	size I NOTNULL,
	hash C(32) NOTNULL ,
	description X ,
	template_detail C(64),
	template_form C(64),
	template_email C(64),
	hierarchy X,
	counter I NOTNULL DEFAULT 0,
	created ' . CMS_ADODB_DT . ',
	expires ' . CMS_ADODB_DT . ',
    starts ' . CMS_ADODB_DT . ',
	visible I,
	thumb_path X DEFAULT \'\'
';
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_downloadmanager_files',$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix().'module_downloadmanager_files_seq');
$this->CreateEvent('DownloadManagerFileEdited');
$this->CreateEvent('DownloadManagerFileAdded');
$this->CreateEvent('DownloadManagerFileDeleted');

// Categories
$dict = NewDataDictionary($db);
$flds =
	"category_id I KEY ,
	name C(30) NOTNULL,
	alias C(64) UNIQUE,
	description C(255) ,
	parent_id I NOTNULL,
	hierarchy C(255),
	depth I DEFAULT 0,
	long_name X,
	priority  C(5) DEFAULT 100,
	hierarchy_priority X, 	
	default_template C(255) , 	
	create_date " . CMS_ADODB_DT . ",
	modified_date " . CMS_ADODB_DT . "
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_downloadmanager_categories',$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix().'module_downloadmanager_categories_seq');
$this->CreateEvent('DownloadManagerCategoryEdited');
$this->CreateEvent('DownloadManagerCategoryAdded');
$this->CreateEvent('DownloadManagerCategoryDeleted');

# Setup General category
$catid = $db->GenID(cms_db_prefix()."module_downloadmanager_categories_seq");
$query = 'INSERT INTO '.cms_db_prefix().'module_downloadmanager_categories (category_id, name, parent_id, create_date, modified_date, alias) VALUES ('.$catid.',\'General\',-1,'.$db->DBTimeStamp(time()).','.$db->DBTimeStamp(time()).',\'general\')';
$db->Execute($query);
$this->UpdateHierarchyPositions();

// Associations
$dict = NewDataDictionary($db);
$flds =
	"
	file_id I KEY,
    category_id I KEY,
	item_order I NOTNULL
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_downloadmanager_files_category',$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// Downloads
$dict = NewDataDictionary($db);
$flds =
	"download_id I KEY ,
	file_id I NOTNULL,
	user_type I,
	user_id I,
	hashkey C(255),
	downloaded " . CMS_ADODB_DT . "
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_downloadmanager_downloads',$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix().'module_downloadmanager_downloads_seq');

// Users
$dict = NewDataDictionary($db);
$flds =
	"user_id I NOTNULL,
	firstname C(64),
	lastname C(64),
	email C(128),
	mailinglist L
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_downloadmanager_users',$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix().'module_downloadmanager_users_seq');

// Mapping FEU groups to files
$dict = NewDataDictionary($db);
$flds =
	"file_id I NOTNULL,
	group_id I NOTNULL
";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_downloadmanager_filegroups',$flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);

// we add the default templates
$templatetypes = array('list', 'detail', 'form', 'email');
foreach($templatetypes as $type){
	$this->SetTemplate($type.'_default', $this->GetTemplateFromFile($type.'_default'), $this->GetName());
	$this->SetPreference('default_'.$type.'_template', $type.'_default');
}
$this->SetTemplate('default_download_template', $this->GetTemplateFromFile('download'));

$this->CreateEvent('DownloadManagerTemplateEdited');
$this->CreateEvent('DownloadManagerTemplateAdded');
$this->CreateEvent('DownloadManagerTemplateDeleted');

// create a preferences
$this->SetPreference("dir", "downloads/");
$this->SetPreference("allowmultiple", true);
$this->SetPreference("linkexpire", 2);
$this->SetPreference("scan", "downloads/");
$this->SetPreference("scan_recurs", true);
$this->SetPreference("expired_searchable", false);
$this->SetPreference("dir_thumbs", "images/DownloadManagerThumbs/");
$this->SetPreference("thumbs_auto", true);
$this->SetPreference("thumbs_size", 80);
$this->SetPreference("admin_wysiwyg", true);

// put mention into the admin log
$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('installed',$this->GetVersion()));

?>
