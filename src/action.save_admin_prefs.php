<?php

if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}
if(isset ($params['download_dir']))
	$this->SetPreference('dir', $params['download_dir']);
$params['search_expires'] = isset($params['search_expires']) && $params['download_scan_recurs'] == true;
if( $this->GetPreference('expired_searchable', true) != $params['search_expires'])
{
	$this->SearchReindex();
	$this->SetPreference('expired_searchable', $params['search_expires']);
}
if(isset ($params['download_scan']))
	$this->SetPreference('scan', $params['download_scan']);

if(isset($params['dir_thumbs']) )
{
	global $config;
	$this->SetPreference("dir_thumbs", 
		str_replace($config['uploads_path'].DIRECTORY_SEPARATOR,'', realpath($config['uploads_path'].DIRECTORY_SEPARATOR.$params['dir_thumbs'] ) ).DIRECTORY_SEPARATOR //"images/DownloadManagerThumbs/"
	);
}
$params['thumbs_auto'] = isset ($params['thumbs_auto']) && $params['thumbs_auto'] == true;
$this->SetPreference("thumbs_auto", $params['thumbs_auto'] );

if(isset($params['thumbs_size']) && (int)$params['thumbs_size']  > 5 ) 
	$this->SetPreference("thumbs_size", (int)$params['thumbs_size'] );

$params['download_scan_recurs'] = isset ($params['download_scan_recurs']) && $params['download_scan_recurs'] == true;
$this->SetPreference('scan_recurs', $params['download_scan_recurs']);

if(isset ($params['download_template']))
	$this->SetTemplate('default_download_template',$params['download_template']);

$params['admin_wysiwyg'] = isset ($params['admin_wysiwyg']) && $params['admin_wysiwyg'] == true;
$this->SetPreference("admin_wysiwyg", $params['admin_wysiwyg'] );


$this->DoAction('defaultadmin', $id, array( 'tab_message'=>'prefsupdated', 'active_tab' => "preferences"));

?>