<?php
/****
Plugin Name:Column Management
Plugin URI:
Author: Milton
Author URI:
Description: This is my first plugin
Version: 1.0
License:
License URI:
Text Domain:cm
Domain path:/languages/
******/
class Column_Management{
    public function __construct(){
        add_action('plugins_loaded',array($this,'cm_text_domain'));
        add_action('admin_init',array($this,'cm_plugins_started'));
    }
    public function cm_text_domain(){
        load_plugin_textdomain( 'cm', false, dirname( __FILE__ ) . "/languages" );
    }
    public function cm_plugins_started(){
        add_filter('manage_posts_columns',array($this,'cm_post_column'));
        add_action('manage_posts_custom_column',array($this,'cm_add_custom_post_column'),10,2);
    }
    public function cm_post_column($columns){
       $columns['id']=__('ID','cm');
       $columns['thumbnail']=__('Thumbnail','cm');
       return $columns;
    }
    public function cm_add_custom_post_column($column,$post_id){
        if('id'==$column){
            echo $post_id;
        }
        if('thumbnail'==$column){
            $thumbnail=get_the_post_thumbnail($post_id,array('80','80'));
            echo $thumbnail;
        }
    }
}
new Column_Management();
