<?php
/*
Plugin Name: hieWPwhois
Plugin URI: http://www.hostiteasy.de/wordpress-plugin-hiewpwhois/
Description: With hieWPwhois Plugin you can query domain status and whois (DomainavailibilityCHecK).
Version: 1.0
Author: Mathias Arzberger
Author URI: http://arzberger-krueger.de

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

include_once( 'hieWPwhois_widget.php' );

// Funktion die beim aktivieren des Plugins aufgerufen werden soll
function ak_hieWPwhois_plugin_aktivieren() {
	// Option hinzufügen
	add_option( 
		'ak_hieWPwhois_options', // Key der Option
			array( // Ein Array um mehrere Werte zu speichern
				'widget_header' 				=> 'Domainverfügbarkeit',
				'allowed_extensions' 			=> '.de,.com,.net,.org,.biz',
				'widget_submit_button_text' 	=> 'Jetzt prüfen!',
				'widget_select_extension_width' => '70',
				'widget_domain_input_width' 	=> '120',
				'register_link'					=> 'http://www.hostiteasy.de/paketauswahl/?domain=AKDOMAIN',
				'register_link_target'			=> '_self',
				'domain_available_text'			=> 'Die Domain AKDOMAIN ist laut dem zuständigen Registrar verfügbar.<br><strong><a href="AKLINK" target="AKLINKTARGET">jetzt registrieren</a>.</strong>',
				'domain_registered_text'		=> 'Die Domain AKDOMAIN wurde laut zuständigen Registrar bereits registriert.',
				'domain_request_error'			=> 'Es ist ein Fehler aufgetreten, bitte überprüfen Sie Ihre Eingabe.'
    		)
  	);
  	add_option( 
		'ak_hieWPwhois_response', // Key der Option
			array( // Ein Array um mehrere Werte zu speichern
				'de_free' => 'Status:      free',
				'de_invalid' => 'Status:      invalid',
				'com_free' => 'No match for "AKDOMAIN"',
				'com_invalid' => 'Registrant:
    Unavailable',
				'net_free' => 'No match for "AKDOMAIN"',
				'net_invalid' => 'Registrant:
    Unavailable',
				'org_free' => 'NOT FOUND',
				'org_invalid' => 'Status:INACTIVE',
				'biz_free' => 'Not found: AKDOMAIN',
				'biz_invalid' => 'no-string-define'
			)
	);
}

// Funktion die beim deaktivieren des Plugins aufgerufen werden soll
function ak_hieWPwhois_plugin_deaktivieren() {
	// Option aus der Datenbank entfernen
	delete_option( 'ak_hieWPwhois_options' );
	delete_option( 'ak_hieWPwhois_response' );
}

// Funktion zur Ausgabe der Beispielseite
function ak_hieWPwhois_admin() {
	// Option lesen
	$akOptions = get_option( 'ak_hieWPwhois_options' );
	$akResponse = get_option( 'ak_hieWPwhois_response' );
	// Option Array aktuallisieren
	if( $_POST['ak-hieWPwhois-submit'] ) {
		$akOptions['allowed_extensions'] = stripslashes( $_POST['allowed_extensions'] );
		$akOptions['register_link'] = stripslashes( $_POST['register_link'] );
		$akOptions['register_link_target'] = stripslashes( $_POST['register_link_target'] );
		$akOptions['domain_available_text'] = stripslashes( $_POST['domain_available_text'] );
		$akOptions['domain_registered_text'] = stripslashes( $_POST['domain_registered_text'] );
		$akOptions['domain_request_error'] = stripslashes( $_POST['domain_request_error'] );
		$akOptions['widget_header'] = stripslashes( $_POST['ak-hieWPwhois-widget-header'] );
    	$akOptions['widget_domain_input_width'] = stripslashes( $_POST['ak-hieWPwhois-widget-domain-input-width'] );
    	$akOptions['widget_select_extension_width'] = stripslashes( $_POST['ak-hieWPwhois-widget-select-extension-width'] );
		update_option( 'ak_hieWPwhois_options', $akOptions ); // Option in der Datenbank updaten
  	}
	// Konfigurationsoberfläche ausgeben
	$akLabelStyle = 'width: 150px;';
	echo "<div class=\"wrap\">";
    echo "<h2>Allgemeine Optionen</h2>";
	echo "<form name=\"form1\" method=\"post\" action=\"\">";
    echo "<label for=\"allowed_extensions\" style=\"$akLabelStyle\">Erlaubte Domains: </label><input id=\"allowed_extensions\" name=\"allowed_extensions\" value=\"".$akOptions['allowed_extensions']."\" type=\"text\" /><br />";
	echo "<label for=\"register_link\" style=\"$akLabelStyle\">Registrier Link: </label><input id=\"register_link\" name=\"register_link\" value=\"".$akOptions['register_link']."\" type=\"text\" /><br />";
	echo "<label for=\"register_link_target\" style=\"$akLabelStyle\">Registrier Link Target: </label><select id=\"register_link_target\" name=\"register_link_target\" style=\"width: 150px;\"><option value=\"_blank\""; if($akOptions['register_link_target']=='_blank')echo " selected"; echo ">_blank</option><option value=\"_self\""; if($akOptions['register_link_target']=='_self')echo " selected"; echo ">_self</option><option value=\"_top\""; if($akOptions['register_link_target']=='_top')echo " selected"; echo ">_top</option><option value=\"_parent\" "; if($akOptions['register_link_target']=='_parent')echo " selected"; echo ">_parent</option></select><br />";
	echo "<h3>Ausgabetexte</h3>";
	echo "<label for=\"domain_available_text\" style=\"$akLabelStyle\">Domain verfügbar: </label><br /><textarea id=\"domain_available_text\" name=\"domain_available_text\" cols=\"40\" rows=\"2\" style=\"font-size:11px;\">".$akOptions['domain_available_text']."</textarea><br /><br />";
    echo "<label for=\"domain_registered_text\" style=\"$akLabelStyle\">Domain registriert: </label><br /><textarea id=\"domain_registered_text\" name=\"domain_registered_text\" cols=\"40\" rows=\"2\" style=\"font-size:11px;\">".$akOptions['domain_registered_text']."</textarea><br /><br />";
    echo "<label for=\"domain_request_error\" style=\"$akLabelStyle\">Eingabefehler: </label><br /><textarea id=\"domain_request_error\" name=\"domain_request_error\" cols=\"40\" rows=\"2\" style=\"font-size:11px;\">".$akOptions['domain_request_error']."</textarea><br />";	
	echo "<p>Erlaubte Platzhalter:<ul><li>AKDOMAIN = Domain nach der gesucht wurde.</li><li>AKLINK = Registrier Link aus den allgemeinen Einstellungen.</li><li>AKLINKTARGET = Registrier Link Target aus den allgemeinen Einstellungen.</li></ul></p>";
    echo "<h2>Widget Optionen</h2>";
	echo "<p><label for=\"ak-hieWPwhois-widget-header\" style=\"$akLabelStyle\">Titel</label>";
	echo "	<input type=\"text\" id=\"ak-hieWPwhois-widget-header\" name=\"ak-hieWPwhois-widget-header\" value=\"".$akOptions['widget_header']."\" /><br />";
	echo "	<label for=\"ak-hieWPwhois-widget-domain-input-width\" style=\"$akLabelStyle\">Länge des Domainfeldes <small>(in Pixel)</small></label>";
	echo "	<input type=\"text\" id=\"ak-hieWPwhois-widget-domain-input-width\" name=\"ak-hieWPwhois-widget-domain-input-width\" value=\"".$akOptions['widget_domain_input_width']."\" /><br />";  
	echo "	<label for=\"ak-hieWPwhois-widget-select-extension-width\" style=\"$akLabelStyle\">Länge des TLD Feldes <small>(in Pixel)</small></label>";
	echo "	<input type=\"text\" id=\"ak-hieWPwhois-widget-select-extension-width\" name=\"ak-hieWPwhois-widget-select-extension-width\" value=\"".$akOptions['widget_select_extension_width']."\" /><br />";
	echo "</p>";
    echo "<h2>Antwort Code</h2>";
  	echo "<p>Die Eingabe eigener Antwort Codes ist für die nächste Version gepplant.";
  	/* next version
	foreach ( $akResponse as $responseField => $responseCode ) {
		echo "<label for=\"ak-$responseField\" style=\"$akLabelStyle\">$responseField</label><input type=\"text\" id=\"ak-$responseField\" name=\"ak-$responseField\" value=\"$responseCode\" /><br />";
	}
	*/
	echo "<p>";
	echo "	<input type=\"hidden\" name=\"ak-hieWPwhois-submit\" id=\"ak-hieWPwhois-submit\" value=\"1\" />";
	echo "	<input type=\"submit\" value=\"Speichern\" />";
	echo "</form>";
	echo "</div>";
}

// Adminmenu Optionen erweitern
function ak_hieWPwhois_menue_hinzufuegen() {
    add_options_page('hieWPwhois Plugin', 'hieWPwhois', 9, 'hieWPwhois-options', 'ak_hieWPwhois_admin'); //optionenseite hinzufügen
}

// Registrieren der WordPress-Hooks
add_action('admin_menu', 'ak_hieWPwhois_menue_hinzufuegen');


// Die aktivieren/deaktivieren Funktionen registrieren
add_action( 'activate_'.plugin_basename(__FILE__),   'ak_hieWPwhois_plugin_aktivieren' );
add_action( 'deactivate_'.plugin_basename(__FILE__), 'ak_hieWPwhois_plugin_deaktivieren' );

?>
