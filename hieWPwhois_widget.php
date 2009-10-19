<?php

/* 
 -------------------------------------------------------------------------------

    Copyright 2009 Arzberger&Krueger GbR  (email : wpplugins@arzberger-krueger.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

-------------------------------------------------------------------------------
*/

// Funktion zur Ausgabe des Widgets
function ak_hieWPwhois_widget( $args ) { // $args enthält Strings die vor/nach dem Widget und vor/nach dem Titel ausgegeben werden sollen
	// Option lesen
	$akOptions = get_option( 'ak_hieWPwhois_options' );
	$akResponse = get_option( 'ak_hieWPwhois_response' );
	// Ausgabe
	$akExtensions = explode(',',$akOptions['allowed_extensions']);
	
	echo "\r\n<!-- Widget of hieWPwhois Plugin: http://hostitesy.de/hieWPwhois -->\r\n";
	echo $args['before_widget'];
	echo '<form method="post" id="hieWPwhoisSearch">';
	echo $args['before_title'].$akOptions['widget_header'].$args['after_title'];
	echo '<input type="text" name="ak_domain" style="width:'.$akOptions['widget_domain_input_width'].'px" value="'.stripslashes( $_POST['ak_domain'] ).'" />';
	echo '<select name="ak_extension" style="width:'.$akOptions['widget_select_extension_width'].'px">';
	foreach ($akExtensions as $akExtension) {
		echo '<option value="'.str_replace(".", "", $akExtension).'"';
		if(str_replace(".", "", $akExtension) == $_POST['ak_extension']) echo " selected";
		echo '>'.$akExtension.'</option>';
	}
	echo '</select>';
	echo '<br />';
	echo '<input type="submit" name ="ak_domain_search_submit" value="'.$akOptions['widget_submit_button_text'].'"/>';
	echo '</form>';
	if( $_POST['ak_domain_search_submit'] AND $_POST['ak_domain'] ) {

		$akDomain = $_POST['ak_domain'].'.'.$_POST['ak_extension'];
		require_once "Net/Whois.php";
		
		$akWhois = new Net_Whois;
		$akData = $akWhois->query ( $akDomain );
		$akExt = $_POST['ak_extension'];
		$akS = 'AKDOMAIN';
		
		if (preg_match("/".str_replace($akS, $akDomain, $akResponse[$akExt.'_free'])."/i", $akData)) {
			$akOut = str_replace($akS, $akDomain, $akOptions['domain_available_text']);
    		$akOut = str_replace('AKLINK', $akOptions['register_link'], $akOut);
    		echo str_replace('AKLINKTARGET', $akOptions['register_link_target'], $akOut);    		
		} elseif (preg_match("/".str_replace($akS, $akDomain, $akResponse[$akExt.'_invalid'])."/i", $akData)) {
   			echo str_replace($akS, $akDomain, $akOptions['domain_request_error']);		
		} else {
			echo str_replace($akS, $akDomain, $akOptions['domain_registered_text']);
			echo "<div id=\"akInfoBox\"><a href=\"#\">WHOIS<span><pre>$akData</pre></span></a></div>";
    		echo "<style type=\"text/css\">";
			echo "<!--";    		
    		echo "@import \"wp-content/plugins/hieWPwhois/hieWPwhois.css\";";
			echo "-->";
			echo "</style>";			
		}
	}
	echo $args['after_widget'];  
	echo "\r\n<!-- /Widget of hieWPwhois Plugin: http://hostitesy.de/hieWPwhois -->\r\n";
}

// Funktion zur Ausgabe der Widget-Konfiguration
function ak_hieWPwhois_widget_konfiguration() {
	// Option Array aktuallisieren
	if( $_POST['ak-hieWPwhois-widget-submit'] ) {
		$akOptions['widget_header'] = stripslashes( $_POST['ak-hieWPwhois-widget-header'] );
    	$akOptions['widget_domain_input_width'] = stripslashes( $_POST['ak-hieWPwhois-widget-domain-input-width'] );
    	$akOptions['widget_select_extension_width'] = stripslashes( $_POST['ak-hieWPwhois-widget-select-extension-width'] );
		update_option( 'ak_hieWPwhois_options', $akOptions ); // Option in der Datenbank updaten
  	}
	// Option lesen
	$akOptions = get_option( 'ak_hieWPwhois_options' );
	
  	// Konfigurationsoberfläche ausgeben
	echo '<p>'.'Titel:<br />';
	echo '<input type="text" id="ak-hieWPwhois-widget-header" name="ak-hieWPwhois-widget-header" value="'.$akOptions['widget_header'].'" /><br />';
	echo 'Länge des Domainfeldes <small>(in Pixel)</small>:<br />';
	echo '<input type="text" id="ak-hieWPwhois-widget-domain-input-width" name="ak-hieWPwhois-widget-domain-input-width" value="'.$akOptions['widget_domain_input_width'].'" /><br />';  
	echo 'Länge des TLD Feldes <small>(in Pixel)</small>:<br />';
	echo '<input type="text" id="ak-hieWPwhois-widget-select-extension-width" name="ak-hieWPwhois-widget-select-extension-width" value="'.$akOptions['widget_select_extension_width'].'" /><br />';
	echo '</p>';
	echo '<input type="hidden" name="ak-hieWPwhois-widget-submit" id="ak-hieWPwhois-widget-submit" value="1" />';
}
 
// Widget Funktionen im "plugins_loaded"-Hook registrieren
function ak_hieWPwhois_widget_registrieren() {
  // Widget registrieren
  register_sidebar_widget(
    'hieWPwhois Widget', // Name des Widgets
    'ak_hieWPwhois_widget' // Name der Funktion die den Widget Inhalt ausgibt
  );
  // Konfigurationsoberfläche registrieren
  register_widget_control(
    'hieWPwhois Widget', // Name des Widgets
    'ak_hieWPwhois_widget_konfiguration' // Name der Funktion die den Widget Konfiguration ausgibt
    // optional kann noch die Größe angegeben werden
  );
}
add_action( 'plugins_loaded', 'ak_hieWPwhois_widget_registrieren' );

?>