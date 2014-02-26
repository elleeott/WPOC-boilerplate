<?php
function my_calendar_rss() {
$offset = (60*60*get_option('gmt_offset'));
// establish template
if ( isset($_GET['mcat']) ) { $cat_id = (int) $_GET['mcat']; } else { $cat_id = false; }
	$template = "\n<item>
    <title>{title}</title>
    <link>{link}</link>
	<pubDate>{rssdate}</pubDate>
	<dc:creator>{author}</dc:creator>  	
    <description><![CDATA[{rss_description}]]></description>
	<content:encoded><![CDATA[<div class='vevent'>
    <h1 class='summary'>{rss_title}</h1>
    <div class='description'>{rss_description}</div>
    <p class='dtstart' title='{ical_start}'>Begins: {time} on {date}</p>
    <p class='dtend' title='{ical_end}'>Ends: {endtime} on {enddate}</p>	
	<p>Recurrance: {recurs}</p>
	<p>Repetition: {repeats} times</p>
    <div class='location'>{rss_hcard}</div>
	{link_title}
    </div>]]></content:encoded>
	<dc:format xmlns:dc='http://purl.org/dc/elements/1.1/'>text/html</dc:format>
	<dc:source xmlns:dc='http://purl.org/dc/elements/1.1/'>".home_url()."</dc:source>	
	{guid}
  </item>\n";
  if ( get_option( 'mc_use_rss_template' ) == 1 ) { $templates = get_option('mc_templates'); $template = $templates['rss']; }
// add RSS headers
$charset = get_bloginfo('charset');
$output = '<?xml version="1.0" encoding="'.$charset.'"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	>
<channel>
  <title>'. get_bloginfo('name') .' Calendar</title>
  <link>'. home_url() .'</link>
  <description>'. get_bloginfo('description') . ': My Calendar Events</description>
  <language>'. get_bloginfo('language') .'</language>
  <managingEditor>'. get_bloginfo('admin_email') .' (' . get_bloginfo('name') . ' Admin)</managingEditor>
  <generator>My Calendar WordPress Plugin http://www.joedolson.com/articles/my-calendar/</generator>
  <lastBuildDate>'. mysql2date('D, d M Y H:i:s +0000', time()+$offset) .'</lastBuildDate>
  <atom:link href="'. mc_get_current_url() .'" rel="self" type="application/rss+xml" />';

	$events = mc_get_rss_events( $cat_id );
	if ( is_array( $events) ) {
		//print_r($events);
	}
	$groups = array();
	foreach ( array_keys($events) as $key ) {
		$event =& $events[$key];	
		if ( !in_array( $event->occur_group_id, $groups ) ) {
			$array = event_as_array($event);
			$output .= jd_draw_template( $array, $template, 'rss' );
			if ( $event->event_span == 1 ) {
				$groups[] = $event->occur_group_id;
			}		
		}
	}
$output .= '</channel>
</rss>';
	header('Content-type: application/rss+xml');
	header("Pragma: no-cache");
	header("Expires: 0");
echo mc_strip_to_xml($output);
}

// just a double check to try to ensure that the XML feed can be rendered.
function mc_strip_to_xml($value) {
    $ret = "";
    $current;
    if (empty($value)) {
        return $ret;
    }

    $length = strlen($value);
    for ($i=0; $i < $length; $i++) {
        $current = ord($value{$i});
        if (($current == 0x9) ||
            ($current == 0xA) ||
            ($current == 0xD) ||
            (($current >= 0x20) && ($current <= 0xD7FF)) ||
            (($current >= 0xE000) && ($current <= 0xFFFD)) ||
            (($current >= 0x10000) && ($current <= 0x10FFFF))) {
            $ret .= chr($current);
        } else {
            $ret .= " ";
        }
    }
    return $ret;
}