<?php
/**
 * Linkmap Plugin: displays a number of recent entries from the linkmap subnamespace
 *
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author   Esther Brunner <wikidesign@gmail.com>
 * @author   Robert Rackl <wiki@doogie.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_linkmap_linkgraph extends DokuWiki_Syntax_Plugin {

    function getInfo() {
        return array(
          'author' => 'Martyn Eggleton for access-space.org',
                'email'  => 'martyn@access-space.org',
                'date'   => @file_get_contents(DOKU_PLUGIN . 'linkmap/VERSION'),
                'url'    => 'http://dokuwiki.org/plugin:linkmap',
                'name'   => 'Linkmap Plugin (linkgraph component)',
                'desc'   => 'Displays all links in a namespace that refer to another spefic namespace',
                'url'    => 'http://dokuwiki.org/plugin:linkmap',
                );
    }

    function getType() { return 'substition'; }
    function getPType() { return 'block'; }
    function getSort() { return 309; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{linkgraph>.*?\}\}',$mode,'plugin_linkmap_linkgraph');
    }

    function handle($match, $state, $pos, &$handler) {
        global $ID;
        
        $match = substr($match, 12, -2); // strip {{linkmap> from start and }} from end
        list($match, $sSearch) = explode('&', $match, 2);
        list($ns, $sDataHeadings) = explode('?', $match, 2);

        if (($ns == '*') || ($ns == ':')) $ns = '';
        elseif ($ns == '.') $ns = getNS($ID);
        else $ns = cleanID($ns);
        
        $sSearch = cleanID($sSearch);
        //echo "\n<br><pre>\nsSearch  =" .$sSearch ."</pre>";

        return array($ns, $sDataHeadings, $sSearch);
    }

    function render($mode, &$renderer, $data)
    {
      $renderer->nocache();
      list($ns, $sDataHeadings, $sSearch) = $data;
      $sSearch = cleanID($sSearch);

      if ($oMap =& plugin_load('helper', 'linkmap'))
      {
        $oMap->createMap($ns, $sDataHeadings);
        $oMap->getFullMap();
      }
      else
      {
        return false;
      }
      
      if ($mode == 'xhtml')
      {
        //we currently ignore security on pages and show everything to everyone
        $aRendered = $oMap->render_graph($sSearch);
        /*$sWiki = $aRendered['sContents'];
        $aInstructions = p_get_instructions($sWiki);
        //echo "\n<br><pre>\naInstructions  =" .var_export($aInstructions , TRUE)."</pre>";
        $sXHTML = p_render($mode, $aInstructions, $renderer->info);
        $renderer->doc .= $sXHTML;
        */
        $renderer->doc .= $aRendered['sContents'];
      }
      return true;
    }
  }
// vim:ts=4:sw=4:et:enc=utf-8:
