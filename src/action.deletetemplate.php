<?php
if (!isset($gCms)) exit;
if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}
$newparams = array('active_tab' => 'templates');

if( isset($params['template_name']) && $params['template_name'] != '')
{
	if ($this->CheckPermission('Modify Download Manager Template'))
	{
		if($this->DeleteTemplate($params['template_name']))
		{
			// template successfully deleted
			$newparams['tab_message'] = 'templatedeleted';
			@$this->SendEvent('DownloadManagerTemplateDeleted', array('template_name' => $template_name));
		}
	}
	else
	{
		echo $this->ShowErrors($this->Lang('needpermission', array('Modify Download Manager Template')));
	}
}

$this->Redirect($id, 'defaultadmin', $returnid, $newparams );

?>
