{$startform}

{$filetypeinput}

<div class='pageoverflow'><p class='pagetext'>{$accesstypelabel}:</p>
<p class='pageinput'>{$inputaccesstype}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$feugroupslabel}:</p>
<p class='pageinput'>{$inputfeugroups}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$descriptionlabel}:</p>
	<p class='pageinput'>{$inputdescription}</p></div>
<div class="pageoverflow">
	<p class="pagetext">{$useexpirationtext}:</p>
	<p class="pageinput">{$inputexp}</p>
</div>
<div class='pageoverflow'><p class='pagetext'>{$expireslabel}:</p>
<p class="pageinput">{html_select_date prefix=$expiredateprefix time=$inputexpires start_year="-10" end_year="+15"} {html_select_time prefix=$expiredateprefix time=$inputexpires}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$visiblelabel}:</p>
<p class='pageinput'>{$inputvisible}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$make_thumblabel}:</p>
<p class='pageinput'>{$inputmake_thumb}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$thumb_pathlabel}:</p>
<p class='pageinput'>{$inputthumb_path}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$template_detaillabel}:</p>
<p class='pageinput'>{$inputtemplate_detail}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$template_formlabel}:</p>
<p class='pageinput'>{$inputtemplate_form}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$template_emaillabel}:</p>
<p class='pageinput'>{$inputtemplate_email}</p></div>

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
<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
</div>
{$endform}

