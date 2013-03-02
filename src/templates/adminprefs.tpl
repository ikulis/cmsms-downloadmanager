{foreach from=$pref_errors item=err}
<div class="pagemcontainer">{$stop_image}{$err}</div>
{/foreach}
{$startform}
	<div class="pageoverflow">
		<p class="pagetext">{$label_download_dir}:</p>
		<p class="pageinput">{$input_download_dir}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$label_download_scan}:</p>
		<p class="pageinput">{$input_download_scan}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$label_download_scan_recurs}:</p>
		<p class="pageinput">{$input_download_scan_recurs}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$label_prefs_search_expires}:</p>
		<p class="pageinput">{$input_search_expires}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$label_thumb_dir}:</p>
		<p class="pageinput">{$input_thumb_dir}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$label_thumb_size}:</p>
		<p class="pageinput">{$input_thumb_size}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$label_thumb_auto}:</p>
		<p class="pageinput">{$input_thumb_auto}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$label_download_template}:</p>
		<br style="clear:left;"/>
		<div style="float:left;width:220px">{$label_download_template_info}</div>
		<p class="pageinput">{$input_download_template}</p>
	</div>
    <div class="pageoverflow">
        <p class="pagetext">{$label_wysiwyg_on}:</p>
        <p class="pageinput">{$input_wysiwyg_on}</p>
    </div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}
