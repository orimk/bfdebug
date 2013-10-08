{*
  Safely echoes out a handful of common caching variables.
  Useful when debugging performance of environments.

  Use with {include uri="design:bfdebugcache.tpl"}
*}

<!--
bfdebugcache

Time: {currentdate()|datetime('custom','%Y-%m-%d %H:%i:%s')}
Random: {rand(0,100000)}

[ContentSettings]
ViewCaching ........... {ezini('ContentSettings', 'ViewCaching',     'site.ini')}
CachedViewModes ....... {ezini('ContentSettings', 'CachedViewModes', 'site.ini')}
PreViewCache .......... {ezini('ContentSettings', 'PreViewCache',    'site.ini')}
StaticCache ........... {ezini('ContentSettings', 'StaticCache',     'site.ini')}

[DesignSettings]
DesignLocationCache ... {ezini('DesignSettings', 'DesignLocationCache', 'site.ini')}

[OverrideSettings]
Cache ................. {ezini('OverrideSettings', 'Cache', 'site.ini')}

[TemplateSettings]
Debug ................. {ezini('TemplateSettings', 'Debug',                'site.ini')}
DevelopmentMode ....... {ezini('TemplateSettings', 'DevelopmentMode',      'site.ini')}
TemplateCache ......... {ezini('TemplateSettings', 'TemplateCache',        'site.ini')}
TemplateOptimization .. {ezini('TemplateSettings', 'TemplateOptimization', 'site.ini')}
TemplateCompile ....... {ezini('TemplateSettings', 'TemplateCompile',      'site.ini')}
NodeTreeCaching ....... {ezini('TemplateSettings', 'NodeTreeCaching',      'site.ini')}
-->