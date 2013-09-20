<?php
set_time_limit(0);

/**
 * This template will spider your site to find the string you placed in renderings of templates
 * It will:
 * 	1) Find all the nodes, descending, from the start_node
 * 	2) Get a URL for each node
 * 	3) It will then get the HTML for each node, and if the given string is found, it will attempt to build a "stacktrace" of templates included
 * 
 * See needed params
 * 
 * @copyright Copyright (C) Beaconfire 2012. All rights reserved.
 * @version 1.0.0
 */

#################
#  Setting up env
#################
require 'autoload.php';
if ( file_exists( "config.php" ) ) {
	require "config.php";
}
$cli = eZCLI::instance();
$params = new ezcConsoleInput();

$helpOption = new ezcConsoleOption( 'h', 'help' );
$helpOption->mandatory = false;
$helpOption->shorthelp = "Show help information";
$params->registerOption( $helpOption );

$nodesOpt = new ezcConsoleOption( 'n', 'nodes', ezcConsoleInput::TYPE_STRING );
$nodesOpt->mandatory = true;
$nodesOpt->shorthelp = "StartNodes (comma-delimited)";
$params->registerOption( $nodesOpt );

$urlOpt = new ezcConsoleOption( 'u', 'url', ezcConsoleInput::TYPE_STRING );
$urlOpt->mandatory = true;
$urlOpt->shorthelp = "Url/Hostname for this site (http://www.something.com)";
$params->registerOption( $urlOpt );

$stringOpt = new ezcConsoleOption( 's', 'needle-string', ezcConsoleInput::TYPE_STRING );
$stringOpt->mandatory = true;
$stringOpt->shorthelp = "String to be found (first instance)";
$params->registerOption( $stringOpt );

// Process console parameters
try {
	$params->process();
} catch ( ezcConsoleOptionException $e ) {
	print $e->getMessage(). "\n\n" . $params->getHelpText( 'run script.' ) . "\n\n";
	exit();
}

// Init an eZ Publish script - needed for some API function calls
// and a siteaccess switcher
$ezp_script_env = eZScript::instance( array( 'debug-message' => '', 'use-session' => true, 'use-modules' => true, 'use-extensions' => true ) );
$ezp_script_env->startup();
$ezp_script_env->initialize();

$spider = new bfDebugSiteSpider(array(
	"nodes" => $nodesOpt->value,
	"url" => $urlOpt->value,
	"needle" => $stringOpt->value
));
$spider->setOutputHandler($cli);
$spider->run();

// Avoid fatal error at the end
$ezp_script_env->shutdown();
die();

?>