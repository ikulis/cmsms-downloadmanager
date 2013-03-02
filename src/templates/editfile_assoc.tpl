{$startformassoc}
<table cellspacing="0" class="pagetable">
	<thead>
		<tr>
			<th class="pageicon">&nbsp;</th>
			<th>{$categorytext}</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$associations item=entry}
		<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
			<td>{$entry->inputassoc}</td>
			<td>{$entry->name}</td>
		</tr>
{/foreach}
	</tbody>
</table>

<div class="pageoverflow">
	<p class="pagetext"> </p>
	<p class="pageinput">{$hidden}{$selector}{$submit}{$cancel}</p>
</div>
{$endformassoc}