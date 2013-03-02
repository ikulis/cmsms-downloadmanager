<?php
#-------------------------------------------------------------------------
# Module: DownloadManager 
# Version: 0.1 alpha
# Aythor: Szymon Łukaszczyk
# Project page: http://dev.cmsmadesimple.org/projects/downloadmanager/
#-------------------------------------------------------------------------
# Method: Uninstall
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
    wrapped in the Uninstall() method in the module body.
*/

		$db =& $gCms->GetDb();
		
		// remove the database table
		$dict = NewDataDictionary( $db );
		$sqlarray =$dict->DropTableSQL( cms_db_prefix()."module_downloadmanager_files" );
		$dict->ExecuteSQLArray($sqlarray);
		$db->DropSequence( cms_db_prefix()."module_downloadmanager_files_seq" );
		
		$dict = NewDataDictionary( $db );
		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_downloadmanager_categories" );
		$dict->ExecuteSQLArray($sqlarray);
		$db->DropSequence( cms_db_prefix()."module_downloadmanager_categories_seq" );
		
		$dict = NewDataDictionary( $db );
		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_downloadmanager_files_category" );
		$dict->ExecuteSQLArray($sqlarray);

		$dict = NewDataDictionary( $db );
		$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_downloadmanager_templates" );
		$dict->ExecuteSQLArray($sqlarray);
		$db->DropSequence( cms_db_prefix()."module_downloadmanager_templates_seq" );

		// remove the templates
		$this->DeleteTemplate();
		
		// remove the permissions
		$this->RemovePermission('Use DownloadManager');
		$this->RemovePermission('Modify DownloadManager File');
		
		// remove the event
		$this->RemoveEvent('DownloadManagerFileEdited');
		$this->RemoveEvent('DownloadManagerFileAdded');
		$this->RemoveEvent('DownloadManagerFileDeleted');
        $this->RemoveEvent('DownloadManagerCategoryEdited');
        $this->RemoveEvent('DownloadManagerCategoryAdded');
        $this->RemoveEvent('DownloadManagerCategoryDeleted');
        $this->RemoveEvent('DownloadManagerTemplateEdited');
        $this->RemoveEvent('DownloadManagerTemplateAdded');
        $this->RemoveEvent('DownloadManagerTemplateDeleted');

        // remove the event
		$this->RemovePreference("dir");
		$this->RemovePreference("allowmultiple");
        $this->RemovePreference("linkexpire");
        $this->RemovePreference("scan");
        $this->RemovePreference("scan_recurs");
        $this->RemovePreference("expired_searchable");
		$this->RemovePreference("dir_thumbs");
		$this->RemovePreference("thumbs_auto");
		$this->RemovePreference("thumbs_size");
        $this->RemovePreference("admin_wysiwyg");
		
		// put mention into the admin log
		$this->Audit( 0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));

?>
