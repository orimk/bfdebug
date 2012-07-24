{literal}
<style>
	#debug { padding:0 10px; }
	#debug h2 { font-size: 30px; margin: 20px 0 -15px 20px; }
	#debug { font-size: 14px; }
	#debug h3 { font-size: 18px; margin:40px 20px 20px; text-transform:uppercase; }
	#debug td pre { padding:2px; }
	#debug td.debugheader:nth-child(2) { white-space:nowrap; }
	#ezDebugLevelsToggle { margin-bottom: 20px; }
	#ezDebugLevelsToggle .button { font-size:12px; display:inline; padding:5px 10px !important; font-style:normal; background:white; font-weight:bold !important;
		border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px;
		transition: color 0.75s,background-color 0.75s;
		-moz-transition: color 0.75s,background-color 0.75s; /* Firefox 4 */
		-webkit-transition: color 0.75s,background-color 0.75s; /* Safari and Chrome */
		-o-transition: color 0.75s,background-color 0.75s; /* Opera */
	}

	#ezDebugLevelsToggle .notice  { color: green; }
	#ezDebugLevelsToggle .warning { color: orange; }
	#ezDebugLevelsToggle .error   { color: red; }
	#ezDebugLevelsToggle .debug   { color: brown; }
	#ezDebugLevelsToggle .timing  { color: blue; }
	#ezDebugLevelsToggle .strict  { color: purple; }

	#ezDebugLevelsToggle .on.notice  { color:white; background-color: green; }
	#ezDebugLevelsToggle .on.warning { color:white; background-color: orange; }
	#ezDebugLevelsToggle .on.error   { color:white; background-color: red; }
	#ezDebugLevelsToggle .on.debug   { color:white; background-color: brown; }
	#ezDebugLevelsToggle .on.timing  { color:white; background-color: blue; }
	#ezDebugLevelsToggle .on.strict  { color:white; background-color: purple; }

	#ezDebugLevelsToggle .all  { margin-right:10px; background:#fff; color:#555; }
	#ezDebugLevelsToggle .none { margin-left:10px; background:#fff; color:#555; }
</style>

<script>
	$(function() {
		var toggleDebug = function( level, onOff ) {
			var cookieDomain = window.location.host.match( /\.[\w-]+\.[\w]{2,3}$/ );
			var button = $( '#ezDebugLevelsToggle input[value=' + level + ']' );
			if ( !onOff ) {
				if ( button.hasClass('on') ) {
					onOff = 'off'
				} else {
					onOff = 'on'
				}
			}
			if ( onOff === 'off' ) {
				$('#debug tr.'+level.toLowerCase()).hide().next().hide();
				button.addClass('off');
				button.removeClass('on');
				document.cookie = "eZDebugLevelsToggle_" + level + '=off; path=/; domain='+cookieDomain;
			} else {
				$('#debug tr.'+level.toLowerCase()).show().next().show();
				button.addClass('on');
				button.removeClass('off');
				document.cookie = "eZDebugLevelsToggle_" + level + '=on; path=/; domain='+cookieDomain;
			}
		}

		$('#ezDebugLevelsToggle .button').click( function() {
			var attr = $(this).val();
			if ( attr === 'All' ) {
				toggleDebug('Error','on');
				toggleDebug('Warning','on');
				toggleDebug('Notice','on');
				toggleDebug('Timing','on');
				toggleDebug('Debug','on');
				toggleDebug('Strict','on');

			} else if ( attr === 'None' ) {
				toggleDebug('Error','off');
				toggleDebug('Warning','off');
				toggleDebug('Notice','off');
				toggleDebug('Timing','off');
				toggleDebug('Debug','off');
				toggleDebug('Strict','off');

			} else {
				toggleDebug( attr );
			}
		});

		var match;
		var cookies = document.cookie.match( /eZDebugLevelsToggle_\w+=(on|off)/g );
		if ( cookies ) {
			for ( var i = 0; i < cookies.length; i++ ) {
				match = cookies[i].match( /eZDebugLevelsToggle_(\w+)=(on|off)/ );
				toggleDebug( match[1], match[2] )
			}
		}

		/*
		$('#debug tr.error, #debug tr.warning').each( function() {
			if ( typeof console !== 'object' ) {
				return;
			}
			var message = "eZ " + $(this).find('b').text() + "\n\t    " + $(this).next().text();
			if ( $(this).hasClass('error') ) {
				console.error(message);
			} else {
				console.warn(message);
			}
		});
		*/

	});
</script>
<div id="ezDebugLevelsToggle" class="block">
	<input class="button all" type="button" value="All">
	<input class="button error on" type="button" value="Error">
	<input class="button warning on" type="button" value="Warning">
	<input class="button timing on" type="button" value="Timing">
	<input class="button notice  on" type="button" value="Notice">
	<input class="button debug on" type="button" value="Debug">
	<input class="button strict on" type="button" value="Strict">
	<input class="button none" type="button" value="None">
</div>
{/literal}
