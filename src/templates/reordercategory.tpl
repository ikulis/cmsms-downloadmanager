{$startform}
{if $itemcount > 0}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th>{$categorytext} - {$prioritytext}</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->name}</td>
		</tr>
{/foreach}
	</tbody>
</table>

{/if}
<div class="pageoverflow">
<p class="pagetext"> </p>
<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
</div>
{$endform}
<div class="pageoptions"><p class="pageoptions">{$addlink}</p></div>

