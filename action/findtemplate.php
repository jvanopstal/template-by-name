<?php
/**
 * DokuWiki Plugin templatebyname (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Jan Van Opstal <jvanopstal@mznl.be>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class action_plugin_templatebyname_findtemplate extends DokuWiki_Action_Plugin {

    public function register(Doku_Event_Handler &$controller) {

       $controller->register_hook('COMMON_PAGETPL_LOAD', 'BEFORE', $this, 'handle_common_pagetpl_load');

    }

    public function handle_common_pagetpl_load(Doku_Event &$event, $param) {
		global $conf;

	    if(empty($event->data['tplfile'])){

            $path = dirname(wikiFN($event->data['id']));
            $len = strlen(rtrim($conf['datadir'],'/'));
            $dir = substr($path, strrpos($path, '/')+1);
            $blnFirst = true;
            $blnFirstDir = true;
            while (strLen($path) >= $len){
                if($blnFirst == true && @file_exists($path.'/_'.noNS($event->data['id']).'.txt')){
                    $event->data['tplfile'] = $path.'/_'.noNS($event->data['id'].'.txt');
                    break;
                }
                elseif(@file_exists($path.'/__'.noNS($event->data['id']).'.txt')){
                    $event->data['tplfile'] = $path.'/__'.noNS($event->data['id'].'.txt');
                    break;
                }
                elseif($blnFirst == true && @file_exists($path.'/_template.txt')){
                    $event->data['tplfile'] = $path.'/_template.txt';
                    break;
                }
                elseif($blnFirst == false && $blnFirstDir == true && @file_exists($path.'/~_'.$dir.'.txt') && noNS($event->data['id']) == 'start'){
                    $event->data['tplfile'] = $path.'/~_'.$dir.'.txt';
                    break;
                }
                elseif($blnFirst == false && $blnFirstDir == true && @file_exists($path.'/~'.$dir.'.txt')){
                    $event->data['tplfile'] = $path.'/~'.$dir.'.txt';
                    break;
                }
                elseif($blnFirst == false && @file_exists($path.'/~~_'.$dir.'.txt') && noNS($event->data['id']) == 'start'){
                    $event->data['tplfile'] = $path.'/~~_'.$dir.'.txt';
                    break;
                }
                elseif($blnFirst == false && @file_exists($path.'/~~'.$dir.'.txt')){
                    $event->data['tplfile'] = $path.'/~~'.$dir.'.txt';
                    break;
                }
                elseif(@file_exists($path.'/__template.txt')){
                    $event->data['tplfile'] = $path.'/__template.txt';
                    break;
                }
                $path = substr($path, 0, strrpos($path, '/'));
                if($blnFirst == false){
                    $blnFirstDir = false;
                }
                $blnFirst = false;
            }
        }

    }

}

// vim:ts=4:sw=4:et:
