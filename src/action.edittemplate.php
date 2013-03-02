<?php
if (!isset($gCms)) exit;

if (! $this->CheckPermission('Use DownloadManager'))
{
	echo $this->DisplayErrorPage($id, $params, $returnid,$this->Lang('accessdenied'));
	return;
}

if (isset($params['cancel']))
{
	$params = array('active_tab' => 'templates');
	$this->Redirect($id, 'defaultadmin', $returnid, $params );
}

$prefix = isset($params['prefix'])?$params['prefix']:'';
$template_name = '';

if ( isset($params['content']) )
{
	// the template has been submitted
	$errors = 0;

	if($params['template_name'] == '')
	{
		echo $this->ShowErrors($this->Lang('nonamegiven'));
		$errors++;
	}

	if( !$this->CheckPermission('Modify Download Manager Template') )
	{
		echo $this->ShowErrors($this->Lang('needpermission', array('Modify Download Manager Template')));
		$errors++;
	}

	$content = $params['content'];
	$template_name = $params['template_name'];
	
	if( $errors == 0 && isset($params['new']) && $params['new'] )
	{
		// new template
		$message = 'templateadded';
		$template_name = munge_string_to_url($template_name);
		// check if the name already exists:
		$alltemplates = $this->ListTemplates($this->GetName());
		if(in_array($prefix.$template_name, $this->ListTemplates($this->GetName())))
		{
			echo $this->ShowErrors($this->Lang('alreadyexist'));
			$errors++;
		}
		else
		{
			@$this->SendEvent('DownloadManagerTemplateAdded', array('template_name' => $template_name, 'content' => $content, 'prefix' => $prefix));
		}
	}
	else
	{
		// exisiting template
		$message = 'templateupdated';
		// force a cache clear?
		$this->DeleteTemplate($prefix.$template_name, $this->GetName());
		@$this->SendEvent('DownloadManagerTemplateEdited', array('template_name' => $template_name, 'content' => $content, 'prefix' => $prefix));
	}
		
	if($errors == 0)
	{
		$this->SetTemplate($prefix.$template_name, $content, $this->GetName());
		$new = false;

		if(isset($params['submit']))
		{
			$this->Redirect($id, 'defaultadmin', $returnid, array('tab_message'=> $message, 'active_tab' => 'templates'));
		}
		else
		{
			echo $this->ShowMessage($this->Lang($message));
		}
	}
}
elseif ( isset($params['from']) && $tmp = $this->GetTemplate($prefix.$params['from'], $this->GetName()) )
{
	// we are duplicating an existing template
	$content = $tmp;
	$new = true;
}
elseif ( isset($params['template_name']) && $tmp = $this->GetTemplate($prefix.$params['template_name'], $this->GetName()) )
{
	// we are editing an existing template
	$content = $tmp;
	$template_name = $params['template_name'];
	$new = false;
}
else
{
	// we are creating a new template
	// we get a default content for the new template
	$content = $this->GetTemplateFromFile($prefix.'default');
	// default templates, if any, should be named "prefix_default.tpl"
	$new = true;
}

// form display
$this->smarty->assign('startform', $this->CreateFormStart($id, 'edittemplate', $returnid));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('inputcontent', $this->CreateTextArea(false, $id, $content, 'content', '', '', '', '', '20', '5'));
$this->smarty->assign('inputtemplate_name', $new?$this->CreateInputText($id, 'template_name', $template_name, 30, 64):$template_name.$this->CreateInputHidden($id, 'template_name', $template_name));
$this->smarty->assign('help_template_variables', $this->Lang($prefix.'templatehelp'));
$this->smarty->assign('help_template_general', $this->Lang('general_templatehelp'));
$this->smarty->assign('contentlabel', $this->Lang('contentlabel'));
$this->smarty->assign('template_namelabel', $this->Lang('template_namelabel'));
$this->smarty->assign('hidden', $this->CreateInputHidden($id, 'new', $new).$this->CreateInputHidden($id, 'prefix', $prefix));
$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', lang('submit')));
$this->smarty->assign('apply', $this->CreateInputSubmit($id, 'apply', lang('apply')));
$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', lang('cancel')));

echo $this->ProcessTemplate('edittemplate.tpl');
?>
