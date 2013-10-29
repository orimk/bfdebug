bfdebug eZPublish extension
=================

Provides enhanced debug functionality for eZ Publish.
Specifically, it  

1. Offers four template operators: bfdebug, bfdebugoverrides, bfdebugvars, bfdebugoperators, bfdebugDisableAll. Read usage to see what they do... this all started with bfdebug, which was a Coldfusion-like expandable debug output.
2. bfdebugcache template (used as an include)
3. Adds the "spiderFindTemplate.php" script
4. Reworks the debug bar below so that we can easily turn on and off segments of dev

Software Dependencies:
--------------------------------

Written in eZPublish 4.4, and tested in eZPublish 4.6-4.7.
Dependencies: None.

Multilanguage considerations:
--------------------------------

None.

Multisite considerations:
--------------------------------

None.

Installation and Setup
--------------------------------

bfdebug is an eZ extension. Just drop it into your extensions and enable it.

Usage
--------------------------------

### bfdebug operator

Provides nicely formatted layered debug output. A replacement for `attribute(show)`.

**Dependencies:** jQuery loaded somewhere in the page.

**Usage:** `{$item|bfdebug($depth, $params)}`

* `$depth` is how many levels deep we display *(DEFAULT: 1)*
* `$params` *array*
    * `pos => (tail|top|inline|custom)`
        * `tail` - after the last body element, 
        * `top` - before the first body element,
        * `inline` - no special positioning (DEFAULT),
        * `custom:{before|after}` - (use posDetailPath to describe)
    * `posJqueryPath` (jQuery path where the debug statement will be placed) - example: `$("body").children().last()`
 

### bfdebugvars operator

Returns all local, global, and namespaced variable names available to the current template.

**Example:** `{bfdebugvars()|bfdebug(2)}`


### bfdebugoverrides operator

Returns all conditions and their values that could be used to do an override in the current module/page.

**Example:** `{bfdebugoverrides()|bfdebug(2)}`


### bfdebugoperators operator

Returns all template operators, functions and function attributes available.

**Example:** `{bfdebugoperators()|bfdebug(2)}`


### bfdebugcache template

Echoes out many common cache settings in a comment. Useful for verifying cache settings on a production website.

After including this template, view source and search for "bfdebugcache".

Take out the include once you're done. Not that it would really hurt anything to leave it, but it's bad form.

**Example:** `{include uri="design:bfdebugcache.tpl"}`

### spiderFindTemplate.php script

Used to find all places a template is used by spidering all ez nodes, and examining output. If the correct unique string is found, the script will offer a stacktrace-like output to find out what the sequence of template inclusion/execution was.

To use, 

1. find the template in question (either a template, or an override template, or an include... doesn't matter).
2. in there, insert some unique text that the script will look for
3. run the script with a set of starting nodes, the URL to use, and your unique string. 
4. the script will descend down all the subtrees of the given nodes, pull their output, and look for the unique string

You would use this script if you arrive at an unknown site, and want to know precisely what the impact will be of changing a generic "listitem" rendering of a node/class.

### Design enhancements

Adds a toolbar to the debug section allowing you to easily show/hide Debug and Notice messages.

TODOs
--------------------------------

(for bfdebug operator)
* on highlight, provide full path to each attribute key.
* when out of depth, write jscript that reaches into a static page and renders deeper content, then appends that. With this, you can go any number of levels deep, without ever having to refresh a page.
* redo how levels are done on the page.
* allow/fix rendering of blocks, for instance
