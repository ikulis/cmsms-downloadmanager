<script type="text/javascript">
{literal}
function DMradioupdate()
{{/literal}
  var id = '{$formid}';
{literal}
  document.getElementById(id + 'uploaded').disabled = true;
  document.getElementById(id + 'file').disabled = true;
  document.getElementById(id + 'external').disabled = true;

  if( document.getElementById(id + 'radioupload').checked == true )
  {
    document.getElementById(id + 'file').disabled = false;
  }
  else if ( document.getElementById(id + 'radiouploaded').checked == true)
  {
    document.getElementById(id + 'uploaded').disabled = false;
  }
  else if ( document.getElementById(id + 'radioexternal').checked == true)
  {
    document.getElementById(id + 'external').disabled = false;
	document.getElementById(id + 'md5').disabled = false;
  }

}
</script>
{/literal}

<div class="pageoverflow">
<p class="pagetext"><input type="radio" value="new" name="{$formid}radiou" id="{$formid}radioupload"  onchange="DMradioupdate()" {if $inputuploadedenabled == true}checked="checked"{/if}/>
*{$uploadlabel}:</p>
<p class="pageinput">{$inputfile}</p>
<div class="pageoverflow">
<p class="pagetext"><input type="radio" value="old" name="{$formid}radiou" id="{$formid}radiouploaded"  onchange="DMradioupdate()" {if $inputuploadedenabled == false}checked="checked"{/if}/>
*{$uploadedlabel}:</p>
<p class="pageinput">{$rootpath}/ {$inputuploaded}</p>
</div>
<div class="pageoverflow">
<p class="pagetext"><input type="radio" value="external" name="{$formid}radiou" id="{$formid}radioexternal"  onchange="DMradioupdate()"/>
*{$externallabel}:</p>
<p class="pageinput">http:// {$inputexternal}</p>
<p class="pageinput">{$hashlabel}: {$inputmd5}</p>
</div>