{$startform}

<div class="pageoverflow">
<p class="pagetext">*{$namelabel}:</p>
<p class="pageinput">{$inputname}</p>
</div>

<div class='pageoverflow'><p class='pagetext'>{$descriptionlabel}:</p>
<p class='pageinput'>{$inputdescription}</p></div>

<div class='pageoverflow'><p class='pagetext'>{$parent_idlabel}:</p>
<p class='pageinput'>{$inputparent_id}</p></div>

{if !empty($hidden)}
<div class='pageoverflow'><p class='pagetext'>{$aliaslabel}:</p>
<p class='pageinput'>{$inputalias}</p></div>
{/if}

<div class='pageoverflow'><p class='pagetext'>{$default_templatelabel}:</p>
<p class='pageinput'>{$inputdefault_template}</p></div>

<div class="pageoverflow">
<p class="pagetext"> </p>
<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
</div>
{$endform}
