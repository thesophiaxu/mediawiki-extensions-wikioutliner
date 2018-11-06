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
class SpecialOutliner extends SpecialPage {
    public function __construct() {
        parent::__construct( 'Outliner' );
    }

    /**
     * Show the page to the user
     *
     * @param string $sub The subpage string argument (if any).
     *  [[Special:HelloWorld/subpage]].
     */
    public function execute( $sub ) {
        $out = $this->getOutput();

        $out->setPageTitle( 'Outliner' );

        $out->addHelpLink( 'How to become a MediaWiki hacker' );

        $out->addWikiMsg( 'welcome-to-wikioutliner' );
        $out->addWikiMsg( 'use-pagename-to-edit');
        $out->addWikiMsg( 'use-esc-to-toggle-fullscreen');

        $out->addModules ( 'ext.WikiOutliner.concord' );
        $out->addModules ( 'mediawiki.api.edit' );
        $out->addModules ( 'mediawiki.api.parse' );

        // HTML Head Info
        $out->addHeadItem( 'everything', '<link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">');

        $out->addHTML( '
        <br/>
        <br/>
        <input type="text" id="filename" value=""></input>
        <script>function renavToFile() {
                // Go to file
                var filename = $("#filename").val();
                window.location = "/index.php/Special:Outliner?pagename="+filename;
        }</script>
        <script>function toggleFullscreen() {$(\'#containerelement\').toggleClass(\'fullscreen\');}</script>
        <script></script>
        <button class="btn btn-secondary" onClick="renavToFile()">Go</button>
		<div class="divOutlinerContainer" id="containerelement" style="display: flex;">
		    <div id="filelist">
		        <!--placeholder--></div>
			<div id="outliner">
				</div>
			</div>
			
		' );

    }

    /**
     * @return string
     */
    protected function getGroupName() {
        return 'other';
    }
}


