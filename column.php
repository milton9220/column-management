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

        //add_action('init',array($this,'cm_added_wordcount_as_postmeta_data'));
    }
    public function cm_text_domain(){
        load_plugin_textdomain( 'cm', false, dirname( __FILE__ ) . "/languages" );
    }
    public function cm_plugins_started(){
        add_filter('manage_posts_columns',array($this,'cm_post_column'));
        add_filter('manage_pages_columns',array($this,'cm_pages_column'));        
        add_action('manage_posts_custom_column',array($this,'cm_add_custom_post_column'),10,2);
        add_action('manage_pages_custom_column',array($this,'cm_add_custom_page_column'),10,2);
        add_filter('manage_edit-post_sortable_columns',array($this,'cm_column_sortable'));

        
        add_action('save_post',array($this,'cm_update_post_count_on_save_post'));
        add_action('pre_get_posts',array($this,'cm_post_sorted_by_wordcount'));
        add_action('pre_get_posts',array($this,'cm_custom_post_filter_data'));
        add_action('pre_get_posts',array($this,'cm_custom_thumbnail_filter_data'));
        add_action('pre_get_posts',array($this,'cm_custom_wordcount_filter_data'));
        add_action('restrict_manage_posts',array($this,'cm_custom_filter_post'));
        add_action('restrict_manage_posts',array($this,'cm_custom_filter_thumbnail'));
        add_action('restrict_manage_posts',array($this,'cm_custom_filter_wordcount'));
    }
    public function cm_post_column($columns){
       $columns['id']=__('ID','cm');
       $columns['thumbnail']=__('Thumbnail','cm');
       $columns['wordcount']=__('Word Count','cm');
       return $columns;
    }
    public function cm_pages_column($columns){
        $columns['id']=__('ID','cm');
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
        if('wordcount'==$column){
            // $_post=get_post($post_id);
            // $content=$_post->post_content;
            // $wordn=str_word_count(strip_tags($content));
            $wordn = get_post_meta( $post_id, 'wordt', true );
            echo $wordn;
        }
    }
    public function cm_add_custom_page_column($column,$post_id){
        if('id'==$column){
            echo $post_id;
        }
    }
    public function cm_column_sortable($columns){
        $columns['wordcount']='wordt';
        return $columns;
    }
    //ei funciton 1bar run kore remove kore dibo karon just wordcount r value gula every post id ar against a wordcount gula post meta akare save korar jonno
    // public function cm_added_wordcount_as_postmeta_data(){
    //     $_posts=get_posts(array(
    //         'post_type' =>'post',
    //         'posts_per_page'=>-1
    //     ));

    //     foreach($_posts as $post){
    //         $content=$post->post_content;
    //         $wordcount=str_word_count(strip_tags($content));
    //         update_post_meta($post->ID,'wordt',$wordcount);
    //     }
    // }

    public function cm_post_sorted_by_wordcount($wpquery){
        if(!is_admin()){
            return;
        }
        $orderby=$wpquery->get('orderby');
        if('wordt'==$orderby){
            $wpquery->set('meta_key','wordt');
            $wpquery->set('orderby','meta_value_num');
        }
    }
    public function cm_update_post_count_on_save_post($post_id){
            $_p=get_post($post_id);
            $content=$_p->post_content;
            $wordcount=str_word_count(strip_tags($content));
            update_post_meta($post_id,'wordt',$wordcount);
    }
    public function cm_custom_filter_post(){
        if(isset($_GET['post_type']) && $_GET['post_type'] !='post'){ //display only on posts page
            return;
        }
        $filter_value= isset($_GET['DEMOFILTER']) ? $_GET['DEMOFILTER']:'';

        $values=array(
            '0'=>__('Select Status','cm'),
            '1'=>__('Some Posts','cm'),
            '2'=>__('Some Post++','cm')
        );
        ?>
        <select name=DEMOFILTER>
        <?php foreach($values as $key=> $value){
            
            printf('<option value="%s" %s>%s</option>',$key,$key==$filter_value ? "selected='selected'":'',$value);
         } ?>
         </select>
    <?php 
    }
    public function cm_custom_post_filter_data($wpquery){
        if(!is_admin()){
            return;
        }
        $filter_value = isset( $_GET['DEMOFILTER'] ) ? $_GET['DEMOFILTER'] : '';
        if ( '1' == $filter_value ) {
            $wpquery->set( 'post__in', array(39) );
        } else if ( '2' == $filter_value ) {
            $wpquery->set( 'post__in', array( 5) );
        }
    }
    public function cm_custom_filter_thumbnail(){
        if(isset($_GET['post_type']) && $_GET['post_type'] !='post'){ //display only on posts page
            return;
        }
        $filter_value= isset($_GET['THUMBFILTER']) ? $_GET['THUMBFILTER']:'';

        $values=array(
            '0'=>__('Select Thumbnail','cm'),
            '1'=>__('Has Thumbnail','cm'),
            '2'=>__('No Thumbnail','cm')
        );
        ?>
        <select name=THUMBFILTER>
        <?php foreach($values as $key=> $value){
            
            printf('<option value="%s" %s>%s</option>',$key,$key==$filter_value ? "selected='selected'":'',$value);
         } ?>
         </select>
    <?php
    }
    public function cm_custom_filter_wordcount(){
        if(isset($_GET['post_type']) && $_GET['post_type'] !='post'){ //display only on posts page
            return;
        }
        $filter_value= isset($_GET['WORDCFILTER']) ? $_GET['WORDCFILTER']:'';

        $values=array(
            '0'=>__('Select Wordcount','cm'),
            '1'=>__('Above 100','cm'),
            '2'=>__('200 to 400','cm'),
            '3'=>__('Below 50','cm'),
        );
        ?>
        <select name=WORDCFILTER>
        <?php foreach($values as $key=> $value){
            
            printf('<option value="%s" %s>%s</option>',$key,$key==$filter_value ? "selected='selected'":'',$value);
         } ?>
         </select>
    <?php
    }
    public function cm_custom_thumbnail_filter_data($wpquery){
        if(!is_admin()){
            return;
        }
        $filter_value = isset( $_GET['THUMBFILTER'] ) ? $_GET['THUMBFILTER'] : '';
        if ( '1' == $filter_value ) {
            $wpquery->set('meta_query',array(
                array(
                    'key'=>'_thumbnail_id',
                    'compare' =>'EXITS'
                )
            ));
        } else if ( '2' == $filter_value ) {
            $wpquery->set( 'meta_query',array(
                array(
                    'key' =>'_thumbnail_id',
                    'compare' =>'NOT EXISTS'
                )
            ) );
        }
    }
    public function cm_custom_wordcount_filter_data($wpquery){
        if(!is_admin()){
            return;
        }
        $filter_value = isset( $_GET['WORDCFILTER'] ) ? $_GET['WORDCFILTER'] : '';
        if ( '1' == $filter_value ) {
            $wpquery->set('meta_query',array(
                array(
                    'key'=>'wordt',
                    'value'=>'100',
                    'compare' =>'>',
                    'type'   =>'NUMERIC'
                )
            ));
        } else if ( '2' == $filter_value ) {
            $wpquery->set( 'meta_query',array(
                array(
                    'key' =>'wordt',
                    'value'=>array('200','400'),
                    'compare' =>'BETWEEN',
                    'type' =>'NUMERIC'
                )
            ) );
        }
        else if ( '3' == $filter_value ) {
            $wpquery->set( 'meta_query',array(
                array(
                    'key' =>'wordt',
                    'value'=>'50',
                    'compare' =>'<',
                    'type' =>'NUMERIC'
                )
            ) );
        }
    }
}
new Column_Management();
