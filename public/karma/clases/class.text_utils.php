<?php 
class text_utils{
	public static function text_limit($text, $limit = false, $final_tag = "...", $append_text=""){
		$espacio = " ";
        if ($limit===false) $limit = '125';
        $text = strip_tags($text);
        
        if (strlen($text)<=$limit) return $text;
        
        $texta = substr($text, 0, $limit);
        $p = array();
        $p1 = strpos(substr($text, $limit, 30), ' ');
        $p2 = strpos(substr($text, $limit, 30), ',');
        $p3 = strpos(substr($text, $limit, 30), '.');
        if ($p1) $p[]=$p1;
        if ($p2) $p[]=$p2;
        if ($p3) $p[]=$p3;
        if(sizeof($p) > 0)
        	$texta = $texta.substr($text, $limit, ($p[0]));
        return $texta.$final_tag.$append_text;
    }
}
?>