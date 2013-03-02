<div class="pageoptions" style="margin-bottom:10px;">
<p class="pageoptions" >{$addlink} {$scanlink} </p>
<form method="POST" style="float:left;margin:10px;" action="{$input_category_selector_url}"> 
    {$category_selector}: {$input_category_selector} 
    <input type="submit"/> <br/>
    {$current}: {$value_category_selector|default:$noselector} <br/>
    {$verbose|ucwords}: {$value_category_selector_verbose|default:$noselector}
</form> 
<form method="POST" style="float:left;margin:10px;" action="{$input_category_selector_url}"> 
    {$filter}: {$input_filter} 
    <input type="submit"/> <br/>
    {$current}: {$value_filter|default:$nofilter} <br/>
</form> 

</div>

<table cellspacing="0" class="pagetable" >
<thead>
    	     <colgroup>
	      <col/>
	      <col/>
	      <col/>
	      <col/>
	      <col/>
          <col/>
	      <col style="width:16px;"/>
	      <col style="width:16px;"/>
	      <col style="width:16px;"/>
	    </colgroup>
<tr>
<th class="pageicon">{$tableIDColHeader}</th>
<th>{$tableNameColHeader}</th>
<th>{$tableDescriptionColHeader}</th>
<th class="pageicon">{$tablecounterheader}</th>
<th class="pageicon">{$tablevisibleheader}</th>
<th class="pageicon" style="width:150px">{$tableavailablityheader}</th>
<th class="pageicon" colspan="3">{$tableActionsHeader}</th>
</tr>
</thead>
{if $itemcount > 0}
<tbody>
{foreach from=$items item=entry}
<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
<td>{$entry->id}</td>
<td style="width:200px;"> {$entry->name}</td>
<td>{$entry->description|truncate:100}</td>
<td>{$entry->counter}</td>
<td>{$entry->visible}</td>
<td>{$entry->available}</td>
<td>{$entry->changelink}</td>
<td>{$entry->editlink}</td>
<td>{$entry->deletelink}</td>
</tr>
{/foreach}
</tbody>
{/if}
</table>


<div class="pageoptions">
<p class="pageoptions">{$addlink}</p>
</div>
