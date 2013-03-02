<?php

if (!isset($gCms)) exit;

$db =& $this->GetDb();
$file = false;

if( !isset ( $params['alias']) )
	$this->DisplayDownloadErrorPage($this->lang('nofile'));


$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files WHERE alias = ? ';
$dbresult = $db->Execute($query,array($this->CreateAlias($params['alias'])));
if ($dbresult && $row = $dbresult->FetchRow())
{
	$file = new StdClass();
	foreach($row as $key=>$value)
		$file->$key = $value;
}
else
	$this->DisplayDownloadErrorPage($this->lang('wrongfile'));

// checking permisions
if(	( $this->CheckDownType($file->accesstype , DMNR_BY_MAIL) && !$this->MailerInstalled() ) ||
		( $this->CheckDownType($file->accesstype , DMNR_BY_FEU) && !$this->FEUinstalled() ) ||
		( $this->CheckDownType($file->accesstype , DMNR_BY_BOTH ) && !$this->MailerInstalled() && !$this->FEUinstalled()))
{
	// the file has an access type that is unavailable
	$this->DisplayDownloadErrorPage($this->lang('noacces'));
}

// if the file has FEU access, we check if the user is already logged in and has access
// if he does, we don't display forms and go to the details
$hasperm = false;
if( $this->CheckDownType($file->accesstype , DMNR_BY_FEU) )
	$hasperm = $this->CheckFEUPerm($file->file_id);

// user has not gain acces by FEU so we are cheaking if he can get this by mail
if( !$hasperm && $this->CheckDownType($file->accesstype , DMNR_BY_MAIL))
{
	// EMAIL IS A POSSIBLE ACCESS

	// load the inputs and labels
	$prx = 'sm_';
	$this->smarty->assign("firstname_label", $this->Lang("prompt_firstname"));
	$this->smarty->assign("firstname_input", $this->CreateInputText($id,$prx."firstname","",28,64));
	$this->smarty->assign("lastname_label", $this->Lang("prompt_lastname"));
	$this->smarty->assign("lastname_input", $this->CreateInputText($id,$prx."lastname","",28,64));
	$this->smarty->assign("email_label", $this->Lang("prompt_email"));
	$this->smarty->assign("email_input", $this->CreateInputText($id,$prx."email","",28,64));
	$this->smarty->assign("mailinglist_label", $this->Lang("prompt_mailinglist"));
	$this->smarty->assign("mailinglist_input", $this->CreateInputCheckbox($id, $prx."mailinglist", 1, 1));
	$this->smarty->assign("submit", $this->CreateInputSubmit($id,$prx.'submit',$this->lang('submitreqemail'), 'onclick="return downloader_validate(\''.$id.'\');"') );



	if( $file->template_form != '' && $template = $this->GetTemplate($file->template_form) )
	{
		// the template exists, we're going to use it
	}
	else
	{
		// no template specified, or template doesn't exist... we get the default one
		$templatename = $this->GetPreference('default_form_template', '');
		$template = $this->GetTemplate($templatename);
	}

	echo '<script>
	function downloader_validate(id){
		var firstname = document.getElementById(id+"'.$prx.'firstname").value;
		var lastname = document.getElementById(id+"'.$prx.'lastname").value;
		var email = document.getElementById(id+"'.$prx.'email").value;
		if(firstname == "" || lastname == ""){
			alert("'.$this->Lang("please_name").'");
			return false;
		}else if(email == "" || email.indexOf("@") == -1){
			alert("'.$this->Lang("please_email").'");
			return false;
		}else{
			return true;
		}
	}
	</script>
	';

	echo $this->CreateFormStart($id, 'sendemail', $returnid, 'post');
	echo $this->CreateInputHidden($id, "file_id", $file->file_id);
	echo $this->ProcessTemplateFromData($template);
	echo $this->CreateFormEnd();
}

// user has not gain acces by FEU so we are cheaking if he can get this by login
if( !$hasperm && $this->CheckDownType($file->accesstype , DMNR_BY_FEU) && $this->FEUinstalled() )
{
	// FEU IS A POSSIBLE ACCESS

	$FEU = $this->GetModuleInstance('FrontEndUsers');
	if($FEU->LoggedIn()){
		// the user is logged in, but doesn't have access
		$this->DisplayDownloadErrorPage($this->lang('noaccess'));
	}
	else
	{
		// user not logged in
		$loginmsg = ($this->CheckDownType($file->accesstype , DMNR_BY_MAIL))?'mayalsologin':'mustlogin';
		echo '<p class="dmlogintitle">'.$this->Lang($loginmsg).'</p>';
		$allowedgroups = $this->GetFileGroups($file_id);
		$allowedgroups = $this->GetFEUGroupNames($allowedgroups);
		$arr = array("form"=>"login");
		if(!empty($allowedgroups))
			$arr["only_groups"]=$allowedgroups;

		$FEU->_DoUserAction($id,$arr, $returnid);
	}
}

if( $this->CheckDownType($file->accesstype , DMNR_BY_FREE ) || $hasperm)
{
	// downloading file
	$file_name =  $file->name.($file->ext!=''?'.'. $file->ext:'');
	
	$this->smarty->assign('download_name',  $file_name) ;
	$this->smarty->assign('downloading', $this->lang('downloading', $file_name) );
	$this->smarty->assign('downloading_info', $this->lang('downloading_info'));
	$this->smarty->assign('backtolistlink', '<a href="javascript:history.back()">'.$this->lang('backtolist').'</a>');

	header("Refresh: 2; url=".$gCms->config['root_url']."/modules/DownloadManager/download.php?alias=".$file->alias);
	echo $this->ProcessTemplateFromDatabase('default_download_template' ,'',true) ;
	//~ echo $this->ProcessTemplate('download.tpl');
}

?>
