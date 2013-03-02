{if $itemcount > 0}
<div class="pageoptions"><p class="pageoptions">{$addlink} {$reordercategorylink}</p></div>
<table cellspacing="0" class="pagetable">
	<thead>
	     <colgroup>
	      <col/>
	      <col/>
	      <col/>
	      <col/>
	      <col style="width:16px;"/>
	      <col style="width:16px;"/>
	      <col style="width:16px;"/>
	    </colgroup>
		<tr>
			<th class="pageicon">&nbsp;</th>
			<th>{$categorytext}</th>
			<th>{$aliastext} </th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon" colspan="3">{$tableActionsHeader}</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->id}</td>
			<td>{$entry->name}</td>
			<td>{$entry->alias}</td>
			<td>{$entry->filenbtxt}</td>
			<td>{if $entry->filenb > 1}{$entry->reorderlink}{/if}</td>
			<td>{$entry->editlink}</td>
			<td>{$entry->deletelink}</td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}

<div class="pageoptions"><p class="pageoptions">{$addlink}</p></div>

