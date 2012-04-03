<?php
class emailKarma {

	protected $kMenu;
	protected $eConf;

	protected $l;
	public $retJson = array();
	protected $aMenus = array();
	protected $aTMPMenus = array('Fast Mail','Saved Emails','Sent Emails'); //,'Karma Contacts'
	protected $froms;

	protected $confPath;

	protected $conffile;

	protected $aSenders = array();
	protected $aContacts = array();

	protected $email_data = false;

	function __construct($kMenu) {
		$this->kMenu = $kMenu;
		$this->l = $this->kMenu->l;
		$this->load();
	}

	protected function load(){
		$this->kMenu->aPrincJs = array_merge($this->kMenu->aPrincJs,$this->getJs());
		$this->kMenu->aPrincCss = array_merge($this->kMenu->aPrincCss,$this->getCss());
		$this->confPath = dirname(__FILE__)."/../../../configuracion/email_karma/";
		$conffile = $this->kMenu->getMenuSection('emailSystem','conf');

		if (!file_exists($this->confPath.$conffile)) {
			$this->error("El fichero de configuración del Sistema de Emails Karma no existe:<br /><em>./configuracion/email_karma/".$conffile."</em>");
		}else{
			$this->eConf = parse_ini_file($this->confPath.$conffile,true);
			$c = new con("describe karma_emails_log");
			if ($c->error()){
				$sql = "CREATE TABLE `karma_emails_log` (
`id_email` mediumint(8) unsigned NOT NULL auto_increment,
`from` varchar(255) default NULL,
`to` varchar(500) default NULL,
`subject` varchar(255) default NULL,
`iden` varchar(255) default NULL,
`text` text,
`id_usuario` mediumint(8) unsigned NOT NULL,
`fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
`st` enum('saved','sent') default 'saved',
PRIMARY KEY  (`id_email`),
KEY `id_usuario` (`id_usuario`),
CONSTRAINT `karma_emails_log_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `karma_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
				iError::warn($c->geterror());
				iError::warn("<textarea>".$sql."</textarea>");
			}
			$this->loadSenders();
		}
	}

	public function getJs() {
		return array('../modules/emailKarma/scripts/email.js','../modules/tablon/scripts/jquery.autocomplete.js','../modules/tablon/scripts/tiny_mce/tiny_mce.js','../modules/emailKarma/scripts/tiny_conf.js');
	}

	public function getCss() {
		return array('../modules/emailKarma/css/email.css');
	}

	protected function loadSenders(){
		$read=true;
		$i = 0;
		do{
			$lev = 'sender_'.$i;
			if (isset($this->eConf[$lev]) ){
				$o = $this->eConf[$lev];
				if (isset($o['parseFile'])){
					$mainField = $o['mainField'];
					$nameField = $o['nameField'];
					$emailField = $o['emailField'];
					if (!file_exists($this->confPath.$o['parseFile'])) $this->error("el fichero de configuración de Emails Karma no existe: ".$o['parseFile']);
					$tmp = parse_ini_file($this->confPath.$o['parseFile'],true);
					$main = $tmp[$mainField];
					$c = new con("select ".$nameField ." as name , ".$emailField." as email from ".$main['tab']." where ".$main['id']." = '".$_SESSION["__ID"]."' ");
					if ($c->getNumRows()>0){
						$r = $c->getResult();
						$this->aSenders[$i]['name'] = $r['name'];
						$this->aSenders[$i]['email'] = $r['email'];
					}
				}else{
					$this->aSenders[$i]['name'] = $o['name'];
					$this->aSenders[$i]['email'] = $o['email'];
				}
			}else $read=false;
			$i++;
		}while($read==true);
	}

	protected function ex(){
		die(json_encode($this->retJson));
	}

	protected function error($str="error"){
		$this->retJson['error'] = $str;
		if ( isset($_GET['ajax']) ){
			$this->ex();
		}else{
			iError::warn($str);
		}

	}

	public function ajax(){
		if ( !isset($_GET['ajax']) ) { $this->error();}
		switch($_GET['load']) {
			case "init": $this->init(); break;
			case "mod": $this->loadContents($_GET['mod']); break;
			case "savemail": $this->get_email_data($_GET['action']); break;
			case "contacts_autocomplete": $this->autocomplete(); break;
			default: $this->error(); break;
		}
		$this->ex();
	}

	protected function loadMenu($q){
		$this->aMenus[i::clean($q)] = $q;
	}

	protected function drawMenu(){
		$html = '<ul>';
		$first = true;
		foreach($this->aMenus as $iden => $lit){
			$html.= '<li class="tabbuttom '.(($first)? 'selected':'' ).'" mod="'.$iden.'"><a>'.$lit.'</a></li>';
			if ($first)	$first=false;
		}
		$html.= '</ul>';
		$this->retJson['menu'] = $html;
	}

	protected function loadContents($q){
		foreach ($this->aTMPMenus as $m) $this->loadMenu($m);
		if (!in_array(i::clean($q),array_keys($this->aMenus))) $this->error();
		$this->htmlfroms = $this->drawFroms();
		if (method_exists($this,'draw_'.i::clean($q))) {
			$method = 'draw_'.i::clean($q);
			$pagetext=call_user_method($method,$this);
			$this->retJson['html'] = '<div class="tabcontainer">'.$pagetext.'</div>';
		}
	}

	protected function init(){
		foreach ($this->aTMPMenus as $m) $this->loadMenu($m);
		$this->drawMenu();
		$this->loadContents($this->aTMPMenus[0]);
	}

	protected function loadContacts($q=false){
		$ret = array();
		if (isset($this->eConf["contacts"]) ){
			$i = 0;
			$o = $this->eConf["contacts"];
			if (isset($o['parseFile'])){
				$mainField = $o['mainField'];
				$nameField = $o['nameField'];
				$emailField = $o['emailField'];
				if (!file_exists($this->confPath.$o['parseFile'])) $this->error("el fichero de configuración de Emails Karma no existe: ".$o['parseFile']);
				$tmp = parse_ini_file($this->confPath.$o['parseFile'],true);
				$main = $tmp[$mainField];
				$sql = "select ".$nameField ." as name , ".$emailField." as email ";
				$sql.= "from ".$main['tab']." ";// where ".$main['id']." = '".$_SESSION["__ID"]."' ";
				if (isset($q)&&trim($q)!=""){
					$sql.= " where ".$nameField ." like '".$q."%' or ".$emailField." like '".$q."%' " ;
				}
				$c = new con($sql);
				if ($c->getNumRows()>0){
					while($r = $c->getResult()){
						$this->aContacts[$i]['name'] = $r['name'];
						$this->aContacts[$i]['email'] = $r['email'];
						$ret[$i] =  '"'.$r['name'].'" <'.$r['email'].'>';
						$i++;
					}
				}
			}
		}
		return $ret;
	}

	protected function get_email_data($action){
		//$e = "\"lande\" <landerargasdfh> ";
		//echo $e;
		//$h1tags = preg_match_all("/(<h1.*>)(\w.*)(<\/h1>)/isxmU",$file,$patterns);

		//var_dump(preg_match("/<(\w.*)>/",$e,$o), $o );
			//			exit();
		$this->email_data = array();
		$errors = array();
		foreach($_POST as $field => $value){

			$field = mysql_real_escape_string($field);
			$value = mysql_real_escape_string($value);
			switch ($field){
				case "from":	break;
				case "to":
					$tmp = explode(",",$value);
					$this->email_data[$field.'_str'] = $value;

					$value = array();

					foreach ($tmp as $x){
						if (trim($x)=="") continue;

						$value[] = trim($x);
						$tmp = trim($x);
						if (preg_match("/<(\w.*)>/",trim($x),$o))	$tmp = $o[1];

						if (!i::checkmail($tmp)) $errors['to'] = "Incorrect email : ".htmlentities(trim($x));

					}

					$toVals = $value;

				break;
				case "subject":
				case "email_body":
					if (trim($value)=="") $errors[$field] = $field." is empty.";
				break;
				default: continue; break;
			}

			$this->email_data[$field] = $value;
		}
		if (sizeof($errors)>0){
			$this->retJson['formaterrors'] = $errors;
		}else{
			$tmpdata = $this->email_data;

			switch($action){
				case "send":
					$tmpdata['st'] = 'sent';
					if (!$tmpdata['to']){  $errors['to'] = "Email field is empty."; $this->retJson['formaterrors'] = $errors; break;}
					else $this->retJson['send'] = "true";
				case "save":
					if (!$this->retJson['send']) $this->retJson['save'] = "true";
					if (!$tmpdata['st']) $tmpdata['st'] = 'saved';
					$tmpdata['to'] = $tmpdata['to_str'];
					unset($tmpdata['to_str']);
					$tmpdata['text'] = $tmpdata['email_body'];
					unset($tmpdata['to_str'],$tmpdata['email_body']);
					$tmpdata['iden'] = $this->unique($tmpdata['subject'],'id_email','karma_emails_log','iden');
					$tmpdata['id_usuario'] = $_SESSION['__ID'];
					$vals = array();
					foreach ($tmpdata as $foo=>$v) $vals[] = "'".($v)."'";
					if ($action=="send"){
						require_once((dirname(__FILE__))."/../../libs/htmlMimeMail.php");

						foreach ($toVals as $to){
							$mail = new htmlMimeMail();
							$mail->setSubject($tmpdata['subject']);
							$mail->setHtml($tmpdata['text'], $tmpdata['text']);
							$mail->setFrom(str_replace("\"","",$tmpdata['from']));
							$r = $mail->send(array(str_replace(array("\"","\\"),"",$to)));
							if (!$r) {
								unset($this->retJson['send']);
								$errors['to'] = "An error has occurred."; $this->retJson['formaterrors'] = $errors; break;
								break;
							}
						}

					}
					$sql = "insert into karma_emails_log (`".implode('`,`',array_keys($tmpdata))."`) values(".implode(',',$vals).") ";
					$c = new con($sql);
				break;
				default:
					$this->error();
				break;
			}
		}
	}

	protected function unique($unique,$id,$tab,$uniquefield,$bd="default"){
		$unique = i::clean($unique);
		$uniqueTemp  = $unique;
		$cont = 0;
		do {
			$sql = 'select '.$id.' from '.$tab.' where '.$uniquefield.'=\''.$unique.'\'';
			$con = new con($sql,$bd);

			if ($con->getNumRows()==0) break;
			$unique = preg_replace("/^([^\.]*)/","\\1_".$cont++,$uniqueTemp);
		} while (1);

		return $unique;
	}

	protected function autocomplete(){
		$q = mysql_real_escape_string($_GET['q']);
		$this->loadContacts($q);
		foreach($this->aContacts as $r){
			echo "\"".$r['name']."\" <".$r['email'].">|\n";
		}
		exit();
	}

	protected function drawFroms(){
		$html = '<select name="from">';
		foreach ($this->aSenders as $sender){
			$o = ''.$sender['name'].' &lt;'.$sender['email'].'&gt; ';
			$html.='<option value="'.$o.'">'.$o.'</option>';
		}
		$html.= '</select>';
		return $html;
	}

	protected function draw_fast_mail(){
		if(isset($_GET['load_id'])&&$id=(int)$_GET['load_id']){
			$c=new con("select * from karma_emails_log where id_email='".$id."' and id_usuario='".$_SESSION['__ID']."' limit 1");
			$r = $c->getResult();
			if ($c->getNumRows()>0){
				$this->retJson['noSaveOnSend'] = "true";
			}
		}
		$html = '
			<table id="emailForm">
			<tr><td>From</td><td class="u_fields">'.$this->htmlfroms.'</td></tr>
			<tr><td>To</td><td class="u_fields">
				<input type="text" name="to" class="autocomplete_to" value="'.((isset($r['to']))? htmlentities($r['to']):'').'" />
			</td></tr>
			<tr><td>Subject</td><td class="u_fields"><input type="text" name="subject" value="'.((isset($r['subject']))? $r['subject']:'').'" /></td></tr>
			<tr><td>Text Body</td><td class="u_fields"><textarea name="email_body" class="tiny_email" >'.((isset($r['text']))? $r['text']:'').'</textarea></td></tr>
			</table>

			<ul id="emailopts">
			<li id="save" ><a >save</a></li>
			<li id="send" ><a >send</a></li>
			</ul>
		';


		$this->retJson['jsCallback'] = "emailsys.tt";

		return $html;
	}

	protected function draw_sent_emails(){
		$this->retJson['jsCallback'] = "emailsys.storageBind";
		$c = new con("select * , unix_timestamp(fecha_insert) as fecha_insert from karma_emails_log where id_usuario = '".$_SESSION['__ID']."' and st='sent' order by fecha_insert desc limit 100");
		$html='<h3>Storage:</h3>';
		$html.='<div class="text_contents" >';
		if ($c->getNumRows()<=0){
			$html.='<p>No emails stored.</p>';
			return $html.'</div>';
		}else{
			while ($r=$c->getResult()){
				$html.='<p><img mod="fast_mail" src="./icons/back.png" class="load_email tooltip" alt="load this mail" title="load this mail" id_email="'.$r['id_email'].'" />'.date(K_DATETIMEFORMATNOSECS,$r['fecha_insert']).': <strong>'.$r['subject'].'</strong>, <em>'.text_utils::text_limit($r['text']).'.</em> </p>';
			}
			return $html.'</div>';
		}
	}
	protected function draw_saved_emails(){
		$this->retJson['jsCallback'] = "emailsys.storageBind";
		$c = new con("select * , unix_timestamp(fecha_insert) as fecha_insert from karma_emails_log where id_usuario = '".$_SESSION['__ID']."' and st='saved' order by fecha_insert desc limit 100 ");
		$html='<h3>Storage:</h3>';
		$html.='<div class="text_contents" >';
		if ($c->getNumRows()<=0){
			$html.='<p>No emails stored.</p>';
			return $html.'</div>';
		}else{
			while ($r=$c->getResult()){
				$html.='<p><img mod="fast_mail" src="./icons/back.png" class="load_email tooltip" alt="load this mail" title="load this mail" id_email="'.$r['id_email'].'" />'.date(K_DATETIMEFORMATNOSECS,$r['fecha_insert']).': <strong>'.$r['subject'].'</strong>, <em>'.text_utils::text_limit($r['text']).'.</em> </p>';
			}
			return $html.'</div>';
		}
	}

	protected function draw_karma_contacts(){
		return "Karma Contacts";
	}

	/*
	* metodo que devuelve el "método" a aplicar en el objeto principal $o desde index para este externalmodule de kMenu
	*/
	public function enableContentObjectMethod() {
		if ($this->kMenu->menuSectionExists('emailSystem')){
		    return "enable_email_system";
		}
		return false;
	}

	public function isAllowed() {
		return $this->kMenu->menuSectionExists('emailSystem');
	}

}

/*
 *
 * CREATE TABLE `karma_emails_log` (
  `id_email` mediumint(8) unsigned NOT NULL auto_increment,
  `from` varchar(255) default NULL,
  `to` varchar(500) default NULL,
  `subject` varchar(255) default NULL,
  `iden` varchar(255) default NULL,
  `text` text,
  `id_usuario` mediumint(8) unsigned NOT NULL,
  `fecha_insert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `st` enum('saved','sent') default 'saved',
  PRIMARY KEY  (`id_email`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `karma_emails_log_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `karma_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
 *
 */

?>
