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

/**
 * Hooks for BoilerPlate extension
 */
class Hooks {

	/**
	 * Hook: NameOfHook
	 *
	 * @param string $arg1 First argument
	 * @param bool $arg2 Second argument
	 * @param bool $arg3 Third argument
	 */
	public static function onNameOfHook( $arg1, $arg2, $arg3 ) {
		// Stub
	}

    public static function redirect($url, $statusCode = 303)
    {
        header('Location: ' . $url, true, $statusCode);
        die();
    }

    public static function onArticlePageDataBefore( $article, $fields, &$tables, &$joinConds ) {
	    // TODO: Redirect all 'Outline:' to outliner
        global $wgRequest;
        //echo($wgRequest->getText( 'action' ));
        //die;
        $pos = strpos($article->getTitle(), "Outline:");
        if ($pos === 0 && $wgRequest->getHeader( 'Is-Raw' ) != 333 && $wgRequest->getText( 'action' )!= 'edit') {
            self::redirect("/index.php/Special:Outliner?pagename=".substr($article->getTitle(), 8));
        }
	}

}
