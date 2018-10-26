<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */

namespace MediaWiki\Extension\WikiOutliner;

use HTMLForm;
use MediaWiki\MediaWikiServices;
use SpecialPage;

/**
 * HelloWorld SpecialPage
 */
class SpecialHelloWorld extends SpecialPage {
	public function __construct() {
		parent::__construct( 'HelloWorld' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 *  [[Special:HelloWorld/subpage]].
	 */
	public function execute( $sub ) {
		$out = $this->getOutput();

		$out->setPageTitle( $this->msg( 'wikioutliner-helloworld' ) );

		$out->addHelpLink( 'How to become a MediaWiki hacker' );

		$out->addWikiMsg( 'wikioutliner-helloworld-intro' );

		$out->addHeadItem('everything', '<script src="/concord/libraries/jquery-1.9.1.min.js"></script>  
		<link href="/concord/libraries/bootstrap.css" rel="stylesheet">
		<script src="/concord/libraries/bootstrap.min.js"></script>
		<link rel="stylesheet" href="/concord/concord.css"/>
		<script src="/concord/concord.js"></script>
		<script src="/concord/concordUtils.js"></script>
		<link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
		<script>
			var appConsts = {
				"productname": "Hello",
				"productnameForDisplay": "Hello Outliner",
				"domain": "hello.blorkmark.com", 
				"version": "0.52"
				}
			var appPrefs = {
				"outlineFont": "Arial", "outlineFontSize": 16, "outlineLineHeight": 24,
				"authorName": "", "authorEmail": ""
				};
			var whenLastKeystroke = new Date (), whenLastAutoSave = new Date ();  
			var flReadOnly = false, flRenderMode = false;
			var cmdKeyPrefix = "Ctrl+"; 
			
			var urlConcordSource = "https://raw.github.com/scripting/concord/master/opml/concord.opml";
			var urlConcordCssSource = "https://raw.github.com/scripting/concord/master/opml/concordCss.opml";
			var urlConcordDocs = "https://raw.github.com/scripting/concord/master/opml/concordDocs.opml";
			var urlConcordUtilsSource = "https://raw.github.com/scripting/concord/master/opml/concordUtils.opml";
			var urlHelloOutliner = "https://raw.github.com/scripting/concord/master/example1/source.opml";
			var urlExample0 = "https://raw.github.com/scripting/concord/master/example0/source.opml";
			var urlExample2 = "https://static.smallpicture.com/tacoma/wo/admin/2013/09/18/archive056.opml";
			var urlWorknotes = "https://static.smallpicture.com/tacoma/wo/admin/2013/09/18/archive057.opml";
			
			function initLocalStorage () {
				if (localStorage.savedOpmltext == undefined) {
					localStorage.savedOpmltext = initialOpmltext;
					editSource (urlConcordDocs); //9/14/13 by DW
					}
				if (localStorage.ctOpmlSaves == undefined) {
					localStorage.ctOpmlSaves = 0;
					}
				if (localStorage.whenLastSave == undefined) {
					localStorage.whenLastSave = new Date ().toString ();
					}
				if (localStorage.flTextMode == undefined) {
					localStorage.flTextMode = "true";
					}
				}
			function setInclude () { //used to test includes
				opSetOneAtt ("type", "include");
				opSetOneAtt ("url", "https://smallpicture.com/states.opml");
				}
			function editSource (url) {
				opXmlToOutline (initialOpmltext); //empty the outline display
				readText (url, function (opmltext, op) {
					opXmlToOutline (opmltext);
					saveOutlineNow ();
					}, undefined, true);
				}
			function nukeDom () {
				var summit, htmltext = "", indentlevel = 0;
				$(defaultUtilsOutliner).concord ().op.visitToSummit (function (headline) {
					summit = headline;
					return (true);
					});
				var visitSub = function (sub) {
					if (sub.attributes.getOne ("isComment") != "true") { 
						htmltext += filledString ("\t", indentlevel) + sub.getLineText () + "\r\n"
						if (sub.countSubs () > 0) {
							indentlevel++;
							sub.visitLevel (visitSub); 
							indentlevel--;
							}
						}
					};
				summit.visitLevel (visitSub);
				
				var t = new Object ();
				t.text = summit.getLineText ();
				htmltext = multipleReplaceAll (htmltext, t, false, "<" + "%", "%" + ">");
				
				document.open ();
				document.write (htmltext);
				document.close ();
				}
			function opExpandCallback (parent) {
				var type = parent.attributes.getOne ("type"), url = parent.attributes.getOne ("url"), xmlUrl = parent.attributes.getOne ("xmlUrl");
				//link nodes
					if ((type == "link") && (url != undefined)) {
						window.open (url);
						return;
						}
				//rss nodes
					if ((type == "rss") && (xmlUrl != undefined)) {
						window.open (xmlUrl);
						return;
						}
				//include nodes
					if ((type == "include") && (url != undefined)) {
						parent.deleteSubs ();
						parent.clearChanged ();
						readText (url, function (opmltext, op) {
							op.insertXml (opmltext, right); 
							op.clearChanged ();
							}, parent, true);
						}
				}
			function opInsertCallback (headline) { 
				headline.attributes.setOne ("created", new Date ().toUTCString ());
				}
			function opCollapseCallback (parent) {
				if (parent.attributes.getOne ("type") == "include") {
					parent.deleteSubs ();
					parent.clearChanged ();
					}
				}
			function opHoverCallback (headline) { 
				var atts = headline.attributes.getAll (), s = "";
				//set cursor to pointer if there\'s a url attribute -- 3/24/13  by DW
					if ((atts.url != undefined) || (atts.xmlUrl != undefined)) {
						document.body.style.cursor = "pointer";
						}
					else {
						document.body.style.cursor = "default";
						}
				}
			function opCursorMovedCallback (headline) {
				}
			function opKeystrokeCallback (event) { 
				whenLastKeystroke = new Date (); 
				}
			function runSelection () {
				var value = eval (opGetLineText ());
				opDeleteSubs ();
				opInsert (value, "right");
				opGo ("left", 1);
				}
			function setOutlinerPrefs (id, flRenderMode, flReadonly) { 
				$(id).concord ({
					"prefs": {
						"outlineFont": appPrefs.outlineFont, 
						"outlineFontSize": appPrefs.outlineFontSize, 
						"outlineLineHeight": appPrefs.outlineLineHeight,
						"renderMode": flRenderMode,
						"readonly": flReadonly,
						"typeIcons": appTypeIcons
						},
					"callbacks": {
						"opInsert": opInsertCallback,
						"opCursorMoved": opCursorMovedCallback,
						"opExpand": opExpandCallback,
						"opHover": opHoverCallback, 
						"opKeystroke": opKeystrokeCallback
						}
					});
				}
			function saveOutlineNow () {
				localStorage.savedOpmltext = opOutlineToXml (appPrefs.authorName, appPrefs.authorEmail);
				localStorage.ctOpmlSaves++;
				opClearChanged ();
				console.log ("saveOutlineNow: " + localStorage.savedOpmltext.length + " chars.");
				}
			function backgroundProcess () {
				if (opHasChanged ()) {
					if (secondsSince (whenLastKeystroke) >= 1) { 
						saveOutlineNow ();
						}
					}
				}
			function startup () {
				initLocalStorage ();
				$("#idMenuProductName").text (appConsts.productname);
				$("#idProductVersion").text ("v" + appConsts.version);
				//init menu keystrokes
					if (navigator.platform.toLowerCase ().substr (0, 3) == "mac") {
						cmdKeyPrefix = "&#8984;";
						}
					$("#idMenubar .dropdown-menu li").each (function () {
						var li = $(this);
						var liContent = li.html ();
						liContent = liContent.replace ("Cmd-", cmdKeyPrefix);
						li.html (liContent);
						});
				setOutlinerPrefs ("#outliner", true, false); //9/20/13 by DW -- change initial value for renderMode from false to true
				opSetFont (appPrefs.outlineFont, appPrefs.outlineFontSize, appPrefs.outlineLineHeight); 
				opXmlToOutline (localStorage.savedOpmltext);
				self.setInterval (function () {backgroundProcess ()}, 1000); //call every second
				}
			</script>
		<style>
			body {
				background-color: whitesmoke;
				}
			.divOutlinerContainer { 
				width: 75%;
				margin-top: 75px;
				margin-left: auto;
				margin-right: auto;
				border:1px solid gainsboro;
				min-height: 550px;
				max-height: 800px;
				overflow: auto;
				padding: 6px;
				background-color: white;
				}
			.divSubtext {
				width: 65%;
				margin-top: 3px;
				margin-left: auto;
				margin-right: auto;
				}
			/* menubar */
				.divMenubar .container { 
					margin-left: auto;
					margin-right: auto;
					width: 940px;
					}
				.divMenubar .navbar .nav > li > a { 
					font-size: 15px;
					padding-top: 12px;
					padding-left: 8px; padding-right: 8px; //6/3/13 by DW
					outline: none !important;
					}
				.dropdown-menu > li > a {
					cursor: pointer;
					}
				.navbar-inner { 
					-moz-border-radius: 0;
					-moz-border-radius: none; 
					-moz-box-shadow: none; 
					background-image: none; 
					border-radius: 0;  
					}
				.divMenubar .brand { 
					margin-top: 0;
					}
				.divMenubar .nav li {
					font-family: Arial;
					font-size: 14px;
					font-weight: bold;
					}
				.menuKeystroke {
					float: right;
					margin-left: 25px;
					}
				.menuKeystroke:before {
					content: "";
					}
				 #idMenuProductName {
					font-family: "Arial";
					font-size: 24px;
					font-weight: bold;
					font-style: italic;
					}
			</style>
');

        $out->addHTML( '
        <div class="divOutlinerContainer">
			<div id="outliner">
				</div>
			</div>
		<script>
			$(document).ready (function () {
				startup ();
				});
			</script>

        ');


	}

	/**
	 * @param string[] $formData The submitted form data.
	 * @return bool|string
	 */
	static function trySubmit( $formData ) {
		if ( $formData['myfield1'] == 'Fleep' ) {
			return true;
		}

		return 'HAHA FAIL';
	}

	/**
	 * @return string
	 */
	protected function getGroupName() {
		return 'other';
	}
}
