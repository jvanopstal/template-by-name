<?php
/**
 * DokuWiki Plugin templatebyname (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jan Van Opstal <jvanopstal@mznl.be>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'action.php';

class action_plugin_templatebyname_findtemplate extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler &$controller) {

       $controller->register_hook('COMMON_PAGETPL_LOAD', 'BEFORE', $this, 'handle_common_pagetpl_load');

    }
	
	public function check_file_name($path, $subPrefix, $filename, $datadir){
		$retValue = '';
		if(@file_exists($path.'/'.$subPrefix.$filename.'.txt') && $this->getConf('allowlocal') == 1 && $this->getConf('allownoneditable') == 1){
			$retValue = $path.'/'.$subPrefix.$filename.'.txt';
		}
		if(@file_exists($path.'/'.$this->getConf('editableprefix').$subPrefix.$filename.'.txt') && $this->getConf('allowlocal') == 1 && $this->getConf('alloweditable') == 1){
			$retValue = $path.'/'.$this->getConf('editableprefix').$subPrefix.$filename.'.txt';
		}
		if(@file_exists($conf['datadir'].'/'.$this->getConf('otherlocation').'/'.$subPrefix.$filename.'.txt') && $this->getConf('allowmirror') == 1 && $this->getConf('allownoneditable') == 1){
			$retValue = $conf['datadir'].'/'.$this->getConf('otherlocation').'/'.$subPrefix.$filename.'.txt';
		}
		if(@file_exists($datadir.'/'.$this->getConf('otherlocation').'/'.$this->getConf('editableprefix').$subPrefix.$filename.'.txt') && $this->getConf('allowmirror') == 1 && $this->getConf('alloweditable') == 1){
			$retValue = $datadir.'/'.$this->getConf('otherlocation').'/'.$this->getConf('editableprefix').$subPrefix.$filename.'.txt';
		}
		
		return $retValue;
	}
	
    public function handle_common_pagetpl_load(Doku_Event &$event, $param) {
		global $conf;
		if(empty($event->data['tpl'])){

			if(empty($event->data['tplfile'])){

				$path = dirname(wikiFN($event->data['id']));
				$len = strlen(rtrim($conf['datadir'],'/'));
				$dir = substr($path, strrpos($path, '/')+1);
				$blnFirst = true;
				$blnFirstDir = true;
				$strTemp = '';
				while (strLen($path) >= $len){
					if($blnFirst == true && ($strTemp = $this->check_file_name($path,'_',noNS($event->data['id']),$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
					elseif(($strTemp = $this->check_file_name($path,'__',noNS($event->data['id']),$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
					elseif($blnFirst == true && ($strTemp = $this->check_file_name($path,'_','template',$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
					elseif($blnFirst == false && $blnFirstDir == true && noNS($event->data['id']) == 'start' && ($strTemp = $this->check_file_name($path,'~_',$dir,$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
					elseif($blnFirst == false && $blnFirstDir == true && ($strTemp = $this->check_file_name($path,'~',$dir,$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
					elseif($blnFirst == false && noNS($event->data['id']) == 'start' && ($strTemp = $this->check_file_name($path,'~~',$dir,$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
					elseif($blnFirst == false && ($strTemp = $this->check_file_name($path,'~~',$dir,$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
					elseif(($strTemp = $this->check_file_name($path,'__','template',$conf['datadir'])) != ''){
						$event->data['tplfile'] = $strTemp;
						break;
					}
				
					/*if($blnFirst == true && @file_exists($path.'/'.$this->getConf('editableprefix').'_'.noNS($event->data['id']).'.txt')){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'_'.noNS($event->data['id'].'.txt');
						break;
					}
					elseif($blnFirst == true && @file_exists($path.'/_'.noNS($event->data['id']).'.txt')){
						$event->data['tplfile'] = $path.'/_'.noNS($event->data['id'].'.txt');
						break;
					}
					elseif(@file_exists($path.'/'.$this->getConf('editableprefix').'__'.noNS($event->data['id']).'.txt')){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'__'.noNS($event->data['id'].'.txt');
						break;
					}
					elseif(@file_exists($path.'/__'.noNS($event->data['id']).'.txt')){
						$event->data['tplfile'] = $path.'/__'.noNS($event->data['id'].'.txt');
						break;
					}
					elseif($blnFirst == true && @file_exists($path.'/'.$this->getConf('editableprefix').'_template.txt')){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'_template.txt';
						break;
					}
					elseif($blnFirst == true && @file_exists($path.'/_template.txt')){
						$event->data['tplfile'] = $path.'/_template.txt';
						break;
					}
					elseif($blnFirst == false && $blnFirstDir == true && @file_exists($path.'/'.$this->getConf('editableprefix').'~_'.$dir.'.txt') && noNS($event->data['id']) == 'start'){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'~_'.$dir.'.txt';
						break;
					}
					elseif($blnFirst == false && $blnFirstDir == true && @file_exists($path.'/~_'.$dir.'.txt') && noNS($event->data['id']) == 'start'){
						$event->data['tplfile'] = $path.'/~_'.$dir.'.txt';
						break;
					}
					elseif($blnFirst == false && $blnFirstDir == true && @file_exists($path.'/'.$this->getConf('editableprefix').'~'.$dir.'.txt')){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'~'.$dir.'.txt';
						break;
					}
					elseif($blnFirst == false && $blnFirstDir == true && @file_exists($path.'/~'.$dir.'.txt')){
						$event->data['tplfile'] = $path.'/~'.$dir.'.txt';
						break;
					}
					elseif($blnFirst == false && @file_exists($path.'/'.$this->getConf('editableprefix').'~~_'.$dir.'.txt') && noNS($event->data['id']) == 'start'){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'~~_'.$dir.'.txt';
						break;
					}
					elseif($blnFirst == false && @file_exists($path.'/~~_'.$dir.'.txt') && noNS($event->data['id']) == 'start'){
						$event->data['tplfile'] = $path.'/~~_'.$dir.'.txt';
						break;
					}
					elseif($blnFirst == false && @file_exists($path.'/'.$this->getConf('editableprefix').'~~'.$dir.'.txt')){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'~~'.$dir.'.txt';
						break;
					}
					elseif($blnFirst == false && @file_exists($path.'/~~'.$dir.'.txt')){
						$event->data['tplfile'] = $path.'/~~'.$dir.'.txt';
						break;
					}
					elseif(@file_exists($path.'/'.$this->getConf('editableprefix').'__template.txt')){
						$event->data['tplfile'] = $path.'/'.$this->getConf('editableprefix').'__template.txt';
						break;
					}
					elseif(@file_exists($path.'/__template.txt')){
						$event->data['tplfile'] = $path.'/__template.txt';
						break;
					}*/
					$path = substr($path, 0, strrpos($path, '/'));
					if($blnFirst == false){
						$blnFirstDir = false;
					}
					$blnFirst = false;
				}
			}
		}
    }
}

// vim:ts=4:sw=4:et:
