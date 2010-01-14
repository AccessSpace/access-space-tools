<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Martyn Eggleton <martyn@access-space.org>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');
require_once(DOKU_INC.'inc/infoutils.php');


/**
 * This is the base class for all syntax classes, providing some general stuff
 */
class helper_plugin_dataloop extends DokuWiki_Plugin {

    /**
     * constructor
     */
    function helper_plugin_dataloop(){
        if(!function_exists('sqlite_open')){
            msg('data plugin: SQLite support missing in this PHP install - plugin will not work',-1);
        }
    }

    /**
     * return some info
     */
    function getInfo($sName = null){
        $aInfo =  array(
            'author' => 'Martyn Eggleton for access-space.org (based on Andreas Gohr)',
            'email'  => 'martyn@access-space.org',
            'date'   => '2009-08-06',
            'name'   => 'Data Loop Plugin',
            'desc'   => 'Adds new output options to work with "Structured Data Plugin"',
            'url'    => 'http://www.dokuwiki.org/plugin:dataloop',
        );
				if ($sName)
				{
					$aInfo['name'] = $sName;
				}
				return $aInfo;
    }
}
