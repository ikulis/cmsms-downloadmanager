<?php
if (!isset($gCms)) exit;

$cmsmailer = $this->GetModuleInstance('CMSMailer');
if(!$cmsmailer)		die($this->lang('error'));	// due to prior checking, this shouldn't be happening

$db =& $this->GetDb();

$file = false;
if(isset($params['file_id']))
{
	$file_id = (int) $params['file_id'];
	$query = 'SELECT * FROM '.cms_db_prefix().'module_downloadmanager_files WHERE file_id = ? ';
	$dbresult = $db->Execute($query,array($file_id));
	$showheaders = false;
	include('function.assingfiles.php');
	$file = $onerow;
}

// cleaning prefixes
$prx = 'sm_';
foreach($params as $k => $v)
	if( stripos($k, $prx) === 0 )
	{
		unset($params[$k]);
		$params[substr($k,strlen($prx))] = $v;
	}
if(!$file)
{
	$this->DisplayDownloadErrorPage($this->lang('nofile'));
}
else
{
	if(!preg_match("/^([\w|\.|\-|_]+)@([\w||\-|_]+)\.([\w|\.|\-|_]+)$/i", $params['email']))
	{
		// the basic checking was done through js
		$this->DisplayDownloadErrorPage($this->lang('invalidemail'));
	}
	else
	{
		// check if the user exists
		$query = "SELECT user_id FROM ".cms_db_prefix()."module_downloadmanager_users WHERE email=?";
		$dbresult = $db->Execute($query,array($params['email']));
		if ($dbresult && $row = $dbresult->FetchRow())
		{
			$userid = $row['user_id'];
		}
		else
		{
			// if not, add him
			$userid = $db->GenID(cms_db_prefix()."module_downloadmanager_users_seq");
			$query = "INSERT INTO ".cms_db_prefix()."module_downloadmanager_users (user_id,firstname,lastname,email,mailinglist) VALUES (?,?,?,?,?)";
			$values = array($userid, str_replace(',','',$params['firstname']), str_replace(',','',$params['lastname']), $params['email'], isset($params['mailinglist'])?$params['mailinglist']:0);
			$result = $db->Execute($query,$values);
			
		}
		
		// create the download
		$hashkey = $file_id.$userid.md5(uniqid(rand(),1)).date('Ymd');
		$download_id = $db->GenID(cms_db_prefix()."module_downloadmanager_downloads_seq");
		$query = "INSERT INTO ".cms_db_prefix()."module_downloadmanager_downloads ( download_id, user_id, user_type, file_id, hashkey, downloaded)
			VALUES(?,?,2,?,?,0);";
		$values = array($download_id,$userid,$file_id,$hashkey);
		$result = $db->Execute($query,$values);

		// retrieve the email template
		if( $file->template_email != '' && $template = $this->GetTemplate($file->template_email) )
		{
			// the template exists, we're going to use it
		}
		else
		{
			// no template specified, or template doesn't exist... we get the default one
			$templatename = $this->GetPreference('default_email_template', '');
			$template = $this->GetTemplate($templatename);
		}

		$href = $gCms->config['root_url']."/modules/DownloadManager/download.php?hashkey=".$hashkey;
		$url = '<a href="'.$href.'" > '.$file->name.'</a>';


		$this->smarty->assign('item',$file);
		$this->smarty->assign('firstname',$params['firstname']);
		$this->smarty->assign('lastname',$params['lastname']);
		$this->smarty->assign('durl',$url);
		$this->smarty->assign('dhref',$href);
		$emailbody = $this->ProcessTemplateFromData($template);
		
		$cmsmailer->AddAddress($params['email'],$params['firstname']." ".$params['lastname']);
		$cmsmailer->SetBody($emailbody);
		$cmsmailer->IsHTML(true);
		$cmsmailer->SetSubject($this->Lang('download').': '.$file->name);

		if($cmsmailer->Send())
		{
			// this shouldn`t be done this way
			echo '<p>'.$this->Lang('emailsent').'</p>';
		}
		else
		{
			$this->DisplayDownloadErrorPage($this->Lang('couldnotsend'));
		}
	}
}

?>

