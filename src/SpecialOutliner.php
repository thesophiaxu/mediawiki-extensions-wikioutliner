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

        $out->addWikiMsg( 'Welcome to WikiOutliner.' );
        $out->addWikiMsg( 'use ?pagename=xxx to edit');

        $out->addModules ( 'ext.WikiOutliner.concord' );
        $out->addModules ( 'mediawiki.api.edit' );

        // HTML Head Info
        $out->addHeadItem( 'everything', '<link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">');

        $out->addHTML( '
        <br/>
        <br/>
        <input type="text" id="filename" value="MasterOutline"></input>
        <!--
		<div class="divMenubar" id="idMenubar">
			<div class="topbar-wrapper" style="z-index: 0; opacity: 1;">
				<div class="navbar navbar-outliner navbar-fixed-top" data-dropdown="dropdown">
					<div class="navbar-inner">
						<div class="container">
							<a class="brand" href="/"><span id="idMenuProductName"></span></a>
							<ul class="nav" id="idMainMenuList">
								<li class="dropdown" id="idOutlinerMenu"> 
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Outliner&nbsp;<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a onclick="opExpand ();"><span class="menuKeystroke">Cmd-,</span>Expand</a></li>
										<li><a onclick="opExpandAllLevels ();">Expand All Subs</a></li>
										<li><a onclick="opExpandEverything ();">Expand Everything</a></li>
										
										<li class="divider"></li>
										<li><a onclick="opCollapse ();"><span class="menuKeystroke">Cmd-.</span>Collapse</a></li>
										<li><a onclick="opCollapseEverything ();">Collapse Everything</a></li>
										
										<li class="divider"></li>
										<li><a onclick="opReorg (up, 1);"><span class="menuKeystroke">Cmd-U</span>Move Up</a></li>
										<li><a onclick="opReorg (down, 1);"><span class="menuKeystroke">Cmd-D</span>Move Down</a></li>
										<li><a onclick="opReorg (left, 1);"><span class="menuKeystroke">Cmd-L</span>Move Left</a></li>
										<li><a onclick="opReorg (right, 1);"><span class="menuKeystroke">Cmd-R</span>Move Right</a></li>
										
										<li class="divider"></li>
										<li><a onclick="opPromote ();"><span class="menuKeystroke">Cmd-[</span>Promote</a></li>
										<li><a onclick="opDemote ();"><span class="menuKeystroke">Cmd-]</span>Demote</a></li>
										
										<li class="divider"></li>
										<li><a onclick="runSelection ();"><span class="menuKeystroke">Cmd-/</span>Run Selection</a></li>
										<li><a onclick="toggleComment ();"><span class="menuKeystroke">Cmd-\</span>Toggle Comment</a></li>
										
										<li class="divider"></li>
										<li><a onclick="toggleRenderMode ();"><span class="menuKeystroke">Cmd-`</span>Toggle Render Mode</a></li>
										</ul>
									</li>
								<li class="dropdown" id="idSourceMenu"> 
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Source&nbsp;<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a onclick="editSource (urlWorknotes);">Worknotes</a></li>
										<li class="divider"></li>
										<li><a onclick="editSource (urlExample0);">Example 0</a></li>
										<li><a onclick="editSource (urlHelloOutliner);">Example 1</a></li>
										<li><a onclick="editSource (urlExample2);">Example 2</a></li>
										<li class="divider"></li>
										<li><a onclick="editSource (urlConcordSource);">Concord</a></li>
										<li><a onclick="editSource (urlConcordCssSource);">Concord CSS</a></li>
										<li><a onclick="editSource (urlConcordUtilsSource);">Concord Utils</a></li>
										<li class="divider"></li>
										<li><a onclick="editSource (urlConcordDocs);">Concord Docs</a></li>
										</ul>
									</li>
								<li class="dropdown" id="idDocsMenu">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown">Links&nbsp;<b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a href="https://docs.fargo.io/outlinerHowto" target="_blank">Outliner Howto</a></li>
										<li><a href="https://github.com/scripting/concord" target="_blank">GitHub Repo</a></li>
										<li><a href="https://groups.google.com/forum/?fromgroups#!forum/smallpicture-concord" target="_blank">Mail List</a></li>
										</ul>
									</li>
								</ul>
							<ul class="nav pull-right">
								<li>
									<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span id="idProductVersion"></span></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>-->
		<div class="divOutlinerContainer">
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


