<h3> Details of {$item->name} </h3>

{$item->thumb}
<table>
<tr><td>{$sizelabel}</td><td>{$item->size}</td></tr>
<tr><td>{$hashlabel}</td><td>{$item->hash}</td></tr>
<tr><td>{$descriptionlabel}</td><td>{$item->description}</td></tr>
<tr><td>{$createdlabel}</td><td>{$item->created}</td></tr>
<tr><td>{$accesstypelabel}</td><td>{foreach from=$item->accesstypes item=access}<img src="/modules/DownloadManager/images/icons/access-{$access}.png" title="{$access} access" alt="{$access} access"/>{/foreach}</td></tr>
<tr><td>Belongs to categories</td><td>{$item->categories}</td></tr>
</table>
<br/>
{$item->counterlocale}
<br/>
Download {$item->download}.