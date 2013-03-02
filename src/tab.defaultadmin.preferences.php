<?php

if (!isset($gCms)) exit;

// check for permissions
if (!$this->CheckPermission('Use DownloadManager'))
{
	echo $this->ShowErrors($this->Lang('needpermission', array('Use DownloadManager')));
	return;
}


$this->smarty->assign('startform', $this->CreateFormStart($id, 'save_admin_prefs', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('submit',$this->CreateInputSubmit($id, 'submit', 'Submit'));


$pref_errors = array();
$download_dir = $this->GetPreference('dir');
if( !file_exists($config['root_path'].DIRECTORY_SEPARATOR.$download_dir ) )
	$pref_errors[] = $this->lang('dir_dont_exists',$download_dir);
if( !is_writable($config['root_path'].DIRECTORY_SEPARATOR.$download_dir ) )
	$pref_errors[] = $this->lang('download_dir_not_writable',$download_dir);
$download_scan = explode(";",$this->GetPreference('scan'));
if(count($download_scan)>0)
  foreach( $download_scan as $f )
	if( !file_exists($config['root_path'].DIRECTORY_SEPARATOR.$f ) )
	  $pref_errors[] = $this->lang('dir_dont_exists',$f);


$this->smarty->assign('label_thumb_dir', $this->Lang('thumb_dir'));
$this->smarty->assign('label_thumb_size', $this->Lang('thumb_size'));
$this->smarty->assign('label_thumb_auto', $this->Lang('thumb_auto'));
$this->smarty->assign('label_download_dir', $this->Lang('download_dir'));
$this->smarty->assign('label_download_scan', $this->Lang('download_scan'));
$this->smarty->assign('label_download_scan_recurs', $this->Lang('download_scan_recurs'));
$this->smarty->assign('label_prefs_search_expires', $this->Lang('search_expire'));
$this->smarty->assign('label_download_template', $this->Lang('download_template'));
$this->smarty->assign('label_download_template_info', $this->Lang('download_template_info'));
$this->smarty->assign('label_wysiwyg_on', $this->Lang('wysiwyg_on'));

$this->smarty->assign('stop_image',$gCms->variables['admintheme']->DisplayImage('icons/system/stop.gif', 'error','','','systemicon'));
if(count($pref_errors) > 0 )
	$this->smarty->assign_by_ref('pref_errors', $pref_errors);

$this->smarty->assign('input_thumb_dir', $this->CreateInputText($id, 'dir_thumbs', $this->GetPreference('dir_thumbs', lang('needupgrade')), 50, 250));
$this->smarty->assign('input_thumb_size', $this->CreateInputText($id, 'thumbs_size', $this->GetPreference('thumbs_size', lang('needupgrade')), 50, 250));
$this->smarty->assign('input_thumb_auto', $this->CreateInputCheckbox($id, 'thumbs_auto','1', $this->GetPreference('thumbs_auto', lang('needupgrade')),  'class="pagecheckbox"'));
$this->smarty->assign('input_download_dir', $this->CreateInputText($id, 'download_dir', $this->GetPreference('dir', lang('needupgrade')), 50, 250));
$this->smarty->assign('input_download_scan', $this->CreateInputText($id, 'download_scan', $this->GetPreference('scan', lang('needupgrade')), 50, 250));
$this->smarty->assign('input_download_scan_recurs', $this->CreateInputCheckbox($id, 'download_scan_recurs', '1', $this->GetPreference('scan_recurs', true),  'class="pagecheckbox"'));
$this->smarty->assign('input_wysiwyg_on', $this->CreateInputCheckbox($id, 'admin_wysiwyg', '1', $this->GetPreference('admin_wysiwyg', true),  'class="pagecheckbox"'));
$this->smarty->assign('input_search_expires', $this->CreateInputCheckbox($id, 'search_expires', '1', $this->GetPreference('expired_searchable', true),  'class="pagecheckbox"'));
$this->smarty->assign('input_download_template', $this->CreateTextArea( false, $id, $this->GetTemplate('default_download_template'), 'download_template','pagesmalltextarea'));

// Display the populated template
echo $this->ProcessTemplate('adminprefs.tpl');

?>