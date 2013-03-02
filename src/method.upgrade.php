<?php
#-------------------------------------------------------------------------
# Module: DownloadManager 
# Version: 0.1 alpha
# Author: Szymon Łukaszczyk
# Project page: http://dev.cmsmadesimple.org/projects/downloadmanager/
#-------------------------------------------------------------------------
# Method: Upgrade
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/skeleton/
#
#-------------------------------------------------------------------------

/*
    For separated methods, you'll always want to start with the following
    line which check to make sure that method was called from the module
    API, and that everything's safe to continue:
*/ 
if (!isset($gCms)) exit;


/* After this, the code is identical to the code that would otherwise be
    wrapped in the Upgrade() method in the module body.
*/

$db =& $gCms->GetDb();
$taboptarray = array('mysql' => 'TYPE=MyISAM');

		$current_version = $oldversion;
		switch($current_version)
		{
			case "0.1":
			case "0.2":
			case "0.3":
				include( "method.uninstall.php" );
				include( "method.install.php" );
				break;
			case "0.4":
			case "0.4.1":
				$this->SetPreference("dir", "downloads/");
				//~ $this->SetPreference("template", file_get_contents("../modules/DownloadManager/templates/download.tpl"));
			case "0.5":
			case "0.7":
			case "0.8":
			case "0.8.2":
			case "0.8.3":
			case "0.8.4":
			case "0.8.5":
				$query = 'DROP VIEW '.cms_db_prefix().'module_downloadmanager_cat_file_temp;'; 
				$db->Execute($query);
			case "0.8.6":
				// we add the default templates
				$templatetypes = array('list', 'detail', 'form', 'email');
				foreach($templatetypes as $type){
					$template = $this->GetTemplateFromFile($type.'_default');
					$this->SetTemplate($type.'_default', $template, $this->GetName());
					$this->SetPreference('default_'.$type.'_template', $type.'_default');
				}
				
				// we transfer the templates from the module_downloadmanager_templates table
				$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_templates';
				$dbresult = $db->Execute($query);
				while ($dbresult && $row = $dbresult->FetchRow())
				{
					// and save them as "list" templates
					$this->SetTemplate('list_'.$row['template_name'], $row['content']);
				}
				// then we delete the old template table
				$query = 'DROP TABLE '.cms_db_prefix().'module_downloadmanager_templates';
				$dbresult = $db->Execute($query);
				$db->DropSequence( cms_db_prefix()."module_downloadmanager_templates_seq" );
				break;
			case "0.8.7":
				$query = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_categories ALTER priority SET DEFAULT 100';
				$dbresult = $db->Execute($query);
				$this->RemovePermission('Modify DownloadManager File');
				$this->RemovePermission('Modify Download Manager Category');
				$this->RemovePermission('Modify Download Manager Templates');
			case "0.8.8":
				$queries = array();
				$queries[] = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN accesstype int';
				$queries[] = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN template_detail VARCHAR(64)';
				$queries[] = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN template_form VARCHAR(64)';
				$queries[] = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN template_email VARCHAR(64)';
				foreach($queries as $query)	$db->Execute($query);

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

				$this->SetPreference("allowmultiple", true);
				$this->SetPreference("linkexpire", 2);
			case "0.9.0":
				$query = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN hierarchy text';
				$db->Execute($query);
				
				$dict = NewDataDictionary( $db );
				$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_downloadmanager_file_cat_agregator" );
				$dict->ExecuteSQLArray($sqlarray);
				$this->UpdateAssociationAgregation();
			case "0.9.2.2":
				$query[] = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_categories ADD COLUMN depth int DEFAULT 0 AFTER hierarchy';
			case "0.9.2.6":
				$query[] = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_categories ADD COLUMN alias  VARCHAR(64) AFTER name';
				$query[] = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN alias VARCHAR(64) AFTER name';
				foreach($query as $q)
					$db->Execute($q);

				// creating aliases for categories
				$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_categories';
				$dbresult = $db->Execute($query);
				while ($dbresult && $row = $dbresult->FetchRow())
				{
					$i=1;
					$alias = $n = $this->CreateAlias($row['name']);
					while(!$this->CheckAlias('categories', $alias))
						$alias = $n."-".$i++;
					$db->Execute('UPDATE '.cms_db_prefix().'module_downloadmanager_categories SET alias = ? WHERE category_id = ?'
						,array($alias,$row['category_id']));
				}

				// creating aliases for files
				$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files';
				$dbresult = $db->Execute($query);
				while ($dbresult && $row = $dbresult->FetchRow())
				{
					$i=1;
					$alias = $n = $this->CreateAlias($row['name']);
					while(!$this->CheckAlias('files', $alias))
						$alias = $n."-".$i++;
					$db->Execute('UPDATE '.cms_db_prefix().'module_downloadmanager_files SET alias = ? WHERE file_id = ?'
						,array($alias,$row['file_id']));
				}
					
				$this->UpdateHierarchyPositions();
			case "1.0-RC1":
			case "1.0-RC2":
			case "1.0-RC3":
			case "1.1":
				$this->SetPreference("scan", "downloads/");
				$this->SetPreference("scan_recurs", true);
			case "1.2":
			case "1.2.4":
				$query = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN counter INT NOT NULL DEFAULT 0 AFTER hierarchy ';
				$db->Execute($query);
            case "1.3":
				// Changing event names to without spaces
				$this->RemoveEvent('Download ManagerCategoryEdited');
				$this->RemoveEvent('Download ManagerCategoryAdded');
				$this->RemoveEvent('Download ManagerCategoryDeleted');
				$this->RemoveEvent('Download ManagerTemplateEdited');
				$this->RemoveEvent('Download ManagerTemplateAdded');
				$this->RemoveEvent('Download ManagerTemplateDeleted');
				$this->CreateEvent('DownloadManagerCategoryEdited');
				$this->CreateEvent('DownloadManagerCategoryAdded');
				$this->CreateEvent('DownloadManagerCategoryDeleted');
				$this->CreateEvent('DownloadManagerTemplateEdited');
				$this->CreateEvent('DownloadManagerTemplateAdded');
				$this->CreateEvent('DownloadManagerTemplateDeleted');
				// adding start column
				$dict = NewDataDictionary($db);
				$query = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN starts ' . $dict->ActualType(CMS_ADODB_DT) . ' AFTER expires ';
				$db->Execute($query);
				// adding search preference
				$this->SetPreference("expired_searchable", false);
				$query = 'ALTER TABLE '.cms_db_prefix().'module_downloadmanager_categories ADD COLUMN default_template ' . $dict->ActualType('C') . '(255) AFTER hierarchy_priority ';
				$db->Execute($query);
			case "1.4":
			case "1.4.1":
			case "1.4.2":
				// cleaning loose files in categories
				$query = 'SELECT fc.file_id FROM '.cms_db_prefix().'module_downloadmanager_files_category fc WHERE NOT EXISTS ( SELECT 1 FROM '.cms_db_prefix().'module_downloadmanager_files f WHERE f.file_id = fc.file_id )';
				$dbresult = $db->Execute($query);
				$loose = array();
				while ($dbresult && $row = $dbresult->FetchRow())
					$loose[] = (int) $row['file_id'];
				if(!empty($loose))
					$db->Execute('DELETE FROM '.cms_db_prefix().'module_downloadmanager_files_category WHERE file_id IN('.implode(',',$loose).')');
				
				// add preferences
				$this->SetPreference("dir_thumbs", "images/DownloadManagerThumbs/");
				$this->SetPreference("thumbs_auto", true);
				$this->SetPreference("thumbs_size", 80);
				// add thumb path field
				$dict = NewDataDictionary($db);
				$db->Execute('ALTER TABLE '.cms_db_prefix().'module_downloadmanager_files ADD COLUMN thumb_path ' . $dict->ActualType('X') . ' DEFAULT \'\' AFTER visible ');
			
				// create directory
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
            case "1.5":
				$this->SetPreference("admin_wysiwyg", true);
			default:
		}
		
		// put mention into the admin log
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('upgraded',$this->GetVersion()));

?>
