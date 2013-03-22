<?php
/*
Plugin Name: Funny Quotes
Plugin URI: http://www.webcraft-multimedia.net/funnyquotes
Description: Display random funny quotes on your website.
Author: Webcraft-Multimedia
Author URI: http://www.webcraft-multimedia.net/
Version: 1.0
*/

require_once('funnyquotes-admin.php');
require_once('funnyquotes-widget.php');

function funny_quotes_install(){
    //On recupere la variable global de Wordpress
    global $wpdb;

    //On crée une nouvelle table
    $table_name = $wpdb->prefix . "funny_quotes";

    $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          author varchar(55) NOT NULL,
          quote varchar(255) NOT NULL,
          PRIMARY KEY  (id)
      );";

    //On récupere la fonction d'update de la base pour ajouter notre table
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_activation_hook( __FILE__, 'funny_quotes_install' );

