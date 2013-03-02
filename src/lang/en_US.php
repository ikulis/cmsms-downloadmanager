<?php
$lang['friendlyname'] = 'Download Manager';
$lang['postinstall'] = 'Be sure to set "Use Download Manager" permissions to use this module!. <br/> Remember to create downloads/ dir in root cms dir and give it 0777 permissions';
$lang['postuninstall'] = 'Download Manager uninstall';
$lang['really_uninstall'] = 'Really? You\'re sure you want to uninstall this fine module?';
$lang['uninstalled'] = 'Module Uninstalled.';
$lang['installed'] = 'Module version %s installed.';
$lang['prefsupdated'] = 'Module preferences updated.';
$lang['accessdenied'] = 'Access Denied. Please check your permissions.';
$lang['download_error_msg'] = 'Error occured while processing this file';
$lang['none'] = 'None';
$lang['submitreqemail'] = 'Submit to receive link to download';
$lang['error'] = 'Error!';
$lang['back'] = 'Go back';
$lang['actions'] = 'Actions';
$lang['rootcategory'] = 'Root category';
$lang['upgraded'] = 'Module upgraded to version %s.';
$lang['title_mod_prefs'] = 'Module Preferences';
$lang['title_mod_admin'] = 'Module Admin Panel';
$lang['title_admin_panel'] = 'Download Manager';
$lang['nonamegiven'] = 'You must enter a name.';
$lang['mustlogin'] = 'You must log in to download this file:';
$lang['mayalsologin'] = 'You may also log in to download this file:';

$lang['moddescription'] = 'This module allows to Manage Downloads.';
$lang['welcome_text'] = '<p>Welcome to the DownloadManager admin section.</p>';

$lang['changelog'] = '<ul>
<li>Version 0.1 alpha - first alpha</li>
<li>Version 0.2 beta - item reorder added</li>
<li>Version 0.3 beta - making module more user-friendly</li>
<li>Version 0.4 - fixed install bug which unables associations, added associations in file adding</li>
<li>Version 0.8.4 - added category selector, fixed some bugs</li>
<li>Version 1.0-rc1  - added integration with feu module and sending file link by email, made categories walkable, different templates for different action types, changed category_selector parameter into selector, removed category parameter, added category list for files in selector, added aliases to files and categories</li>
<li>Version 1.1  - added the filechange functionality</li>
<li>Version 1.2  - added scanning functionality</li>
<li>Version 1.3  - added download counting</li>
</ul>';

$lang['help'] = '<h3>What Does This Do?</h3>
<p>Allows to manage Downloads from admin panel.</p>
<h3>How Do I Use It</h3>
<p>First make downloads/ directory in root cms directory. Next give it 0777 mode, depending on apache configuration could be 0775 or 0755.</p>
<p>To acces module admin interface go to Content -> Download Manager.</p>
<p>Install it by placing the module in a page or template using the smarty tag &#123;cms_module module=\'DownloadManager\'}. See more examples below.</p>
<h3>Support</h3>
<p>This module does not include commercial support.</p>
<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>
<h3>Translations</h3>
<p>Newest translations are always available at
<a href="http://svn.cmsmadesimple.org/svn/translatecenter/modules/DownloadManager/lang/ext/" title="Download manager tranlations">http://svn.cmsmadesimple.org/svn/translatecenter/modules/DownloadManager/lang/ext/</a></p>
<h3>Copyright and License</h3>
<p>Copyright &copy; 2008, Szymon Łukaszczyk <a href="mailto:szymon.lukaszczyk@gmail.com">&lt;szymon.lukaszczyk@gmail.com&gt;</a> & Pierre-Luc Germain <a href="mailto:pl.germain@gmail.com">&lt;pl.germain@gmail.com&gt;</a>. All Rights Are Reserved.</p>
<p>German translation by Connie Müller-Gödecke <a href="mailto:connie.mueller-goedecke@webdeerns.de">&lt;connie.mueller-goedecke@webdeerns.de&gt;</a>.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>
';
$lang['help_param_action']='Action choosen. Possible actions: 
<ul>
<li><em>default</em>: shows file list
	<br/>optional parameters: <em>categoryid</em> or <em>alias</em>, <em>depth</em>, <em>template</em>
	<br/>ex:  &#123;cms_module module=\'DownloadManager\' alias=\'my-category\' depth=\'2\'} </li>
<li><em>selector</em>: allow to select multiple categories, see selector parameter
	<br/>required parameters: <em>selector</em>
	<br/>optional parameters: <em>template</em>
	<br/>ex:  &#123;cms_module module=\'DownloadManager\' action=\'selector\' selector=\'3&(1|2)\' } </li>
<li><em>detail</em>: shows details of selected file
	<br/>required parameters: <em>alias</em>
	<br/>optional parameters: <em>template</em>
	<br/>ex:  &#123;cms_module module=\'DownloadManager\' action=\'detail\' alias=\'my-file\' } </li>
<li><em>download</em>: downloads of selected file
	<br/>required parameters: <em>alias</em>
	<br/>optional parameters: <em>template</em> 
	<br/>ex:  &#123;cms_module module=\'DownloadManager\' action=\'download\' alias=\'my-file\' } </li>
</ul>';
$lang['help_param_categoryid']='Category id, could be found in categories tab. If selected, alias is not taken into account.';
$lang['help_param_alias']='Category or file alias.';
$lang['help_param_depth']='Allow to specify the depth of tree shown by default action. If not set full tree will be shown.';
$lang['help_param_template']='Template name. If not given the default template for current action is action/file/category.';
$lang['help_param_selector']='The selector which allows to choose more than one category. The selector syntax can have:
<ul>
<li>"&" - intersection</li>
<li>"|" - sum</li>
<li>category id (you can see it in categories tab)</li>
<li>all other characters will be cleaned before parsing</li>
</ul>
For example selector="3&(1|2)" will choose all elements from category with id=3 which are also in sum of category 1 and 2. <br/>
If selector is specified category parameter will <b>not</b> be parsed.
';

$lang['wrongalias'] = 'Wrong alias given.';
$lang['download'] = 'Download';
$lang['downloading']  = 'Downloading %s...';
$lang['backtolist']  = 'Back to the file list.';
$lang['back']  = 'Go back.';
$lang['downloading_info'] = 'Your download should begin shortly. If you encounter problems, please contact the Webmaster.';
$lang['nofile'] = 'No file given';
$lang['wrongfile'] = 'Wrong file id';
$lang['noaccess'] = 'You do not have access to this file.';

//*********** Preferences ****************************
$lang['download_dir']  = 'Download directory';
$lang['wysiwyg_on']  = 'WYSIWYG on in admin tabs?';
$lang['download_scan']  = 'Scanned directories (sepeated with semicolon ex. "dir1/;dir2/;dir3/" )';
$lang['download_scan_recurs']  = 'Scan directories recursive?';
$lang['download_template']  = 'Download template';
$lang['download_template_info']  = '<table style="text-align:left;">
<tr><td><b>Variable:</b></td><td><b>What is it?</b></td></tr>
<tr><td style="vertical-align:top;"><b>&#123;$downloading&#125;</b></td><td>Text: '.$lang['downloading'].'</td></tr>
<tr><td style="vertical-align:top;"><b>&#123;$downloading_info&#125;</b></td><td>Text: '.$lang['downloading_info'].'</td></tr>
<tr><td style="vertical-align:top;"><b>&#123;$download_name&#125;</b></td><td>Name of currently downloading file.</td></tr>
<tr><td style="vertical-align:top;"><b>&#123;$backtolistlink&#125;</b></td><td>Back to the file list link.</td></tr>
</table>
';
$lang['download_dir_not_writable'] = 'Download directory (%s) is not writable, try chmod 777.';
$lang['dir_dont_exists'] = 'Download directory (%s) does not exist, create it first.';
$lang['search_expire'] = 'Expired and not started downloads can appear in search results';
$lang['thumb_dir'] = 'Directory to scan/save thumbnails. Default: "images/DownloadManagerThumbs/". Directory must exist';
$lang['thumb_size'] = 'Max thumbnail size';
$lang['thumb_auto'] = 'Do try automaticly to make thumbnails?';

//*********** File labels ****************************
$lang['namelabel']='Name';
$lang['counterlabel']='Download counter';
$lang['counternbr']='Downloaded %s time(s).';
$lang['aliaslabel']='Alias';
$lang['selector']='Category selector';
$lang['filter']='Filter';
$lang['nofilter']='No filter';
$lang['noselector']='No selector';
$lang['current']='Current';
$lang['verbose']='verbose';
$lang['filelabel']='File';
$lang['filedeleted']='File was deleted.';
$lang['uploadfile']='Upload new file';
$lang['alreadyuploaded']='Use already uploaded file';
$lang['externalfile']='Use external file accessible through http. File will not be downloaded to server';
$lang['filedoesntexists']='File %s doesn`t exist on server.';
$lang['curlfailed']='Curl request failed.';
$lang['curldoesntexists']='Curl php extension needed for external links not present.';
$lang['server_namelabel']='Orginal path';
$lang['extlabel']='Extension';
$lang['sizelabel']='Size';
$lang['hashlabel']='Md5 hash';
$lang['descriptionlabel']='Description';
$lang['createdlabel']='Creation time';
$lang['expireslabel']='Expires';
$lang['make_thumblabel']='Make thumbnail from image (if possible)?';
$lang['thumblabel']='Thumbnail';
$lang['startslabel']='Starts';
$lang['visiblelabel']='Visible';
$lang['useexpiration']='Use expiration date';
$lang['usestart']='Use start date';
$lang['addfile']='New file';
$lang['scanfornewfiles']='Scan for new files';
$lang['scanresult']='Scanned directories: %s <br /> %s new files listed below. Choose file to add.';
$lang['scannonew']='No new files added.';
$lang['nofilegiven']='There was no file';
$lang['nothinguploaded']='No file or empty file was uploaded.';
$lang['unabletomove']='Unable to move or copy uploaded file.';
$lang['typelabel']='Type';
$lang['filestabheader']='Files';
$lang['fileadded']='File succesfully added';
$lang['filemodified']='File succesfully modified';
$lang['templateforform'] = 'Form template';
$lang['templatefordetail'] = 'Detail template';
$lang['templateforemail'] = 'Email template';
$lang['defaulttemplate'] = 'Default category template';
$lang['feugroupslabel'] = 'FEU groups who can access the file, if no group selected all FEU users can access';
$lang['accesstype'] = 'Access type';
$lang['accesstype_free'] = 'Free access';
$lang['accesstype_feuonly'] = 'Frontend Users only';
$lang['accesstype_emailonly'] = 'Email only';
$lang['accesstype_mixed'] = 'FEU or Email';
$lang['details']='Details';
$lang['filechange']='Change this file.';
$lang['changefileinfo']='Choose new file to change the current "%s" (alias:%s) file. All associations, links, info and alias will stay the same.';
$lang['filechanged']='File was successfully replaced';
$lang['changenamelabel']='Change the file name to the one from new file?';
$lang['alltime']='All time';
$lang['availablity']='Availablity';
//*********** Errors ****************************
$lang['noalias']="No such alias or download not available";
$lang['nohash']="No such hash or download not available";
$lang['wrongparams']="Wrong parameters";
$lang['filenotfound']="File %s not found.";
$lang['wrongdata']="Wrong data in database row.";

//*********** Category labels ****************************

$lang['addcategory'] = 'New Category';
$lang['category'] = 'Category' ;
$lang['parent_idlabel'] = 'Parent Category';
$lang['categoriestabheader']='Categories';
$lang['reorder_priority']='Priority (-99&nbsp;to&nbsp;99),  lower->better ';
$lang['nodownloads'] = 'No downloads in this category';
$lang['filenb'] = '%s&nbsp;file(s)';
$lang['categoryadded'] = 'The category was successfully added.';
$lang['categoryupdated'] = 'The category was successfully updated.';
$lang['categorydeleted'] = 'The category was successfully deleted.';

$lang['categorycontenttypefriendly'] = 'Download Category' ;
$lang['overalltabheader']='Overall';
$lang['assocationstabheader']='Categories';
$lang['addassoc']='Add to category';
$lang['wrongcategorysel']='Category selector parse error. <br /> Selector after cleaning: %s';
$lang['nocategorysel']='Category selector not set.';
$lang['wrongcategory']='Selected category does not exists.';


//*********** EMAIL ****************************

$lang['sendemail'] = 'Email download link';
$lang['prompt_firstname'] = 'First name';
$lang['prompt_lastname'] = 'Last name';
$lang['prompt_email'] = 'Email';
$lang['prompt_mailinglist'] = 'I would like to hear about new files and important news';
$lang['emailsent'] = 'An email containing the download link was sent to the specified address.';
$lang['couldnotsend'] = 'There was an error with your request, and the email could not be sent.';
$lang['invalidemail'] = 'The email you have entered is invalid. Without a valid email, we cannot send you a download link, and you cannot download the file.';
$lang['please_name'] = 'Please enter your name.';
$lang['please_email'] = 'Please enter your email address. The download link will be sent to this address.';


//*********** Template labels ****************************

$lang['addtemplate'] = 'New Template';
$lang['listtemplates'] = 'Templates to display lists of categories or files';
$lang['addlisttemplate'] = 'New List Template';
$lang['detailtemplates'] = 'Templates to display the details of a file';
$lang['adddetailtemplate'] = 'New Detail Template';
$lang['formtemplates'] = 'Templates for the form to request a download link by mail';
$lang['addformtemplate'] = 'New Form Template';
$lang['emailtemplates'] = 'Templates for emails sent with download link';
$lang['addemailtemplate'] = 'New Email Body Template';
$lang['defaulttemplate'] = 'Use the default template (see templates tab)';

$lang['prompt_deletetemplate'] = 'Do you really want to delete the template %s?';
$lang['title_template'] = 'Template' ;
$lang['contentlabel'] = 'Content';
$lang['template_namelabel'] = 'Name';

$lang['templateadded'] = 'The template was successfully added.';
$lang['templateupdated'] = 'The template was successfully updated.';
$lang['templatedeleted'] = 'The template was successfully deleted.';

$lang['wrongtemplate'] = 'Wrong template specified.';
$lang['alreadyexist'] = 'There is already a template by that name.';

$lang['general_templatehelp'] = '
<tr><td><b>&#123;$backtolistlink&#125;</b></td><td>Return back in history link</td></tr>
';
$lang['fileitem_templatehelp']  ='
<tr><td><b>&#123;$item->name&#125;</b></td><td>file name</td></tr>
<tr><td><b>&#123;$item->description&#125;</b></td><td>file description</td></tr>
<tr><td><b>&#123;$item->size&#125;</b></td><td>file size</td></tr>
<tr><td><b>&#123;$item->counter&#125;</b></td><td>times the file was downloaded</td></tr>
<tr><td><b>&#123;$item->counterlocale&#125;</b></td><td>text: '.$lang['counternbr'].'</td></tr>
<tr><td><b>&#123;$item->hash&#125;</b></td><td>md5 hash sum</td></tr>
<tr><td><b>&#123;$item->ext&#125;</b></td><td>file extension (jpg,pdf,tar.bz,etc.)</td></tr>
<tr><td><b>&#123;$item->created&#125;</b></td><td>time, when file was uploaded to server</td></tr>
<tr><td><b>&#123;$item->expires&#125;</b></td><td>expiration time, after this - file is no longer available</td></tr>
<tr><td><b>&#123;$item->starts&#125;</b></td><td>start time, after this file is available</td></tr>
<tr><td><b>&#123;$item->thumb&#125;</b></td><td>thumbnail image</td></tr>
<tr><td><b>&#123;$item->thumb_path&#125;</b></td><td>thumbnail path</td></tr>
';

$lang['detail_templatehelp'] = $lang['fileitem_templatehelp'].'
<tr><td><b>&#123;$item->categories&#125;</b></td><td>categories to which file belong (available in selector and detail)</td></tr>
<tr><td><b>&#123;$item->download&#125;</b></td><td>generated download link</td></tr>
<tr><td><b>&#123;$item->href&#125;</b></td><td>href to file download</td></tr>
<tr><td><b>&#123;$item->accesstypes&#125;</b></td><td>array of human readable access types (free,feu,mail)</td></tr>
<tr><td><b>&#123;$item->detail&#125;</b></td><td>generated detail link</td></tr>
';
$lang['form_templatehelp'] = '
<tr><th colspan="2" style="color:#777"><i>Inputs</i>:</th></tr>
<tr><td><b>&#123;$firstname_input&#125;</b></td><td>firstname</td></tr>
<tr><td><b>&#123;$lastname_input&#125;</b></td><td>lastname</td></tr>
<tr><td><b>&#123;$email_input&#125;</b></td><td>email</td></tr>
<tr><td><b>&#123;$mailinglist_input&#125;</b></td><td>mailinglist checkbox</td></tr>
<tr><td><b>&#123;$submit&#125;</b></td><td>submit</td></tr>
<tr><th colspan="2" style="color:#777"><i>Labels</i>:</th></tr>
<tr><td><b>&#123;$firstname_label&#125;</b></td><td>firstname</td></tr>
<tr><td><b>&#123;$lastname_label&#125;</b></td><td>lastname</td></tr>
<tr><td><b>&#123;$email_label&#125;</b></td><td>email</td></tr>
<tr><td><b>&#123;$mailinglist_label&#125;</b></td><td>mailinglist checkbox</td></tr>
';
$lang['email_templatehelp'] = '
<tr><td><b>&#123;$firstname&#125;</b></td><td>firstname of user downloading the file</td></tr>
<tr><td><b>&#123;$lastname&#125;</b></td><td>lastname</td></tr>
<tr><td><b>&#123;$durl&#125;</b></td><td>link for downloading the file</td></tr>
<tr><td><b>&#123;$dhref&#125;</b></td><td>uri for downloading the file</td></tr>
'.$lang['detail_templatehelp'].'
';
$lang['list_templatehelp'] = '
<tr><th colspan="2" style="text-align:center;"><br/><i> In the &#123;foreach from=$items item=item&#125; loop:</i></th></tr>
<tr><th>Variable:</th><th>What is it?</th></tr>
<tr><td><b>&#123;$item->itemlevel&#125;</b></td><td>actual category-level</td></tr>
<tr><td><b>&#123;$item->rowclass&#125;</b></td><td>row1 or row2 depending on parity</td></tr>

<tr><th colspan="2" style="color:#777"><i> When <b>&#123;$item->itemtype&#125;</b> = <i>header</i>:</th></tr>
<tr><td><b>&#123;$item->itemtype&#125;</b></td><td><i>header</i></td></tr>
<tr><td><b>&#123;$item->name&#125;</b></td><td>category name</td></tr>
<tr><td><b>&#123;$item->description&#125;</b></td><td>category description</td></tr>
<tr><td><b>&#123;$item->subcategories&#125;</b></td><td>array of subcategories, each subcategory has elements: id, name, long_name, alias, filesnb, items, parent</td></tr>

<tr><th colspan="2" style="color:#777" ><i> When <b>&#123;$item->itemtype&#125;</b> = <i>file</i>:</th></tr>
<tr><td><b>&#123;$item->itemtype&#125;</b></td><td><i>file</i></td></tr>
'.$lang['detail_templatehelp'].'

<tr><th colspan="2" style="text-align:center;"><br/><i>In whole template:</i></th></tr>
<tr><td><b>Variable:</b></td><td><b>What is it?</b></td></tr>
<tr><td><b>&#123;$itemcount&#125;</b></td><td>number of items in $items</td></tr>
<tr><td><b>&#123;$nodownloads&#125;</b></td><td>message then there are no downloads in selected category</td></tr>
';
?>
