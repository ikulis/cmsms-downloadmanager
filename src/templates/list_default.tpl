{literal}
<style type= "text/css" media= "screen">
.downloads {width:90%;margin:0 5%;}
.downloads h3 {text-align:center;border-bottom:1px solid black;margin:1em 0.5em 0.5em !important;text-align:right;}
.downloads h4 {text-align:center;border-bottom:1px dotted black;margin:1em 0.5em 0.5em !important;text-align:right;font-size:1.1em;}
.downloads h5 {text-align:center;border-bottom:1px dotted black;margin:1em 0.5em 0.5em !important;text-align:right;font-size:1em;}
.downloads .file {border:1px solid black;margin:2px;padding:5px;}
.downloads .description {width:80%;}
.downloads .icons {display:block;float:right;}
.downloads .small {font-size:0.8em;}
.downloads .title {font-size:1.1em;background-color:#fff;width:80%;text-align:center;}
.downloads p {margin:0;padding:0;}
.downloads .row1 {background-color:#ddd;}
.downloads .row2 {background-color:#bbb;}
</style>
{/literal}
<div class="downloads">
{if $topcategorylink != '' } Upper category: {$topcategorylink} {/if}
{foreach from=$items item=item}
    {if $item->itemtype == 'header' }
       {if $item->itemlevel == 0 }
           <h3> Header 0: {$item->name} </h3>
       {elseif $item->itemlevel == 1 }
           <h4> Header 1: {$item->name} </h4>
       {elseif $item->itemlevel == 2 }
           <h5> Header 2: {$item->name} </h5>
       {/if}
           {if $item->description != '' }<span class="small">Description:</span><br /> {$item->description} {/if}
	   {if count($item->subcategories) > 0 }
           <span class="small">Subcategories:{foreach from=$item->subcategories item=subcat}{if $subcat.filesnb > 0} {$subcat.link}{/if}
	   {/foreach}{/if}</span><br />
    {/if}
    {if $item->itemtype == 'file' }

           <div class="file {$item->rowclass}">
			<div class="icons"> {$item->size}
				<img src="/modules/DownloadManager/images/icons/calculator.png" title="Md5: {$item->hash}" alt="Md5: {$item->hash}"/>
				<a href="{$item->href}" title="{$item->name}"><img src="/modules/DownloadManager/images/icons/drive_disk.png"/></a>
{foreach from=$item->accesstypes item=access}
<img src="/modules/DownloadManager/images/icons/access-{$access}.png" title="{$access} access" alt="{$access} access"/>
{/foreach}
			</div>
			<p class="title"> {$item->thumb} {$item->download} </p>
	    {if $item->categories != '' }<p><span class="small">Categories:</span><br /> {$item->categories}</p>{/if}
<p><span class="small">{$item->counterlocale}</span></p>
<p><span class="small">{$item->detail}</span></p>
			<div style="clear:right;"> </div>
		</div>
    {/if}
{foreachelse}
    {$nodownloads}
{/foreach}
</div>
