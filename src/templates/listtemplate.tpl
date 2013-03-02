<h3>{$title}</h3>
<table cellspacing="0" class="pagetable" >
<thead>
<tr>
<th>{$tableNameColHeader}</th>
<th class="pageicon">{$tableDefaultColHeader}</th>
<th class="pageicon"> </th>
<th class="pageicon"> </th>
<th class="pageicon"> </th>
</tr>
</thead>
<tbody>
{foreach from=$items item=entry}
<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
<td>{$entry->namelink}</td>
<td>{$entry->toggledefault}</td>
<td>{$entry->duplicatelink}</td>
<td>{$entry->editlink}</td>
<td>{$entry->deletelink}</td>
</tr>
{/foreach}
</tbody>
</table>
<p>{$addlink}</p>
<p></p>
