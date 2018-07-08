<?php
// Copyright 2014 Tristan van Bokkem

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["AutoLink"] = array(
	"name" => "自动填充",
	"description" => "当用户帖子中含有图片或者视频网站链接时自动填充为相应格式(支持图片和优酷/油管)",
	"version" => "1.2.1",
	"author" => "Ramouch0",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2"
);

class ETPlugin_AutoLink extends ETPlugin {

	// ACCEPTED PROTOTYPES
	//
	var $accepted_protocols = array(
	  'http://', 'https://', 'ftp://', 'ftps://', 'mailto:', 'telnet://',
	  'news://', 'nntp://', 'nntps://', 'feed://', 'gopher://', 'sftp://' );

	//
	// AUTO-EMBED IMAGE FORMATS
	//
	var $accepted_image_formats = array(
	  'gif', 'jpg', 'jpeg', 'tif', 'tiff', 'bmp', 'png', 'svg', 'ico' );


public function handler_format_format($sender)
{
	// quick check to rule out complete wastes of time
	if( strpos( $sender->content, '://' ) !== false || strpos($sender->content, 'mailto:' ) !== false )
	{
	  $sender->content = preg_replace_callback( '/(?<=^|\r\n|\n| |\t|<br>|<br\/>|<br \/>)!?([a-z]+:(?:\/\/)?)([^ <>"\r\n\?]+)(\?[^ <>"\r\n]+)?/i', array( &$this, 'autoLink' ), $sender->content );
	 }
}

public function autoLink( $link = array())
{
  // $link[0] = the complete URL
  // $link[1] = link prefix, lowercase (e.g., 'http://')
  // $link[2] = URL up to, but not including, the ?
  // $link[3] = URL params, including initial ?

  // sanitise input
  $link[1] = strtolower( $link[1] );
  if( !isset( $link[3] ) ) $link[3] = '';

  // check protocol is allowed
  if( !in_array( $link[1], $this->accepted_protocols ) ) return $link[0];

  // check for forced-linking and strip prefix
  $forcelink = substr( $link[0], 0, 1 ) == '!';
  if( $forcelink ) $link[0] = substr( $link[0], 1 );

  $params = array();
  $matches = array();


  if( !$forcelink && ( $link[1] == 'http://' || $link[1] == 'https://' ) )
  {
	$width = 797;
	$height = 447;
	// images
	if( preg_match( '/\.([a-z]{1,5})$/i', $link[2], $matches ) && in_array( strtolower( $matches[1] ), $this->accepted_image_formats ) )
	  return '<img class="auto-embedded" src="'.$link[1].$link[2].$link[3].'" alt="-image-" title="'.$link[1].$link[2].$link[3].'" />';
	// youtube
	else if( strcasecmp( 'www.youtube.com/watch', $link[2] ) == 0 && $this->params( $params, $link[3], 'v' ) )
	  return '<iframe width="'.$width.'" height="'.$height.'"  src="'.$link[1].'www.youtube.com/embed/'.$params['v'].'" frameborder="0" allowfullscreen></iframe>';
	else if( preg_match( '/^(?:www\.)?youtu\.be\/([^\/]+)/i', $link[2], $matches ))
	  return '<iframe width="'.$width.'" height="'.$height.'"  src="'.$link[1].'www.youtube.com/embed/'.$matches[1].'" frameborder="0" allowfullscreen></iframe>';
	// google video
	else if( preg_match( '/^(video\.google\.co(?:m|\.uk))\/videoplay$/i', $link[2], $matches ) && $this->params( $params, $link[3], 'docid' ) )
	  return '<embed class="video" style="width:'.$width.'px;height:'.$height.'px" allowfullscreen="true" src="'.$link[1].$matches[1].'/googleplayer.swf?docid='.$params['docid'].'&fs=true" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" align="top" />';
	// vimeo
	else if( preg_match( '/^(?:www\.)?vimeo\.com\/([^\/]+)/i', $link[2], $matches ) )
	return '<iframe src="http://player.vimeo.com/video/'.$matches[1].'?byline=0&amp;portrait=0" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	// Facebook
	else if( strcasecmp( 'www.facebook.com/photo.php', $link[2] ) == 0 && $this->params( $params, $link[3], 'v' ) )
		return '<iframe src="https://www.facebook.com/video/embed?video_id='.$params['v'].'" width="540" height="420" frameborder="0"></iframe>';
	// youku
  else if( preg_match( '/^(?:v\.)?youku\.com\/v_show\/id_([^\/]+)\.html/i', $link[2], $matches ))
	  return '<iframe width="'.$width.'" height="'.$height.'"  src="https://player.youku.com/embed/'.$matches[1].'" frameborder="0" allowfullscreen></iframe>';
}


  // default to linkifying
	return '<a href="'.$link[0].'" rel="nofollow external">'.$link[0].'</a>';

}

/*Reads query parameters
params : result array as key => value
string : query string
required : array of required parameters key
@return true if required parameters are present.
*/
function params( &$params, $string, $required )
{
  $string = html_entity_decode($string);
  if( !is_array( $required ) ) $required = array( $required );
  if( substr( $string, 0, 1 ) == '?' ) $string = substr( $string, 1 );
  $params = array();
  $bits = preg_split( '/&/', $string );
  foreach( $bits as $bit ) {
	$pair = preg_split( '/=/', $bit, 2 );
	if( in_array( $pair[0], $required ) ) $params[ $pair[0] ] = $pair[1];
  }
  return count( $required ) == count( $params );
}

}
?>
