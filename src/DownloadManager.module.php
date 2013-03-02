<?php

#-------------------------------------------------------------------------
# Module: DownloadManager 
# Version: 1.0-RC3
# Author: Szymon Łukaszczyk
# Project page: http://dev.cmsmadesimple.org/projects/downloadmanager/
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/skeleton/
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

// setting the access constants
define('DMNR_BY_FREE', 0);
define('DMNR_BY_FEU', 1);
define('DMNR_BY_MAIL', 2);
define('DMNR_BY_BOTH', 3);

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
require dirname(__FILE__).'/function.mine_content_type.php';

class DownloadManager extends CMSModule
{

    function GetName()
    {
	return 'DownloadManager';
    }

    function GetFriendlyName()
    {
	return $this->Lang('friendlyname');
    }

    function GetVersion()
    {
	return '1.5.2';
    }

    function GetHelp()
    {
	return $this->Lang('help');
    }

    function GetAuthor()
    {
	return 'Szymon Łukaszczyk';
    }

    function GetAuthorEmail()
    {
	return 'szymon.lukaszczyk@gmail.com';
    }

    function GetChangeLog()
    {
	return $this->Lang('changelog');
    }

    function IsPluginModule()
    {
	return true;
    }

    function HasAdmin()
    {
	return true;
    }

    function GetAdminSection()
    {
	return 'content';
    }

    function GetAdminDescription()
    {
	return $this->Lang('moddescription');
    }

    function VisibleToAdminUser()
    {
	return $this->CheckPermission('Use DownloadManager');
    }

    function GetDependencies()
    {
	return array();
    }

    function MinimumCMSVersion()
    {
	return "1.4";
    }

    function SetParameters()
    {
    // registring new content type
    // $this->RegisterContentType('DownloadCategory',
    // dirname(__FILE__).DIRECTORY_SEPARATOR.'contenttype.downloadcategory.php',
    // $this->Lang('category_page'));

	$this->RestrictUnknownParams(); // only parameters specified here

	// smarty tag parameters
	$this->CreateParameter('action', 'default', $this->lang('help_param_action'));
	$this->CreateParameter('selector', '', $this->lang('help_param_selector'));
	$this->CreateParameter('template', '', $this->lang('help_param_template'));
	$this->CreateParameter('alias', '', $this->lang('help_param_alias'));
	$this->CreateParameter('categoryid', '', $this->lang('help_param_categoryid'));
	$this->CreateParameter('depth', '', $this->lang('help_param_depth'));
	$this->SetParameterType('selector',CLEAN_STRING);
	$this->SetParameterType('action',CLEAN_STRING);
	$this->SetParameterType('template', CLEAN_STRING);
	$this->SetParameterType('alias', CLEAN_STRING);
	$this->SetParameterType('depth', CLEAN_INT);

	$this->SetParameterType('hashkey', CLEAN_STRING);

	// form, parameters
	$this->SetParameterType('file_id', CLEAN_INT);
	$this->SetParameterType(CLEAN_REGEXP.'/sm_.*/',CLEAN_STRING); // for frontend forms with mail sending

	$aliaspatt = '[a-zA-Z0-9\-\_]';

	$this->RegisterRoute('/download\/(?P<returnid>[0-9]+)$/',
	    array('action'=>'default'));
	$this->RegisterRoute('/download\/(?P<returnid>[0-9]+)\/(?P<depth>[0-9]+)$/',
	    array('action'=>'default'));
	$this->RegisterRoute('/download\/category\/(?P<alias>'.$aliaspatt.'+)\/(?P<returnid>[0-9]+)$/',
	    array('action'=>'default'));
	$this->RegisterRoute('/download\/category\/(?P<alias>'.$aliaspatt.'+)\/(?P<returnid>[0-9]+)\/(?P<depth>[0-9]+)$/',
	    array('action'=>'default'));
	$this->RegisterRoute('/download\/get\/(?P<alias>'.$aliaspatt.'+)\/(?P<returnid>[0-9]+)$/',
	    array('action'=>'download'));
    //		$this->RegisterRoute('/download\/getbymail\/(?P<hashkey>[a-z0-9]+)\/(?P<returnid>[0-9]+)$/',
    //					array('action'=>'download'));
	$this->RegisterRoute('/download\/detail\/(?P<alias>'.$aliaspatt.'+)\/(?P<returnid>[0-9]+)$/',
	    array('action'=>'detail'));
    //		$this->RegisterRoute('/download\/sendemail\/(?P<returnid>[0-9]+)$/',
    //					array('action'=>'sendemail'));

    //		For your form's params you need only
    //		$this->SetParameterType('paramName',CLEAN_TYPE);
    //		$this->SetParameterType(CLEAN_REGEXP.'/file_.*/',CLEAN_STRING);

    }

    function GetEventDescription ( $eventname )
    {
	return $this->Lang('event_info_'.$eventname );
    }

    function GetEventHelp ( $eventname )
    {
	return $this->Lang('event_help_'.$eventname );
    }

    function InstallPostMessage()
    {
	return $this->Lang('postinstall');
    }

    function UninstallPostMessage()
    {
	return $this->Lang('postuninstall');
    }

    function UninstallPreMessage()
    {
	return $this->Lang('really_uninstall');
    }
	function getDirectoryTree( $outerDir , $recursive = true){
		$dirs = array_diff( scandir( $outerDir ), Array( '.', '..','.htaccess' ) );
		$dir_array = Array();
		foreach( $dirs as $d ){
			if( is_dir($outerDir.DIRECTORY_SEPARATOR.$d) && $recursive) 
			$dir_array = array_merge( $dir_array ,$this->getDirectoryTree( $outerDir.DIRECTORY_SEPARATOR.$d ) );
			else 
			$dir_array[] = realpath($outerDir.DIRECTORY_SEPARATOR.$d);
		}
		return $dir_array;
	} 
    function GetThumbnailsDropdown($addvalue = null)
    {
		global $config;
		$in_path = $this->GetPreference('dir_thumbs','');
		$list = $this->getDirectoryTree( $config['uploads_path'].DIRECTORY_SEPARATOR. $in_path);
		$ret = array('' => '');
		foreach( $list as $v )
		{
			$v = str_replace( $config['uploads_path'].DIRECTORY_SEPARATOR, '', $v);
			$ret[str_replace( $in_path, '', $v)] = $v;
		}
		if( !empty( $addvalue ) )
			$ret[str_replace( $in_path, '',$addvalue)] = $addvalue;
		return $ret;
	}
    function CreateThumbnail($path)
    {
		global $config;
		
		$ext = strlen('.'. pathinfo($path,PATHINFO_EXTENSION));
		$file = substr( pathinfo($path,PATHINFO_BASENAME), 0, -$ext);

		include_once("lib/easyphpthumbnail/easyphpthumbnail.class.php5");
		$thumb = new easyphpthumbnail();
		$inside_dir = $this->GetPreference('dir_thumbs','');
		$thumb->Thumblocation = $config['uploads_path'].DIRECTORY_SEPARATOR.$inside_dir;
		$thumb->Thumbsize = $this->GetPreference('thumbs_size', 80);
		$thumb->Thumbsaveas = 'jpg';
		$thumb->Thumbfilename = $file.'.jpg';
		$thumb->Thumbprefix = '';
		$thumb->Createthumb( $path,'file');
		return $inside_dir.$thumb->Thumbfilename;
    }

    function ProcessTemplateFromDatabaseByAlias($alias)
    {
	global $gCms;
	$db =& $gCms->GetDb();
	$dbresult = $db->Execute('SELECT template_id FROM '.cms_db_prefix().'module_downloadmanager_templates WHERE template_name = ?',array($alias));
	if ($dbresult !== false && $row = $dbresult->FetchRow())
	{
	//~ var_dump($row);
	    return $this->ProcessTemplateFromDatabase('downman_'.$row['template_id'] ,'',true) ;
	}
	else
	    $this->DisplayErrorPage($id, $params, $returnid, $this->lang('wrongtemplate',$alias) );
	return false;

    }

    function SearchResult($returnid, $id, $attr = '')
    {
      $result = array();
        
      if ($attr == 'download')
      {
        $db =& $this->GetDb();
        $q = "SELECT name, alias FROM ".cms_db_prefix()."module_downloadmanager_files WHERE
                file_id = ?";
        if( $this->GetPreference('expired_searchable',1) == 0 )
        {
            // make sure we don't return expired articles.
            // if we don't want em to.
            $q .= ' AND '. $this->GetFileStdWhere();
        }
        $dbresult = $db->Execute( $q, array( $id ) );
        if ($dbresult)
        {
            $row = $dbresult->FetchRow();
            //0 position is the prefix displayed in the list results.
            $result[0] = $this->GetFriendlyName();
            //1 position is the title
            $result[1] = $row['name'];
            //2 position is the URL to the title.
            $result[2] = $this->CreateDetailLink($id,$returnid,$this->lang('details'),$row['alias'],true);
        }
      }
      else if ($attr == 'category')
      {
        $db =& $this->GetDb();
        $q = "SELECT name, alias FROM ".cms_db_prefix()."module_downloadmanager_categories WHERE
                    category_id = ?";
        $dbresult = $db->Execute( $q, array( $id ) );
        if ($dbresult)
        {
            $row = $dbresult->FetchRow();
            //0 position is the prefix displayed in the list results.
            $result[0] = $this->GetFriendlyName();
            //1 position is the title
            $result[1] = $this->lang('category'). ': '. $row['name'];
            //2 position is the URL to the title.
            $result[2] = $this->CreateCategoryLink($id,$returnid,'',$row['alias'], false,true);
        }

      }
      return $result;
    }

    function SearchDeleteFile($id, &$searchM = null)
    {
        $this->SearchDeleteWords('download', $id, $searchM);
    }
    function SearchDeleteCategory($id, &$searchM = null)
    {
        $this->SearchDeleteWords('category', $id, $searchM);
    }
    function SearchDeleteWords($type, $id, &$searchM = null)
    {
        $db =& $this->GetDb();
        if(is_null($searchM))
            $searchM =& $this->GetModuleInstance('Search');
        if ($searchM != FALSE)
        {
            $searchM->DeleteWords($this->GetName(), $id, $type);
        }
    }
    function SearchAddFile($id, $data, $expire, &$searchM = null)
    {
        $this->SearchAddWords('download', $id, $data, $expire, $searchM);
    }
    function SearchAddCategory($id, $data, $expire, &$searchM = null)
    {
        $this->SearchAddWords('category', $id, $data, $expire, $searchM);
    }
    function SearchAddWords($type, $id, $data, $expire, &$searchM = null)
    {
        $db =& $this->GetDb();
        if(is_null($searchM))
            $searchM =& $this->GetModuleInstance('Search');
        if ($searchM != FALSE)
        {
            $expire = ($expire != NULL && $this->GetPreference('expired_searchable',0) == 0) ?  $db->UnixTimeStamp($expire) : NULL;
            $searchM->AddWords($this->GetName(), $id,$type, $data, $expire);
        }
    }

    function SearchReindex(&$module = null)
    {
        if(is_null($module))
            $module =& $this->GetModuleInstance('Search');
	
        $db =& $this->GetDb();

        $query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files';
        $result = &$db->Execute($query);
        while ($result && !$result->EOF)
        {
            if ($result->fields['visible'] == true)
            {
			$this->SearchDeleteFile($result->fields['file_id'], $module );
            $this->SearchAddFile($result->fields['file_id'],
                    $result->fields['alias'] . ' ' . $result->fields['name'] . ' ' . $result->fields['description'],
                    $result->fields['expires'], $module);
            }
            $result->MoveNext();
        }
        $query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_categories';
        $result = &$db->Execute($query);
        while ($result && !$result->EOF)
        {
			$this->SearchDeleteCategory($result->fields['category_id'], $module );
            $this->SearchAddCategory($result->fields['category_id'],
                    $result->fields['alias'] . ' ' . $result->fields['name'] . ' ' . $result->fields['description'],
                    null, $module);
            $result->MoveNext();
        }
    }

	/*---------------------------------------------------------
	   DisplayErrorPage($id, $params, $returnid, $message)
	   NOT PART OF THE MODULE API

	   This is an example of a simple method to display
	   error information on the admin side.
	   ---------------------------------------------------------*/
    function DisplayErrorPage($id, &$params, $returnid, $message='')
    {
	$this->smarty->assign('title_error', $this->Lang('error'));
	if ($message != '')
	{
	    $this->smarty->assign_by_ref('message', $message);
	}

	// Display the populated template
	echo $this->ProcessTemplate('error.tpl');
	
    }
	function DisplayDownloadErrorPage($msg, $returnlink = null)
	{
		if($returnlink == null ) 
			$returnlink = '<a href="javascript:history.back()">'.$this->lang('back').'</a>';
		$err['msg'] = $msg;
		$this->smarty->assign('error', $this->lang('error'));
		$this->smarty->assign('returnlink', $returnlink);
		$this->smarty->assign_by_ref('download_error', $err);
		$this->smarty->assign('download_error_msg', $this->lang('download_error_msg'));
		echo $this->ProcessTemplate('download_error.tpl');
		exit;
	}
    // get the default restrictions ex. date
    function GetFileStdWhere($alias = null)
    {
        $alias = !empty($alias)?$alias.'.': $alias;
        return '( ( '.$alias.'expires IS NULL  OR '.$alias.'expires >= now() ) 
                    AND ( '.$alias.'starts IS NULL  OR '.$alias.'starts <= now() ) )';
    }
    // gets number of associated files for categories
    function GetCategoryFileNb( $where = false )
    {
	$tmp = array();
	$db =& $this->GetDb();
	$q = 'SELECT category_id , COUNT(file_id) AS count FROM '.cms_db_prefix().'module_downloadmanager_files_category '
	    .($where!==false? 'WHERE '.$where:'').
	    'GROUP BY category_id';
	$dbresult = $db->Execute($q);
	while ($dbresult && $row = $dbresult->FetchRow())
	    $tmp[$row['category_id']] =  $row['count'];
	return $tmp;
    }
    function GetCategories( $where = false )
    {
        $tmp = array();
        $db =& $this->GetDb();
        $q = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_categories '
            .($where!==false? 'WHERE '.$where:'').
            'GROUP BY category_id';
        $dbresult = $db->Execute($q);
        while ($dbresult && $row = $dbresult->FetchRow())
            $tmp[$row['category_id']] =  $row;
        return $tmp;
    }
    // get file assotiation with arrays
    // returns an array with elements
    //		$fileid => array (
    //					'count' => (int) size of array,
    //					0 => array( long_name , name, category_id)
    //					...
    //				)
    function GetFileCategories( $where = false )
    {
	$db =& $this->GetDb();
	// get file categories so we can show to which category file belongs
	// will be used soon

	$files_categories = array();
	$q = 'SELECT file_id, long_name, name, category_id, alias FROM '.cms_db_prefix().'module_downloadmanager_files_category
				LEFT JOIN '.cms_db_prefix().'module_downloadmanager_categories USING (category_id) '.
	    ($where!==false? 'WHERE '.$where:'');

	$dbresult = $db->Execute($q);
	while ($dbresult && $row = $dbresult->FetchRow())
	{
	    $files_categories[$row['file_id']][] = array(
		'long_name' => $row['long_name'],
		'name' => $row['name'],
		'alias' => $row['alias'],
		'category_id' => $row['category_id']
	    );
	}
	foreach ($files_categories as $k => $v )
	    $files_categories[$k]['count'] = count($v);

	return $files_categories;
    }
    // get all subcategories
    //	 items in returned array:
    //   1 => array
    //      'id' => int 1  // id in database
    //      'name' => string 'General' // name
    //		...
    //      'filesnb' => int 2 // number of files
    //      'items' => array(&,&,&,...) // referals to subcategories
    //      'parent' => & // referal to parent category
    function GetSubCategories( $where = false )
    {
	$db =& $this->GetDb();
	$q = 'SELECT *, COUNT(file_id) AS filesnb FROM cms_module_downloadmanager_categories c
			LEFT JOIN '.cms_db_prefix().'module_downloadmanager_files_category USING (category_id)
			' .($where!==false? 'WHERE '.$where:'').
	    'GROUP BY category_id ORDER BY c.hierarchy_priority';
	$subcategories = array();
	$dbresult = $db->Execute($q);
	while ($dbresult && $row = $dbresult->FetchRow())
	{

	    $subcategories[(int) $row['category_id'] ] = array(
		'id' => (int)$row['category_id'],
		'name' => $row['name'],
		'long_name' => $row['long_name'],
		'alias' => $row['alias'],
		'filesnb' => (int)$row['filesnb'],
		'items' => array() ,
		'parent' => false ,
	    );
	    $tmp = &$subcategories[(int) $row['category_id'] ];

	    $s = explode(".",$row['hierarchy']);
	    if(count($s) > 1)
	    {
	    // not a 1st level category
		$subcategories[ (int) $s[count($s)-2] ]['items'][] = $tmp;
		$subcategories[(int) $row['category_id'] ]['parent']  = &$subcategories[ (int) $s[count($s)-2] ];
	    }
	}
	return $subcategories;
    }
    // gets human redeable access type array
    function GetAccessTypes($access)
    {
	$t = array();
	if($this->CheckDownType($access, DMNR_BY_FREE))
	    $t[] = 'free';
	if($this->CheckDownType($access, DMNR_BY_FEU))
	    $t[] = 'feu';
	if($this->CheckDownType($access, DMNR_BY_MAIL))
	    $t[] = 'mail';
	return $t;
    }
    // returns text with replaced non-word chars to $replacement
    function Obstrucate($href, $replacement = '-')
    {
	return preg_replace("/(\W+)/i",$replacement, $href);
    }
    // takes id and array produced by function GetFileCategories
    // further version will make links
    function CreateCategoryLinks($mid,$returnid,$id,  &$arr )
    {
	$id = (int)$id;
	if( !is_array($arr) || empty($arr) || !isset($arr[$id]) )
	// if error or empty or no item to sellect
	    return '';
	$t = array();
	unset($arr[$id]['count']);
	foreach( $arr[$id] as $v )
	    $t[] = $this->CreateCategoryLink($mid,$returnid,$v['name'],$v['alias']);

	return 	implode( ", ", $t );
    }

    //
    function CreateCategoryLink($id,$returnid,$name='',$alias='', $depth=false,$onlyurl=false)
    {
	$arr = array();
	if($depth!==false)
	    $arr['depth'] = $depth;

	if($name == '' || $alias == '')
	{

	    $t = $this->CreateLink($id, 'default', $returnid, $this->lang('rootcategory'),
		$arr,'',$onlyurl,true,'',false,
		'download/'.$returnid.($depth!==false?"/".$depth:''));
	}
	else
	{
	    $arr['alias']=$alias;

	    $t = $this->CreateLink($id, 'default', $returnid, $name,
		$arr,'',$onlyurl,true,'',false,
		'download/category/'.$alias.'/'.$returnid.($depth!==false?"/".$depth:''));
	}
	return $t;
    }
    function CreateDownloadLink($id,$returnid,$name,$alias,$onlyurl=false)
    {
	return $this->CreateLink($id, 'download', $returnid, $name , array('alias'=>$alias),'',$onlyurl,true,'',false,'download/get/'.$alias.'/'.$returnid);
    }

    function CreateDetailLink($id,$returnid,$name,$alias,$onlyurl=false)
    {
	return $this->CreateLink($id, 'detail', $returnid, $name , array('alias'=>$alias),'',$onlyurl,true,'',false,'download/detail/'.$alias.'/'.$returnid);
    }
    function ToHRSize($size)
    {
	return (  $size<1024? $size." B": ( $size<1048576 ?   ((int) ( $size/1024))." KB" : ((int) ( $size/1048576 ))." MB" ));
    }

    // based on News Module function News->UpdateHierarchyPositions()
    function UpdateHierarchyPositions()
    {
	$db =& $this->GetDb();

	// computing files place
	$query = "SELECT category_id, name, priority FROM ".cms_db_prefix()."module_downloadmanager_categories";
	$dbresult = $db->Execute($query);
	while ($dbresult && $row = $dbresult->FetchRow())
	{
	    $current_hierarchy_position = "";
	    $current_hierarchy_priority = "";
	    $current_long_name = "";
	    $content_id = $row['category_id'];
	    $content_priority = $row['priority'];
	    $current_parent_id = $row['category_id'];
	    $count = 0;

	    while ($current_parent_id > -1)
	    {
		$query = "SELECT category_id, name, parent_id, priority FROM ".cms_db_prefix()."module_downloadmanager_categories WHERE category_id = ?";
		$row2 = $db->GetRow($query, array($current_parent_id));
		if ($row2)
		{
		    $current_hierarchy_position = str_pad($row2['category_id'], 5, '0', STR_PAD_LEFT) . "." . $current_hierarchy_position;
		    $current_hierarchy_priority = str_pad($row2['priority'], 5, '0', STR_PAD_LEFT) . "|". str_pad($row2['category_id'], 5, '0', STR_PAD_LEFT). "." . $current_hierarchy_priority;
		    $current_long_name = $row2['name'] . ' | ' . $current_long_name;
		    $current_parent_id = $row2['parent_id'];
		    $count++;
		}
		else
		{
		    $current_parent_id = 0;
		}
	    }

	    if (strlen($current_hierarchy_position) > 0)
	    {
		$current_hierarchy_position = substr($current_hierarchy_position, 0, strlen($current_hierarchy_position) - 1);
		$current_hierarchy_priority = substr($current_hierarchy_priority, 0, strlen($current_hierarchy_priority) - 1);
	    }

	    if (strlen($current_long_name) > 0)
	    {
		$current_long_name = substr($current_long_name, 0, strlen($current_long_name) - 3);
	    }
	    $depth = substr_count( $current_hierarchy_position, '.' );
	    $query = "UPDATE ".cms_db_prefix()."module_downloadmanager_categories SET hierarchy = ?, long_name = ?, priority = ?, hierarchy_priority = ?, depth = ? WHERE category_id = ?";
	    $db->Execute($query, array($current_hierarchy_position, $current_long_name, $content_priority, $current_hierarchy_priority, $depth, $content_id));
	}
    }

    // main function for parsing the selector
    function MatchPair( &$subject , &$arr)
    {
	$matches=array();
	$pattern = '/([$]?\d+|\([$]?\d+\))([|&])([$]?\d+|\([$]?\d+\))/i';
	$cm = preg_match_all($pattern, $subject, $matches);
    
	foreach($matches[0] as $k => $m)
	{
        $c = count($arr);
        $count = 0;
        $subject =  substr($subject,0, strpos($subject, $m)).
            '$'.$c.
            substr($subject,strpos($subject, $m)+strlen($m));

	    $arr[$c] = array( $matches[2][$k], $matches[1][$k] ,$matches[3][$k] );

	    // shrink optimize
	    $v =& $arr[$c]; // let`s make an alias
	    $v = str_replace( array("(",")"), array('','') , $v); // brackets are no longer needed

	    if($v[0] == '|') // we can shrink sql only for OR parameters
		for( $j = 1 ; $j < 3; $j++)
		    if($v[$j]{0} == '$')
		    {
			$z = (int) substr($v[$j], 1);
			if( $arr[$z][0] == '|' )
			{// now we can shrink
			    unset($arr[$z][0]);
			    foreach($arr[$z] as $zv)
				$arr[$c][] = $zv;

			    unset($arr[$c][$j]);
			    $arr[$z] = null; // we won`t need it anymore
			}
		    }
	}
	if($cm > 0)
	    return $this->MatchPair( $subject , $arr);
	return 0;
    }

    function cleanSelector($selector){
        return preg_replace('/([^0-9()|&])+/i', '', $selector); // cleaning
    }

    function selectorToSQL($selector){

        $selector = trim($selector);
        if(empty($selector))
            return '';
        
        $categoryclean = $categoryid  = $this->cleanSelector($selector); // cleaning
        
        if( preg_match('/^[$]?\d+$/i', $categoryid)  || preg_match('/^\([$]?\d+\)$/i', $categoryid) ){  
            // we have single selector
            $categoryid  = preg_replace('/([^0-9])+/i', '', $categoryid); // cleaning
            return 'hierarchy LIKE \'%'.str_pad($categoryid, 5, '0', STR_PAD_LEFT).'%\'';
        }
        // we have complex selector
        $arr = array(); // array for storing pair
        $this->MatchPair( $categoryid , $arr); // recursive category selector parsing

        $categoryid = str_replace( array("(",")"), '' , $categoryid);
        if ( preg_match('/^[$]?\d+$/i', $categoryid) == 0 )
        {
            $this->DisplayErrorPage($id, $params, $returnid, $this->lang('wrongcategorysel', $categoryclean) );
            return;
        }

        // not we are sure that category selector is ok
        // we can change it to sql

        $sql = '';

        foreach( $arr as $k => $v )
        {
            $tmp = array();
            if( empty($v) )
                continue;
            
            $v = array_unique($v); // no need for doubles

            $op = $v[0]; // get operator
            unset($v[0]);  // clean operator

            foreach($v as $tv)
            {
                if($tv{0} == '$') // we get a referal
                {
                    $z = (int) substr($tv, 1);
                    $tmp[] = $arr[$z];
                }
                else
                    $tmp[] = 'hierarchy LIKE \'%'.str_pad($tv, 5, '0', STR_PAD_LEFT).'%\'';
            }

            if( $op == '|' )
                $op = " OR ";
            else
                $op = " AND ";

            $sql = $arr[$k] = "(".implode($op, $tmp).")";
        }

        return $sql;
    }
    // ugrade file agregation when category change
    function UpdateAssociationAgregationCategory( $cat )
    {
	$db =& $this->GetDb();
	$query = "SELECT file_id FROM ".cms_db_prefix()."module_downloadmanager_files_category
				WHERE category_id = ".(int)$cat.";";
	$dbresult = $db->Execute($query);
	$arr = array();
	while( $dbresult && $row = $dbresult->FetchRow() )
	    $arr[] = (int) $row['file_id'];

	$this->UpdateAssociationAgregation( $arr );
    }

    // upgrade file agregation when file(s) change
    // if false all file are upgraged
    //    int only one
    //    array all files in array
    function UpdateAssociationAgregation( $file = false )
    {
	$db =& $this->GetDb();
	$query ='';
	$dbresult = $db->Execute("SELECT file_id FROM ".cms_db_prefix()."module_downloadmanager_files");
	while( $dbresult && $row = $dbresult->FetchRow() )
	    $arr[$row['file_id']] = '';

	$prefix ="SELECT file_id, hierarchy FROM ".cms_db_prefix()."module_downloadmanager_files_category fc
					LEFT JOIN ".cms_db_prefix()."module_downloadmanager_categories c USING(category_id)";
	if( $file  === false )
	{// all files are upgraded
	    $query = $prefix;
	}
	else if( is_int($file) )
	    {// Only one file
		$query = $prefix." WHERE file_id  = $file ";
	    }
	    else if( is_array($file) && count($file)>0 )
		{// only files in array
		    foreach($file as $k => $v)
			$file[$k] = (int) $v;
		    $query = $prefix." WHERE file_id IN(".implode(",",$file).");";
		}

	if($query == '')
	    throw new Exception('Wrong $file parameter in '.__FUNCTION__.' function in file '.__FILE__);

	$arr = array();
	$dbresult = $db->Execute($query);
	while( $dbresult && $row = $dbresult->FetchRow() )
	    $arr[$row['file_id']][] = $row['hierarchy'];

	foreach( $arr as $k => $v)
	{
	    $query = "UPDATE ".cms_db_prefix()."module_downloadmanager_files SET
					hierarchy = '".implode(";",$v)."'
					WHERE file_id = ".(int)$k;
	    $db->Execute($query);
	}
    }

    // based on News Module function News->CreateParentDropdown()
    // return generated dropdowninput with item set on $selectedvalue
    //		  input will contain all $catid kids
    //		  if $catid = -1 all root node kids
    function CreateParentDropdown($id, $catid = -1, $selectedvalue = -1)
    {
	$db =& $this->GetDb();

	$longname = '';

	$items['('.$this->Lang('none').')'] = '-1';

	$query = "SELECT hierarchy, long_name FROM ".cms_db_prefix()."module_downloadmanager_categories WHERE category_id = ?";
	$dbresult = $db->Execute($query, array($catid));
	while ($dbresult && $row = $dbresult->FetchRow())
	    $longname = $row['hierarchy'] . '%';

	$query = "SELECT category_id, name, hierarchy, long_name FROM ".cms_db_prefix()."module_downloadmanager_categories WHERE hierarchy not like ? ORDER by hierarchy_priority,hierarchy";
	$dbresult = $db->Execute($query, array($longname));
	while ($dbresult && $row = $dbresult->FetchRow())
	    $items[$row['long_name']] = $row['category_id'];

	return $this->CreateInputDropdown($id, 'parent', $items, -1, $selectedvalue);
    }

    function GetAccessTypeCombo($key=false, $name2id=false)
    {
	$types = array(	"0" => $this->Lang("accesstype_free"),
	    "1" => $this->Lang("accesstype_feuonly"),
	    "2" => $this->Lang("accesstype_emailonly"),
	    "3" => $this->Lang("accesstype_mixed")
	);
	if($key)
	{
	    return (isset($types[$key])?$types[$key]:false);
	}

	return ($name2id?$types:array_flip($types));
    }

    function FEUinstalled()
    {
		// just check if FEU is installed
		$m = &CMSModule::GetModuleInstance('FrontEndUsers');
		if( $m )
			return true;
		return false;
    }

    function MailerInstalled()
    {
		// just check if CMSMailer is installed
		$m = &CMSModule::GetModuleInstance('CMSMailer');
		if( $m )
			return true;
		return false;
    }

    function GetTemplatesCombo($type=false, $withdefault=true, $name2id=false)
    {
    // get an array of the templates of the chosen type (or all), for use
    // in dropdown lists
	$result = array();
	if($type && substr($type,-1) != '_')	$type .= '_';
	if($withdefault)	$result[''] = $this->Lang('defaulttemplate');
	$templatelist = $this->ListTemplates($this->GetName());
	foreach($templatelist as $template)
	{
	    $name = substr(strstr($template, '_'), 1);
	    $prefix = str_replace($name,"",$template);
	    if(!$type || strtolower($prefix) == strtolower($type))	$result[$template] = $name;
	}
	return ($name2id?$result:array_flip($result));
    }

    function GetGroupsCombo()
    {
    // get an array of the FEU groups, for use in a select list
	if(!$this->FEUinstalled())
	    return array();
	$FEU = $this->GetModuleInstance('FrontEndUsers');
	if($FEU)
	    $groups = $FEU->GetGroupList();
	return $groups;
    }

    function GetFileGroups($file_id)
    {
    // get the groups that have access to a given file
	$db = $this->GetDb();
	$query = "SELECT * FROM ".cms_db_prefix()."module_downloadmanager_filegroups WHERE file_id=?";
	$dbresult = $db->Execute($query, array($file_id));
	$output = array();
	while($dbresult && $row = $dbresult->FetchRow())
	{
	    $output[] = $row['group_id'];
	}
	return $output;
    }

    function CheckFEUPerm($file_id)
    {
    // checks if the logged in user has access to this file
	if(!$this->FEUinstalled())	return false;

	$FEU = $this->GetModuleInstance('FrontEndUsers');
	if(!$FEU->LoggedIn())	return false;

	$userid = $FEU->LoggedInId();
	$usergroups = explode(',',$FEU->GetMemberGroups($userid));
	$groups = $FEU->GetGroupList();
	$filegroups = $this->GetFileGroups($file_id);
	
	// check if any group is needed to access
	if(empty($filegroups))
		$hasperm = true;
	else
		$hasperm = false;

	$i = 0;
	while(!$hasperm && $i < count($usergroups))
	{
	    if(in_array($groups[$usergroups[$i]],$filegroups))	$hasperm = true;
	    $i++;
	}
	return $hasperm;
    }

    function GetFEUGroupNames($groupidarray)
    {
    // this takes an array of group ids, and transform it into a string of group names
    // use this to prepare the only_groups parameter of the FEU login form
	if(count($groupids) == 0)
	    return array();
	if(!$this->FEUinstalled())
	    return false;
	$FEU = $this->GetModuleInstance('FrontEndUsers');
	$groups = array_flip($FEU->GetGroupList());
	$output = '';
	foreach($groupidarray as $oneid)
	{
	    if(isset($groups[$oneid]))	$output .= ($output == ''?'':',').$groups[$oneid];
	}
	return $output;
    }
    function CreateAlias($txt)
    {
	return strtolower($this->Obstrucate($txt));
    }
    // taken form clt module
    // return true if alias does not exist in database
    function CheckAlias($dbtable, $alias, $itemid=false, $idfield="id", $aliasfield="alias")
    {
	if(empty($alias))
	    return false;

	$db = $this->GetDb();
	// checks if this alias already exists in the level
	$query = "SELECT ".$aliasfield." FROM ".cms_db_prefix().'module_downloadmanager_'.$dbtable." WHERE ".$aliasfield." = ?";
	if($itemid) $query .= " AND ".$idfield."!=".$itemid;

	$dbresult = $db->Execute($query,array($alias));
	$target = 0;
	if($dbresult && $row = $dbresult->FetchRow()) $target++;
	return ( $target == 0);
    }

    function CheckDownType($given , $tocheack)
    {
	$given = (int)$given;
	$tocheack= (int)$tocheack;
	if($tocheack === $given )
	    return true;

	$t = $given & $tocheack;
	return  $t>0?true:false ;
    }
    
    function checkFileAllow($filepath){
        global $config;
        $filepath = realpath( $filepath );
        $filepath = str_replace( $config['root_path'].DIRECTORY_SEPARATOR ,'', $filepath ) ;
        switch(  $filepath ){
            case 'config.php':
            case 'index.php':
            case 'version.php':
            case 'rewrite.log':
            case 'fileloc.php':
            case 'include.php':
            case 'moduleinterface.php':
            case 'preview.php':
            case 'soap.php':
            case 'stylesheet.css':
            case 'version.php':
                return false;
        }
        foreach( array( 'modules','admin','lib','doc','plugins' ) as $path )
            if( strpos( $filepath ,$path.DIRECTORY_SEPARATOR) === 0 )
                return false;
        return true;

    }

} // class end

?>
