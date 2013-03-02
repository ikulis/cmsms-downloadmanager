
{$startform}
<div class='pageoverflow'>
	<p class='pagetext'>{$visiblelabel}:</p>
	<p class='pageinput'>{$inputvisible}</p>
</div>
<div class="pageoverflow">
	<p class="pagetext">*{$namelabel}:</p>
	<p class="pageinput">{$inputname}</p>
</div>

<div class="pageoverflow">
	<p class="pagetext">*{$aliaslabel}:</p>
	<p class="pageinput">{$inputalias}</p>
</div>

<div class='pageoverflow'><p class='pagetext'>{$extlabel}:</p>
<p class='pageinput'>{$inputext}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$server_namelabel}:</p>
<p class='pageinput'>{$inputserver_name}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$typelabel}:</p>
<p class='pageinput'>{$inputtype}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$sizelabel}:</p>
<p class='pageinput'>{$inputsize}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$hashlabel}:</p>
<p class='pageinput'>{$inputhash}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$descriptionlabel}:</p>
<p class='pageinput'>{$inputdescription}</p></div>
<div class="pageoverflow">
	<p class="pagetext"> </p>
	<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
</div>
{$extratab}
<div class='pageoverflow'><p class='pagetext'>{$createdlabel}:</p>
<p class='pageinput'>{$inputcreated}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$thumb_pathlabel}:</p>
<p class='pageinput'>{$inputthumb_path}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$accesstypelabel}:</p>
<p class='pageinput'>{$inputaccesstype}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$feugroupslabel}:</p>
<p class='pageinput'>{$inputfeugroups}</p></div>

<div class="pageoverflow">
    <p class="pagetext">{$usestarttext}:</p>
    <p class="pageinput">{$inputstart}</p>
</div>
<div class='pageoverflow'>
    <p class='pagetext'>{$startlabel}:</p>
    <p class="pageinput">{html_select_date prefix=$startdateprefix time=$inputstarts start_year="+0" end_year="+15"} {html_select_time prefix=$startdateprefix time=$inputstarts}</p>
</div>

<div class="pageoverflow">
	<p class="pagetext">{$useexpirationtext}:</p>
	<p class="pageinput">{$inputexp}</p>
</div>
<div class='pageoverflow'>
	<p class='pagetext'>{$expireslabel}:</p>
	<p class="pageinput">{html_select_date prefix=$expiredateprefix time=$inputexpires start_year="-10" end_year="+15"} {html_select_time prefix=$expiredateprefix time=$inputexpires}</p>
</div>


<div class='pageoverflow'><p class='pagetext'>{$template_detaillabel}:</p>
<p class='pageinput'>{$inputtemplate_detail}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$template_formlabel}:</p>
<p class='pageinput'>{$inputtemplate_form}</p></div>
<div class='pageoverflow'><p class='pagetext'>{$template_emaillabel}:</p>
<p class='pageinput'>{$inputtemplate_email}</p></div>
<div class="pageoverflow">
	<p class="pagetext">{$counterlabel}:</p>
	<p class="pageinput">{$inputcounter}</p>
</div>
<div class="pageoverflow">
	<p class="pagetext"> </p>
	<p class="pageinput">{$hidden}{$selector}{$submit}{$cancel}</p>
</div>
{$endform}

