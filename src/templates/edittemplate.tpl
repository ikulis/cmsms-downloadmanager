{literal}
<style type= "text/css" media= "screen">
td {vertical-align:top;}
</style>
{/literal}
{$startform}

<div class='pageoverflow'><p class='pagetext'>*{$template_namelabel}:</p>
<p class='pageinput'>{$inputtemplate_name}</p></div>

<div class="pageoverflow">
<p class="pagetext">{$contentlabel}:</p>
<br style="clear:left;"/>
<div style="float:left;width:280px">
<table style="text-align:left;">
{$help_template_variables}
{$help_template_general}
</table></div>
<p class="pageinput">{$inputcontent}</p>
</div>


<div class="pageoverflow">
<p class="pagetext"> </p>
<p class="pageinput">{$hidden}{$apply}{$submit}{$cancel}</p>
</div>
{$endform}
