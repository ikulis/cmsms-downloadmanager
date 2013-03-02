{$startform}
{if $itemcount > 0}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th style="width:100px;">{$prioritytext}</th>
			<th>{$itemtext}</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td style="text-align:right">{$entry->priority}</td>
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