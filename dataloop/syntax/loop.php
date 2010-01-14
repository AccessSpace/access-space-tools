<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Martyn Eggleton <martyn@access-space.org>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_dataloop_loop extends DokuWiki_Syntax_Plugin {

    /**
     * will hold the data helper plugin
     */
    var $dthlp = null;
		var $loophelper = null;
		

    /**
     * Constructor. Load helper plugin
     */
    function syntax_plugin_dataloop_loop(){
        $this->dthlp =& plugin_load('helper', 'data');
        if(!$this->dthlp) msg('Loading the data helper failed. Make sure the data plugin is installed.',-1);
				
				$this->loophelper =& plugin_load('helper', 'dataloop');
        if(!$this->loophelper) msg('Loading the dataloop helper failed. Make sure the dataloop plugin is installed.',-1);
    }

    /**
     * Return some info
     */
    function getInfo(){
        return $this->loophelper->getInfo('Data Loop');
    }

    /**
     * What kind of syntax are we?
     */
    function getType(){
        return 'container';
    }

		function getAllowedTypes() { return array('baseonly','formatting', 'substition', 'disabled','container','paragraphs'); }   

		
    /**
     * What about paragraphs?
     */
    function getPType(){
        return 'stack';
    }

    /**
     * Where to sort in?
     */
    function getSort(){
        return 1;
    }


    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
			 $this->Lexer->addEntryPattern('<dataloop.*?>(?=.*?</dataloop>)',$mode,'plugin_dataloop_loop');
       //$this->Lexer->addPattern('----+ *dataloop(?: [ a-zA-Z0-9_]*)?-+\n.*?\n----+',$mode,'plugin_dataloop_loop');
    }
		
		function postConnect() { 
			$this->Lexer->addExitPattern('</dataloop>','plugin_dataloop_loop'); 
		}

		
    /**
     * Handle the match - parse the data
     */
    function handle($match, $state, $pos, &$handler){
			switch ($state) {
        case DOKU_LEXER_ENTER :
					$lines = explode("\n",$match);
					array_pop($lines);
					$class = array_shift($lines);
					$class = str_replace('dataloop','',$class);
					$class = trim($class,'- ');
	
					$data = array();
					$data['classes'] = $class;
	
					// parse info
					foreach ( $lines as $line ) {
							// ignore comments
							$line = preg_replace('/(?<![&\\\\])#.*$/','',$line);
							$line = str_replace('\\#','#',$line);
							$line = trim($line);
							if(empty($line)) continue;
							$line = preg_split('/\s*:\s*/',$line,2);
							$line[0] = strtolower($line[0]);
	
							$logic = 'OR';
							// handle line commands (we allow various aliases here)
							switch($line[0]){
									case 'select':
									case 'cols':
													$cols = explode(',',$line[1]);
													foreach($cols as $col){
															$col = trim($col);
															if(!$col) continue;
															list($key,$type) = $this->dthlp->_column($col);
															$data['cols'][$key] = $type;
	
															// fix type for special type
															if($key == '%pageid%') $data['cols'][$key] = 'page';
															if($key == '%title%') $data['cols'][$key] = 'title';
													}
											break;
									case 'title':
									case 'titles':
									case 'head':
									case 'headings':
									case 'header':
									case 'headers':
													$headlevelsplits = explode(';',$line[1]);
													foreach($headlevelsplits as $iLevel => $sCols)
													{
														$cols = explode(',',$sCols);
														foreach($cols as $col){
																$col = trim($col);
																$col = trim($col);
																if(!$col) continue;
																list($key,$type) = $this->dthlp->_column($col);
																$data['headings'][$key] = $iLevel+1;
														}
													}
													//msg("data['headings']= '".var_export($data['headings'], true)."'",-1);
											break;
									case 'limit':
									case 'max':
													$data['limit'] = abs((int) $line[1]);
											break;
									case 'order':
									case 'sort':
													list($sort) = $this->dthlp->_column($line[1]);
													if(substr($sort,0,1) == '^'){
															$data['sort'] = array(substr($sort,1),'DESC');
													}else{
															$data['sort'] = array($sort,'ASC');
													}
											break;
									case 'where':
									case 'filter':
									case 'filterand':
									case 'and':
											$logic = 'AND';
									case 'filteror':
									case 'or':
													if(preg_match('/^(.*?)(=|<|>|<=|>=|<>|!=|=~|~)(.*)$/',$line[1],$matches)){
															list($key) = $this->dthlp->_column(trim($matches[1]));
															$val = trim($matches[3]);
															$val = sqlite_escape_string($val); //pre escape
															$com = $matches[2];
															if($com == '<>'){
																	$com = '!=';
															}elseif($com == '=~' || $com == '~'){
																	$com = 'LIKE';
																	$val = str_replace('*','%',$val);
															}
	
															$data['filter'][] = array('key'     => $key,
																												'value'   => $val,
																												'compare' => $com,
																												'logic'   => $logic
																											 );
													}
											break;
									default:
											msg("data plugin: unknown option '".hsc($line[0])."'",-1);
							}
					}
	
					// if no header titles were given, use column names
					if(!is_array($data['headers'])){
							foreach(array_keys($data['cols']) as $col){
									if($col == '%pageid%'){
											$data['headers'][] = 'pagename'; #FIXME add lang string
									}elseif($col == '%title%'){
											$data['headers'][] = 'page'; #FIXME add lang string
									}else{
											$data['headers'][] = $col;
									}
							}
					}
					$this->aData = $data;
					return array($state, $data, $match);
					break;
				case DOKU_LEXER_UNMATCHED : 
					return array($state, $this->aData, $match);
					break;
				case DOKU_LEXER_EXIT :
					return array($state, $this->aData, '');
					break;
        }
        return array();
    }

    /**
     * Create output or save the data
     */
    function render($format, &$renderer, $alldata) {
        global $ID;
				//dbg($alldata);
        if($format != 'xhtml') return false;
        if(!$this->dthlp->_dbconnect()) return false;
        $renderer->info['cache'] = false;

				list($state,$data, $match) = $alldata;
				
				$sDataLoopSeperator = "£$%DATALOOP£$%";
				
        switch ($state) {
              case DOKU_LEXER_ENTER :
								$renderer->doc .= $sDataLoopSeperator;
								break;
							case DOKU_LEXER_UNMATCHED:
								$renderer->doc .= $renderer->_xmlEntities($match);
								break;
              case DOKU_LEXER_EXIT :
								$aParts = explode($sDataLoopSeperator, $renderer->doc);
								//echo '$aParts='.htmlentities(var_export($aParts, true));
								
								$renderer->doc = $aParts[0];
								$sBlockText = $aParts[1];
								
								//add some preg matching for heading stuff it doesnt do automatically
								preg_match_all('/[ \t]*(={2,})([^\n=]+)\s*=+/', $sBlockText, $aMatches);
								//dbg($aMatches);
											//foreach($aMatches[1] as $iKey => $sCmd)
											//{
				
								//[ \t]*={2,}[^\n]+={2,}[ \t]*(?=\n)
								
								if (count($aMatches[0]))
								{
									$iCurrLevel = 0;
									$iOpenLevels = 0;
									//put this into a loop and try and do '<div class="level'.$level.'">
									// get level and title
									foreach($aMatches[0] as $iKey => $sHeadString)
									{		
										$title = trim($sHeadString);
										$level = 7 - strspn($title,'=');
										if($level < 1) $level = 1;
										$title = trim($title,'=');
										$title = trim($title);
										$sStr = "<h$level>$title</h$level>";
										
										if($level <= $iCurrLevel)
										{
											$sStr = '</div>'.$sStr;
											$iOpenLevels --;
										}
										
										if($level >= $iCurrLevel)
										{
											$sStr = $sStr.'<div class="level'.$level.'">';
											$iOpenLevels ++;
										}
										$iCurrLevel = $level;
										
										$sBlockText = str_replace($sHeadString, $sStr, $sBlockText);
									}
									$sBlockText .= str_repeat("</div>", $iOpenLevels);
									
								}
								
								//echo '$sBlockText='.htmlentities(var_export($sBlockText, true));
								//echo '$format='.htmlentities(var_export($format, true));
								//echo '$renderer='.htmlentities(var_export($renderer, true));
								//dbg($sBlockText);
								//dbg($data);
								$sql = $this->_buildSQL($data); // handles GET params, too
								//dbg($sql);
				
								// register our custom aggregate function
								sqlite_create_aggregate($this->dthlp->db,'group_concat',
																				array($this,'_sqlite_group_concat_step'),
																				array($this,'_sqlite_group_concat_finalize'), 2);
				
				
								// run query
								$types = array_values($data['cols']);
								$res = sqlite_query($this->dthlp->db,$sql);
				
								// build loop
								//$renderer->doc .= '<div class="inline dataplugin_loop '.$data['classes'].'">';
				
								// build column headers
								$cols = array_keys($data['cols']);
								
								// build data rows
								$cnt = 0;
								while ($row = sqlite_fetch_array($res, SQLITE_NUM)) {
									$sCurrentText = $sBlockText;
									
										foreach($row as $num => $col){
											$aMatches = null;
											preg_match_all('/\@\@('.$cols[$num].'[^\@]*)\@\@/i', $sCurrentText, $aMatches);
											$xValue =  $this->dthlp->_formatData($cols[$num],$col,$types[$num],$renderer);
											
											//dbg($aMatches);
											foreach($aMatches[1] as $iKey => $sCmd)
											{
												//dbg($sCmd);
												$aCommand = split('_', $sCmd);
												//dbg($aCommand);
												
												
												switch($aCommand[1])
												{
													case 'url':
														if(!isset($aCommand[2]))
														{
															$xValue2 = $xValue;
														}
														else
														{
															$iColNum = array_search($aCommand[2], $row);
															//no error checking here
															$xValue2 =  $this->dthlp->_formatData($aCommand[2],$row[$iColNum],$types[$iColNum],$renderer);
														}
														
														$sReplace = '<a href="'.$xValue.'">'.$xValue2.'</a>';
														//dbg($sReplace);
													break;
													default:
														$sReplace = $xValue;
													break;
												}
												$sCurrentText = str_ireplace($aMatches[0][$iKey], $sReplace, $sCurrentText);
											}
											
											
										}
										$cnt++;
										$renderer->doc .= $sCurrentText;
										if($data['limit'] && ($cnt == $data['limit'])) break; // keep an eye on the limit
								}
				
								/*
								// if limit was set, add control
								if($data['limit']){
										$renderer->doc .= '<div class="dataplugin_loop limit">';
										$offset = (int) $_GET['dataofs'];
										if($offset){
												$prev = $offset - $data['limit'];
												if($prev < 0) $prev = 0;
				
												$renderer->doc .= '<a href="'.wl($ID,array('datasrt'=>$_GET['datasrt'], 'dataofs'=>$prev, 'dataflt'=>$_GET['dataflt'] )).
																			'" title="'.$this->getLang('prev').'" class="prev">'.$this->getLang('prev').'</a>';
										}
				
										$renderer->doc .= '&nbsp;';
				
										if(sqlite_num_rows($res) > $data['limit']){
												$next = $offset + $data['limit'];
												$renderer->doc .= '<a href="'.wl($ID,array('datasrt'=>$_GET['datasrt'], 'dataofs'=>$next, 'dataflt'=>$_GET['dataflt'] )).
																			'" title="'.$this->getLang('next').'" class="next">'.$this->getLang('next').'</a>';
										}
										$renderer->doc .= '</div>';
								}
				*/
								//$renderer->doc .= '</div>';
								
								break;
							 
            }
            return true;
    }

    /**
     * Builds the SQL query from the given data
     */
    function _buildSQL(&$data){
				//dbg($data);
        $cnt    = 0;
        $tables = array();
        $select = array();
        $from   = '';
        $where  = '';
        $order  = '';


        // take overrides from HTTP GET params into account
        if($_GET['datasrt']){
            if($_GET['datasrt']{0} == '^'){
                $data['sort'] = array(substr($_GET['datasrt'],1),'DESC');
            }else{
                $data['sort'] = array($_GET['datasrt'],'ASC');
            }
        }


        // prepare the columns to show
        foreach (array_keys($data['cols']) as $col){
            if($col == '%pageid%'){
                $select[] = 'pages.page';
            }elseif($col == '%title%'){
                $select[] = "pages.page || '|' || pages.title";
            }else{
                if(!$tables[$col]){
                    $tables[$col] = 'T'.(++$cnt);
                    $from  .= ' LEFT JOIN data AS '.$tables[$col].' ON '.$tables[$col].'.pid = pages.pid';
                    $from  .= ' AND '.$tables[$col].".key = '".sqlite_escape_string($col)."'";
                }
                $select[] = 'group_concat('.$tables[$col].".value,'\n')";
            }
        }

        // prepare sorting
        if($data['sort'][0]){
            $col = $data['sort'][0];

            if($col == '%pageid%'){
                $order = 'ORDER BY pages.page '.$data['sort'][1];
            }elseif($col == '%title%'){
                $order = 'ORDER BY pages.title '.$data['sort'][1];
            }else{
                // sort by hidden column?
                if(!$tables[$col]){
                    $tables[$col] = 'T'.(++$cnt);
                    $from  .= ' LEFT JOIN data AS '.$tables[$col].' ON '.$tables[$col].'.pid = pages.pid';
                    $from  .= ' AND '.$tables[$col].".key = '".sqlite_escape_string($col)."'";
                }

                $order = 'ORDER BY '.$tables[$col].'.value '.$data['sort'][1];
            }
        }else{
            $order = 'ORDER BY 1 ASC';
        }

        // add filters
        if(is_array($data['filter']) && count($data['filter'])){
            $where .= ' AND ( 1=1 ';

            foreach($data['filter'] as $filter){
                $col = $filter['key'];

                if($col == '%pageid%'){
                    $where .= " ".$filter['logic']." pages.page ".$filter['compare']." '".$filter['value']."'";
                }elseif($col == '%title%'){
                    $where .= " ".$filter['logic']." pages.title ".$filter['compare']." '".$filter['value']."'";
                }else{
                    // filter by hidden column?
                    if(!$tables[$col]){
                        $tables[$col] = 'T'.(++$cnt);
                        $from  .= ' LEFT JOIN data AS '.$tables[$col].' ON '.$tables[$col].'.pid = pages.pid';
                        $from  .= ' AND '.$tables[$col].".key = '".sqlite_escape_string($col)."'";
                    }

                    $where .= ' '.$filter['logic'].' '.$tables[$col].'.value '.$filter['compare'].
                              " '".$filter['value']."'"; //value is already escaped
                }
            }

            $where .= ' ) ';
        }

        // add GET filter
        if($_GET['dataflt']){
            list($col,$val) = split(':',$_GET['dataflt'],2);
            if(!$tables[$col]){
                $tables[$col] = 'T'.(++$cnt);
                $from  .= ' LEFT JOIN data AS '.$tables[$col].' ON '.$tables[$col].'.pid = pages.pid';
                $from  .= ' AND '.$tables[$col].".key = '".sqlite_escape_string($col)."'";
            }

            $where .= ' AND '.$tables[$col].".value = '".sqlite_escape_string($val)."'";
        }

        // were any data tables used?
        if(count($tables)){
            $where = 'pages.pid = T1.pid '.$where;
        }else{
            $where = '1 = 1 '.$where;
        }

        // build the query
        $sql = "SELECT ".join(', ',$select)."
                  FROM pages $from
                 WHERE $where
              GROUP BY pages.page
                $order";

        // offset and limit
        if($data['limit']){
            $sql .= ' LIMIT '.($data['limit'] + 1);

            if((int) $_GET['dataofs']){
                $sql .= ' OFFSET '.((int) $_GET['dataofs']);
            }
        }


        return $sql;
    }

    /**
     * Aggregation function for SQLite
     *
     * @link http://devzone.zend.com/article/863-SQLite-Lean-Mean-DB-Machine
     */
    function _sqlite_group_concat_step(&$context, $string, $separator = ',') {
         $context['sep']    = $separator;
         $context['data'][] = $string;
    }

    /**
     * Aggregation function for SQLite
     *
     * @link http://devzone.zend.com/article/863-SQLite-Lean-Mean-DB-Machine
     */
    function _sqlite_group_concat_finalize(&$context) {
         $context['data'] = array_unique($context['data']);
         return join($context['sep'],$context['data']);
    }
}

