<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     block
 * Name:     rss
 * Filename: block.rss.php   
 * Version:  1.0
 * Date:     September 17, 2004
 * Author:   webtigo.ch (info@webtigo.ch)
 * Purpose:  iterates over a rss feed
 * Requires: * pear XML_RSS lib: http://pear.php.net
 * Install:  * put block.rss.php in plugin dir
 *           * call from within a template
 *
 * Input:    file = local file or URL of rss
 *           length= number of items to parse (optional) 
 *           if not defined all the file will be parsed 
 * 
 *
 * Example:  last three php news:
 *
 *           {rss file="http://www.php.net/news.rss" length="3"}
 *             <a href="{$rss_item.link}" target=_blank> {$rss_item.title} </a><br>
 *           {/rss}
 * -------------------------------------------------------------
 */

function smarty_block_rss ($params, $content, &$smarty,&$repeat) {

  if ($repeat) {
   require_once "XML/RSS.php";
   $rss =& new XML_RSS($params["file"]);
   $rss->parse();
   $res=$rss->getItems();
   $index=0;
   if($params['length']>0){
     $res=array_slice($res,0,$params['length']);
   }

  }else{
    $res=array_pop($smarty->_RSS_parse_res);
    $index=array_pop($smarty->_RSS_parse_index)+1;
  }
  $item=$res[$index];
  $check_length=($length==0 or $index<$length);
  $repeat=!empty($item);

  if($item){
     $smarty->assign("rss_item",$item);
     $smarty->_RSS_parse_res[]=&$res;
     $smarty->_RSS_parse_index[]=&$index;
  }
  return $content;
}
?>

