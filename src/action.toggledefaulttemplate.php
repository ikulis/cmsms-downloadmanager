<?php
if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}

$template_name = isset($params['template_name'])?$params['template_name']:'';
$prefix = isset($params['prefix'])?$params['prefix']:'';

if( $template_name != '' && $prefix != '' )
{
	$this->SetPreference('default_'.$prefix.'template', $prefix.$template_name);
}
	
$this->Redirect($id, 'defaultadmin', $returnid, array('active_tab' => 'templates'));	

?>
