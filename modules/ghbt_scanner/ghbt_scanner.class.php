<?php
/**
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 13:03:10 [Mar 13, 2016])
*/
//
//
//ini_set ('display_errors', 'off');

class ghbt_scanner extends module {
/**
* yandex_tts
*
* Module class constructor
*
* @access private
*/
function ghbt_scanner() {
  $this->name="ghbt_scanner";
  $this->title="GoogleHome BT Scanner";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }



}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;

//echo 'view_mode:'.$this->view_mode;



 if ($this->view_mode=='indata_del') {
//   $this->delete($this->id);}	
}
 if ($this->view_mode=='addtopinghost') {
//   $this->add_to_pinghost($this->id);
}	



if ($this->view_mode=='ping') {
//  $this->pingall();
}

if ($this->view_mode=='discover') {
//  $this->discover();

}

if ($this->view_mode=='nmap') {
//  $this->nmap();

}


if ($this->view_mode=='clearall') {
//  $this->clearall();

}



}

 function discover() {
}






 function clearall() {
//$cmd_rec = SQLSelect("delete  FROM wol_devices  ");
}




 function delete($id) {
  $rec=SQLSelectOne("SELECT * FROM wol_devices WHERE ID='$id'");
  // some action for related tables
//  SQLExec("DELETE FROM wol_devices WHERE ID='".$rec['ID']."'");
 }


 function searchdevices(&$out) {

//  $this->pingall();


  require(DIR_MODULES.$this->name.'/search.inc.php');
 }



/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 if ($this->view_mode=='mac') {
   global $mac;
//$res=$this->wake($mac);
$res=$this->WakeOnLan("255.255.255.255", $mac);
 $this->WakeOnLan('192.168.255.255',$mac);
 $this->WakeOnLan('192.168.0.255',$mac);
 $this->WakeOnLan('192.168.1.255',$mac);
$out['RESULT']=print_r($res);
}

$this->searchdevices($out);

}












/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();


 }
 
 function dbInstall($data) {

 $data = <<<EOD

 ghbt_ghdevices: ID int(10) unsigned NOT NULL auto_increment
 ghbt_ghdevices: TITLE varchar(100) NOT NULL DEFAULT ''
 ghbt_ghdevices: MAC varchar(100) NOT NULL DEFAULT ''
 ghbt_ghdevices: IPADDR varchar(100) NOT NULL DEFAULT ''
 ghbt_ghdevices: NAME varchar(100) NOT NULL DEFAULT ''
 ghbt_ghdevices: LASTPING varchar(100) NOT NULL DEFAULT ''
 ghbt_ghdevices: ONLINE varchar(100) NOT NULL DEFAULT ''
 ghbt_ghdevices: VENDOR varchar(100) NOT NULL DEFAULT ''



 ghbt_btdevices: ID int(10) unsigned NOT NULL auto_increment
 ghbt_btdevices: TITLE varchar(100) NOT NULL DEFAULT ''
 ghbt_btdevices: MAC varchar(100) NOT NULL DEFAULT ''
 ghbt_btdevices: IPADDR varchar(100) NOT NULL DEFAULT ''
 ghbt_btdevices: NAME varchar(100) NOT NULL DEFAULT ''
 ghbt_btdevices: LASTPING varchar(100) NOT NULL DEFAULT ''
 ghbt_btdevices: ONLINE varchar(100) NOT NULL DEFAULT ''
 ghbt_btdevices: VENDOR varchar(100) NOT NULL DEFAULT ''

EOD;


  parent::dbInstall($data);
 }
 
 function uninstall() {
SQLExec('DROP TABLE IF EXISTS ghbt_ghdevices');
SQLExec('DROP TABLE IF EXISTS ghbt_btdevices');
  parent::uninstall();
 }




// Sends NBSTAT packet and decodes response
/* Коды ошибок:

  -1 Не удалось получить ответ
   2 Количество секций в ответе не совпадает с ожидаемым
   3 Неверный формат пакета ответа
*/
function nbt_getinfo($ip) {
// Пакет NetBIOS с запросом NBSTAT
    $data = chr(0x81) . chr(0x0c) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x01) .
        chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) . chr(0x00) .
        chr(0x20) . chr(0x43) . chr(0x4b) . chr(0x41) . chr(0x41) . chr(0x41) .
        chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) .
        chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) .
        chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) .
        chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) . chr(0x41) .
        chr(0x41) . chr(0x41) . chr(0x41) . chr(0x00) . chr(0x00) . chr(0x21) .
        chr(0x00) . chr(0x01);

    $fp = fsockopen("udp://$ip:137");
    fputs($fp, $data);
    stream_set_timeout($fp, 1);
    $response['transaction_id'] = fread($fp, 2);
    if (empty($response['transaction_id']))
        return -1;
    else
    $response['transaction_id'] = $this->word2num($response['transaction_id']);
    $response['flags'] = $this->word2num(fread($fp, 2));
    $response['questions'] = $this->word2num(fread($fp, 2));
    $response['answers'] = $this->word2num(fread($fp, 2));
    $response['authority'] = $this->word2num(fread($fp, 2));
    $response['additional'] = $this->word2num(fread($fp, 2));
    if (!($response['questions'] == 0 && $response['answers'] == 1 &&
        $response['authority'] == 0 && $response['additional'] == 0))
        return 2;

//  Answer section
    $buf = fread($fp, 1);
    if ($buf != chr(0x20))
        return 3;

//  Answer Name
    $response['answer_name'] = '';
    while ($buf != chr(0)) {
        $buf = fread($fp, 1);
        $response['answer_name'] .= $buf;
    }

//  Type (should be NBSTAT)
    $response['answer_type'] = $this->word2num(fread($fp, 2));
    if ($response['answer_type'] != 33)
        return 3;

//  Class (should be 1, but we won't check that)
    $response['answer_class'] = $this->word2num(fread($fp, 2));

//  TTL
    $response['answer_ttl'] = $this->dword2num(fread($fp, 4));

//  Data length
    $response['answer_length'] = $this->word2num(fread($fp, 2));

//  Number of names
    $response['answer_number'] = ord(fread($fp, 1));

//  Getting names
    for ($i = 1; $i <= $response['answer_number']; $i++) {
        $response['answer_value'][$i] = fread($fp, 15);
        $response['answer_type_'][$i] = ord(fread($fp, 1));
        $response['answer_flags'][$i] = $this->word2num(fread($fp, 2));
    }

//  Unit ID (MAC)
    $response['answer_mac'] = fread($fp, 6);

//  There more data follows, but we don't need it, so we can drop it.
    fclose($fp);
    return $response;
}

// Issues nbt_getinfo() and returns target machine NetBIOS from response
function nbt_getName($ip) {
    $response = $this->nbt_getinfo($ip);
    $i = 1;
    foreach ($response['answer_type_'] as $answer_type_) {
        if ($answer_type_ == 0)
            return $response['answer_value'][$i];
        $i++;
    }
}

function word2num($word) {
    return ord($word[1]) + ord($word[0]) * 16;
}

function dword2num($dword) {
    return ord($dword[3]) + ord($dword[2]) * 16 + ord($dword[2]) * 16 * 16 + ord($dword[0]) * 16 * 16 * 16;
}


 function add_to_pinghost($id) {
  if (!$id) {
      $id = ($_GET["id"]);
  }
  $ph=SQLSelectOne("SELECT * FROM wol_devices WHERE ID='".$id."'");
// print_r($ph);
  $pinghosts=array(); // опции добавления
  $pinghosts['TITLE'] = $ph['TITLE'];
  $pinghosts['TYPE'] = '0';
  $pinghosts['OFFLINE_INTERVAL'] = '600';
  $pinghosts['ONLINE_INTERVAL'] = '600';
  $pinghosts['HOSTNAME'] = $ph['IPADDR'];
  $pinghosts['CODE_OFFLINE'] = 'say("Устройство ".$host[\'TITLE\']." пропало из сети, возможно его отключили" ,2);';
  $pinghosts['CODE_ONLINE'] = 'say("Устройство ".$host[\'TITLE\']." появилось в сети." ,2);';
  $pinghosts['LINKED_OBJECT'] = '';
  $pinghosts['LINKED_PROPERTY'] = "alive";
  $pinghosts['CHECK_NEXT'] = date("Y-m-d H:i:s");  
  $chek=SQLSelectOne("SELECT * FROM pinghosts WHERE HOSTNAME='".$ph['IPADDR']."'");
  if ($chek['ID']) {
          $chek['ID'] = SQLUpdate('pinghosts', $pinghosts);
      } else {	
          SQLInsert('pinghosts', $pinghosts);
     }
 }


 function getvendor($mac) {

$url="https://macvendors.co/api/$mac/json";
$file = file_get_contents($url);
$data=json_decode($file,true);
//echo $file;
//echo "<br>";
$vendor=$data['result']['company'];
return $vendor;


}
 
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEzLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/



