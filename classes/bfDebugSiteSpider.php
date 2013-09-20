<?php

class bfDebugSiteSpider {
	private $notifyEveryNNodes = 100;
	private $nodes = array();
	private $url = "";
	private $needle = ""; // needle in the haystack

	private $aURLs = array(); // hash, key is nodeId, value is the URL... main stack to go through
	private $aFoundURLHash = array(); // hash with nodeId as key, values are URL, offsetFound, stackTrace

	// see spiderFindTemplate.php for params
	function __construct($aOptions) {
		$this->nodes = explode(",", $aOptions["nodes"]);
		$this->url = $aOptions["url"];
		$this->needle = $aOptions["needle"];
	}

	function run() {
		$this->assembleSearchTree();
		$this->log("Final queue size: ".sizeof($this->aURLs));
		$this->processSearchTree();

		if (sizeof($this->aFoundURLHash) > 0) {
			$this->displayResults();
		} else {
			$this->log("No mentions found.");
		}
	}

	function displayResults() {
		$this->log(print_r($this->aFoundURLHash, true));
	}

	function processSearchTree() {
		$i=0;
		foreach ($this->aURLs as $nodeId => $url) {
			$fullURL = $this->url."/".$url;
			$fullPage = file_get_contents($fullURL);
			$strpos = strpos($fullPage, $this->needle);
			if ($strpos !== false) {
				// found it, extract stacktrace
				$stackTrace = $this->extractStackTrace($fullPage, $strpos);
				array_push($this->aFoundURLHash, array(
					"nodeId" => $nodeId,
					"url" => $url,
					"pos" => $strpos,
					"stackTrace" => $stackTrace
				));
			}
			$i++;
			if ($i % $this->notifyEveryNNodes == 0) {
				$this->log("Done with $i items, ".sizeof($this->aFoundURLHash)." found.");
			}
		}	
	}

	function extractStackTrace(&$fullPage, $strpos) {
		$stackArray = array();
		$partialPage = substr($fullPage, 0, $strpos);
		// extract stop and start values 
		$regex = "/<!-- (START|STOP): including template: (.+?) \(.+?\) -->/s";
		preg_match_all($regex, $partialPage, $matches);
		for ($i=sizeof($matches[0])-1; $i>=0; $i--) {
			$startStop = $matches[1][$i];
			$template = $matches[2][$i];
			if ($startStop == "STOP") {
				// always toss these in
				array_push($stackArray, $startStop.":".$template);
			} else { // it's a start, check if there's an equivalent stop at the top of the stack
				$isStackEmpty = sizeof($stackArray) == 0;
				$isLastYourOwnStop = false;
				if (!$isStackEmpty) {
					$lastElementInStack = $stackArray[sizeof($stackArray)-1];
					$isLastYourOwnStop = $lastElementInStack == "STOP:".$template;
				}
				if (!$isStackEmpty && $isLastYourOwnStop) {
					array_pop($stackArray);
				} else {
					array_push($stackArray, $startStop.":".$template);
				}
			}
		}
		return(array_reverse($stackArray));
	}

	// spider down the tree, get all unique nodes down the chain
	function assembleSearchTree() {
		foreach ($this->nodes as $startNode) {
			$oStartNode = eZFunctionHandler::execute( 'content', 'node', array( 'node_id' => $startNode ));
			$this->__recursiveNodeGet($oStartNode);
		}
	}

	function __recursiveNodeGet($oNode, $depth = 0) {
		$nodeURL = $oNode->attribute("url_alias");
		$nodeID = $oNode->attribute("node_id");
		$this->aURLs[$nodeID] = $nodeURL;

		// then pull all children, call self recursively
		$aChildNodes = eZFunctionHandler::execute( 'content', 'list', array( 
			'parent_node_id' => $nodeID
		));
		foreach($aChildNodes as $oChildNode) {
			$this->__recursiveNodeGet($oChildNode, $depth+1);
		}
		if ($depth < 2) { // just show major sections, basically
			$this->log("Current queue size: ".sizeof($this->aURLs));
		}
	}


	function setOutputHandler($cli) {
		$this->CLI = $cli;
	}
    
	function log($message) {
		$this->CLI->output($message);
	}
}

?>