bfdebug extension
=================

Provides enhanced debug functionality.

Design enhancements
-------------------

	Adds a toolbar to the debug section allowing you to easily show/hide Debug and Notice messages.


bfdebug operator
----------------

	Provides nicely formatted layered debug output.
	Dependencies: jQuery loaded somewhere in the page.

	Usage: {$item|bfdebug($depth, $params)}

	$depth is how many levels deep we display (DEFAULT: 1)
	$params 

	 * pos ("tail" - after the last body element, 
	 * 		"top" - before the first body element,
	 * 		"inline" - no special positioning (DEFAULT),
	 * 		"custom:{before|after}" (use posDetailPath to describe)
	 * posJqueryPath (jQuery path where the debug statement will be placed) - example: $("body").children().last()
	 
	TODO: on highlight, provide full path to each attribute key.
	TODO: when out of depth, write jscript that reaches into a static page and renders deeper content, then appends that. With this, you can go any number of levels deep, without ever having to refresh a page.
	TODO: redo how levels are done on the page.


bfdebugvars operator
--------------------

	Returns all local, global, and namespaced variable names available to the current template.

	Example: {bfdebugvars()|bfdebug(2)}


bfdebugoverrides operator
-------------------------

	Returns all conditions and their values that could be used to do an override in the current module/page.

	Example: {bfdebugoverrides()|bfdebug(2)}


bfdebugoperators operator
-------------------------

	Returns all template operators, functions and function attributes available.

	Example:{bfdebugoperators()|bfdebug(2)}

