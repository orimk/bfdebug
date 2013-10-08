<?php

class bfdebug extends bfCustomExtension {
	protected $xmlDoc = null;
	
	function __construct() {
		parent::__construct();
	}

	public function bfdebugoperators($operatorParameters, $namedParameters, $pipedParam=null) {
		$tpl = eZTemplate::factory();
		$operators = array();

		$operators['functions'] = array_keys( $tpl->Functions );
		$operators['functions_attributes'] = array_keys( $tpl->FunctionAttributes );
		$operators['operators'] = array();

		foreach( $tpl->Operators as $operatorName => $operator ) {
			if ( !in_array( $operatorName, $operators['operators'] )) {
				$operators['operators'][] = $operatorName;
			}
			if ( isset( $operator->Operators ) ) {
				foreach ( $operator->Operators as $operator2 ) {
					if ( !in_array( $operator2, $operators['operators'] )) {
						$operators['operators'][] = $operator2;
					}
				}
			}
		}
		return $operators;
	}

	public function bfdebugvars($operatorParameters, $namedParameters, $pipedParam=null) {
		$tpl = eZTemplate::factory();
		$variables = array();
        $variables['locals'] = array_keys( array_pop( $tpl->CurrentLocalVariablesNames ) );
        foreach( $tpl->Variables as $namespace => $vars ) {
        	if ( $namespace == '' ) {
        		$namespace = "globals";
        	} else {
        		$namespace = ':' . $namespace;
        	}
        	$variables[ $namespace ] = array_keys( $vars );
        }
        return $variables;
	}

	public function bfdebugoverrides($operatorParameters, $namedParameters, $pipedParam=null) {
		$res = array();
		$res = eZTemplateDesignResource::instance();
		return $res->Keys;
	}


	/**
	 * Options to apply (as a second param)
	 * pos ("tail" - after the last body element, 
	 * 		"top" - before the first body element,
	 * 		"inline" - no special positioning,
	 * 		"custom:{before|after}" (use posDetailPath to describe)
	 * posJqueryPath (jQuery path where the debug statement will be placed) - example: $("body").children().last()
	 * @author miro.kresonja
	 */
	public function bfdebug($operatorParameters, $namedParameters, $pipedParam=null) {
		$dumpParam = $pipedParam;
		$depth = intval($namedParameters["depth"]);
		if ($depth == 0) {
			$depth = 1;
		}
		if (!array_key_exists("params", $namedParameters)) {
			$namedParameters["params"] = array();
		}
		if (!array_key_exists("pos", $namedParameters["params"])) {
			$namedParameters["params"]["pos"] = "inline";
		}
//		if ($isAbsolutePos) {
//			$absPosStyle = ".bfdump.toptable {position: fixed; top: 20px; left: 20px; z-index: 1000000; background-color: #ffffff; }";
//		}
		ob_start();
		print <<< EOEND
<style>
	.bfdump { border: solid thin black; }
	.bfdump td { padding: 2px; vertical-align: top; }
	.bfdump th { padding: 2px; text-align: center; background-color: #eeeeee; }
</style>
<script language="javascript">
	function toggleBlock(blockId) {
		if ($("#dbdmcell_"+blockId+" .dbdm_values").css("display") == "none") {
			// then show
			$("#dbdmcell_"+blockId+" > .dbdm_explink").html("collapse");
			$("#dbdmcell_"+blockId+" > .dbdm_values").show('fast');
		} else { // hide
			$("#dbdmcell_"+blockId+" > .dbdm_explink").html("expand");
			$("#dbdmcell_"+blockId+" > .dbdm_values").hide('fast');
		}
	}
</script>
EOEND;
		if ($namedParameters["params"]["pos"] != "inline") {
			$pos = $namedParameters["params"]["pos"];
			$elementSelector = "$(\"body\").children().last()";
			if ($pos == "tail") {
				$elementSelector = "$(\"body\").children().last()";
				$beforeAfterMethod = "appendTo"; // jQuery method
			} else if ($pos == "top") {
				$elementSelector = "$(\"body\").children().first()";
				$beforeAfterMethod = "prependTo";
			} else if (strlen($pos, 0, 6) == "custom") {
				list($custom, $appendPrepend) = explode(":",$pos);
				$beforeAfterMethod = $appendPrepend == "before"? "prependTo" : "appendTo";
				if (!array_key_exists("posJqueryPath", $namedParameters["params"])) {
					$elementSelector = $namedParameters["params"]["posJqueryPath"];
				}
			}
			print <<< EOPOSITION
<script language="javascript">
	$(document).ready(function() {
		$(".bfdump.toptable").each(function() {
			\$(this).$beforeAfterMethod( $elementSelector );
		});
	});
</script>
EOPOSITION;
		}
		$this->debugdumpRecurse($dumpParam, array(), 1, $depth);
		$returnString = ob_get_contents();
		ob_end_clean();
		return($returnString);
	}

	public function bfdebugDisableAll() {
		self::disableAllDebug();
	}
	
	// see XML notes
	// used for spot-checking dev without any debugging, or for modules that will error out if comments and stuff appear
	static function disableAllDebug() {
		eZDebug::updateSettings(array(
		    "debug-enabled" => false,
		    "debug-by-ip" => false,
		));
	}

	public function debugdumpRecurse($dumpParam, $dumpParamPathArr, $currentDepth, $maxDepth) {
		list($isSimple, $type) = self::getComplexityType($dumpParam);
		print "<table class=\"bfdump ".($currentDepth == 1?"toptable":"")."\">\n";
		print "<tr><th colspan='3'>Type: $type</th></tr>\n";
//		print "<tr><td>key</td><td>type</td><td>val</td></tr>\n";
		print "<tr><th>key</th><th>val</th></tr>\n";
		if ($isSimple) {
			print "<tr><td colspan='3'>";
			self::dumpSimpleValue($type, $dumpParam);
			print "</td></tr>\n";
		} else {
			if ($type == "array/hash" || substr($type, 0, 6) == "object") {
				$dps = self::dissolveIntoSimpleHash($type, $dumpParam);
				foreach ($dps as $key => $val) {
$childPathArr = $dumpParamPathArr;
array_push($childPathArr, $key);
$valCellId = self::pathToString($childPathArr);
					list($isChildSimple, $childType) = self::getComplexityType($val);
//					print "<tr><td>$key</td><td>";
//					print "$childType</td><td id=\"dbdmcell_$valCellId\">";
					print "<tr><td>$key</td><td id=\"dbdmcell_$valCellId\">";
					if ($isChildSimple) {
						self::dumpSimpleValue($childType, $val);
					} else {
						if ($currentDepth < $maxDepth) {
							print "[<a class=\"dbdm_explink\" href=\"javascript: toggleBlock('$valCellId')\">expand</a>]";
							print "<div class=\"dbdm_values\" style=\"display: none;\">";
								$this->debugdumpRecurse($val, $childPathArr, $currentDepth+1, $maxDepth);
							print "</div>";
						} else {
							print "...";
						}
					}
					print "</td></tr>";
				}
			}
		}
		print "</table>";
	}

	static function pathToString(array $path) {
		// md5 hash as sometimes these can have special characters that break JavaScript
		return( md5( implode( "_", $path ) ) );
	}
	
	static function dumpSimpleValue (&$type, &$value) {
		if ($type == "NULL") {
			print "NULL";
		} else if ($type == "boolean") {
			if ($value) {
				print "(true)";
			} else {
				print "(false)";
			}
		} else {
			print $value;
		}
	}

	static function getComplexityType (&$dumpParam) {
		$isSimple = true;
		if (is_null($dumpParam)) {
			$type = "NULL";
		} else if (is_scalar($dumpParam)) {
			if (is_numeric($dumpParam)) {
				$type = "number";
			} else if (is_bool($dumpParam)) {
				$type = "boolean";
			} else if (is_string($dumpParam)) {
				$type = "string";
			} else {
				$type = "other/unknown";
			}
		} else {
			$isSimple = false;
			if (is_array($dumpParam)) {
				$type = "array/hash";
			} else {
				$type = "object[".get_class($dumpParam)."]";
			}
		}
		return(array($isSimple, $type));
	}
	
	static function dissolveIntoSimpleHash($type, $objectOrArray) {
		if (is_array($objectOrArray)) {
			return($objectOrArray);
		} else if (is_subclass_of($objectOrArray, "eZPersistentObject")) {
			$attributes = $objectOrArray->attributes();
			$retHash = array();
			foreach ($attributes as $attributeName) {
				$retHash[$attributeName] = $objectOrArray->attribute($attributeName);
			}
			return($retHash);
		} else { // regular ole' class
			return(get_object_vars($objectOrArray));
		}
	}
	
}
?>