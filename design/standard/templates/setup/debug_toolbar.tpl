<div class="block debug-toolbar">

<div class="element">
<h3>Clear cache:</h3>
{include uri='design:setup/clear_cache.tpl'}
</div>

<div class="element">
<h3>Quick settings:</h3>
{let siteaccess=is_set( $access_type )|choose( '', $access_type.name )
     select_siteaccess=false}
{include uri='design:setup/quick_settings.tpl'}
{/let}
</div>

<div class="element">
<h3>Debug level:</h3>
{let siteaccess=is_set( $access_type )|choose( '', $access_type.name )
     select_siteaccess=false}
{include uri='design:setup/debug_toggle.tpl'}
{/let}
</div>

<div class="break"></div>

</div>
