<div class="pageoverflow">{$label_newfiles}</div>
<br/>
<table cellspacing="0" class="pagetable" >
<tbody>
{foreach from=$diff item=entry name=diff}
<tr class="row{$smarty.foreach.diff.index%2 +1}" onmouseover="this.className='rowhover';" onmouseout="this.className='row{$smarty.foreach.diff.index%2 +1}';">
<td style="width:200px;"> 
{$entry}
</td>
</tr>
{/foreach}
</tbody>
</table>
<div style="margin: 10px 30px 10px 10px; width: 97%;">
{$backlink}
</div>
