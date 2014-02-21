<?php
/*
Plugin Name: FW Fussnoten
Plugin URI: http://www.wieser.at/wordpress/plugins/fussnoten
Description: Fussnote unter Beiträge, Verwendung ist für Fixe Texte, Bilder, und auch für Shortcodes.
Flexible können Fussnoten erstellt werden, dabei wird als Selektor Kategorie verwendet, somit
wird die Fussnoten bei allen Beiträgen mit der selben Kategorie verwenden.
als Shortcode liefert diese Plugin [cf name="metafeldname"] mit
Version: 0.6
Author: Franz Wieser
Author URI: http://www.wieser.at/
Update Server: http://www.wieser.at/wordpress/plugins/
Licenc:  GPLv2

*/
// Register Custom Post Type
function fw_fussnote_custom_post_type() {

	$labels = array(
		'name'                => _x( 'fussnote', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'fussnote', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'fussnote', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'add_new_item'        => __( 'Add New Item', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'fussnote', 'text_domain' ),
		'description'         => __( 'fussnote daten', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
		'taxonomies'          => array( 'category', 'post_tag', 'groups' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 20,
		'menu_icon'           => '',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'rewrite'            => array( 'slug' => 'fussnote' ),
	);
	register_post_type( 'fussnote', $args );

}

// Hook into the 'init' action
add_action( 'init', 'fw_fussnote_custom_post_type', 0 );
add_action('admin_init', 'fw_fussnote_tags_und_category');

function fw_fussnote_tags_und_category()
{
	register_taxonomy_for_object_type('category', 'page');
}

add_filter( 'the_content', 'fw_fussnote_filter', 20 );

function fw_fussnote_filter( $content ) {
$categories = get_the_category();
//$categories = get_terms( 'category', array( ) );
//echo '<pre>';
//var_dump($categories);
//echo '</pre>';
$cats=0;
if($categories){
	foreach($categories as $category) {
	//	$fusscontent.= 'ID'.$category->term_id .' name '.$category->cat_name;
		$cats.=$category->term_id.',';
	}

}

//$fusscontent.='-'.$cats.'-';

  
  $argf=array(
  	
  	'post_type' => 'fussnote',
  	'cat'=>$cats,
  	'posts_per_page'=>'-1',
);
/*
'tax_query' => array(
		//'relation' => 'AND',
		array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => array( $cat )
		),
),
*/
         	$my_queryf = new WP_Query( $argf );
   if ( $my_queryf->have_posts() && $cats!=0) {
       while ( $my_queryf->have_posts() ) { 
           $my_queryf->the_post();
           $curpostid=get_the_ID();
         //  $feld1meta=get_post_meta(get_the_ID(),'eintrag',true);
          // $fusscontent.='Fussnote:'.get_the_title().''.$feld1meta.'<br/>';
           $fusscontent.=get_the_content();
           //$my_query->post_content;
}
}
//$out.='<br/>';
	        wp_reset_postdata();
//in_category( $category, $_post )
  


        
//$content.=$out;
$content=$content.$fusscontent;
if (!is_admin())
$content=do_shortcode($content);	

    return $content;
}

function fw_fussnoten_thecontentfilter($filterpostid,$content)
{
$out="FILTER";
	//$text=get_option('fw_fussnote');
	$text=$content;
	$style='style='.get_option('fw_fussnote_style');
   //	get_post_meta($post_id, $key, $single);
         
	    
   
	$pos = strpos($text, '%content%');
    if ($pos !== false) {
                $text = explode('%content%', $text);
                foreach($text as $i => $str) {
                    if (!empty($str)) {
		       $text[$i] = '<span '.$style.' >'.$str.'</span>';
                    }
                }
                $content = $text[0].$content.$text[1];
	       } else {
               $content = $content.'<span '.$style.' >'.$text.'</span>';
	       }
   /*    	$pos = strpos($text, '%key=');
    if ($pos !== false) {
                $text = explode('%key', $content2);
                foreach($text as $i => $str) {
                    if (!empty($str)) {
		       $text[$i] = 'metafield';
                    }
                }
                $content = $text[0].$content.$text[1];
	       } else {
               $content = $content.'<span '.$style.' >'.$text.'</span>';
	       }
*/

 
	
			 //	$content = sprintf('%s %s', $content, get_option('fw_fussnote') );

$content.='debug:'.$out;
$content=do_shortcode($content);	
return $content;	
	
}

//add_filter( 'the_content', 'fussnote_filter_old', 20 );


function fw_fussnote_filter_old( $content ) {

  

        if ( is_home() )
	$text=get_option('fw_fussnote');
	$style='style='.get_option('fw_fussnote_style');
   //	get_post_meta($post_id, $key, $single);
         
	    
   
	$pos = strpos($text, '%content%');
    if ($pos !== false) {
                $text = explode('%content%', $text);
                foreach($text as $i => $str) {
                    if (!empty($str)) {
		       $text[$i] = '<span '.$style.' >'.$str.'</span>';
                    }
                }
                $content = $text[0].$content.$text[1];
	       } else {
               $content = $content.'<span '.$style.' >'.$text.'</span>';
	       }
   /*    	$pos = strpos($text, '%key=');
    if ($pos !== false) {
                $text = explode('%key', $content2);
                foreach($text as $i => $str) {
                    if (!empty($str)) {
		       $text[$i] = 'metafield';
                    }
                }
                $content = $text[0].$content.$text[1];
	       } else {
               $content = $content.'<span '.$style.' >'.$text.'</span>';
	       }
*/

 
	
			 //	$content = sprintf('%s %s', $content, get_option('fw_fussnote') );
		if (in_category('news'))
		{	
		}
		if(is_page('Beispiel Seite'))
		   {
		   $content="Seite Beispiel".$content;
		   }


$content=do_shortcode($content);

    return $content;
}

add_action( 'add_meta_boxes', 'fw_fussnote_side_add' );
function fw_fussnote_side_add()
{
	//global $post;
	//if( has_shortcode( $post->post_content, 'dbsfield') ) {
		
	
	add_meta_box( 'fussnoteside', 'Fussnote', 'fw_fussnote_side_box', 'fussnote', 'normal', 'high' );
	
	//}
}

function fw_fussnote_side_box( $post )
{
$fcontent.='Shortcode: [fussnote id="'.$post->ID.'"]';

echo $fcontent;
}


add_action('admin_menu', 'fussnote_option_menu');

function fw_fussnote_option_menu() {


	add_options_page('Fussnote Option', 'Fussnote', 'manage_options', 'fussnote', 'fw_fussnote_option');


}

function fw_fussnote_option()
{
?>
<div class="form-wrap">
   <h3>FW Fussnoten (Testplugin) </h3>
mit diesem FW Fussnoten Plugin können flexible Fussnoten erstellt werden.<br/>
mit hinzufügen wird im Textfeld des Editor der Gewünschte Inhalte erstellt und gestaltet,<br/>
dabei können Texte, Bilder und auch Shortcodes zusammengestellt werden.<br>
als Selektor wird Kategorie angewählt, bei allen Beiträgen dieser gleichen Kategorie wird der Inhalt der Fussnote angezeigt.<br/>

mit dem mitglieferten Shortcode [cf name="metaboxfieldname"] können metaboxfields (benutzerdefinierte Felder)<br/>

 


   weitere Information zu diesem und weiteren Plugins auf <a href="http://www.wieser.at/wordpress/plugins">www.wieser.at/wordpress/plugins</a><br/>
   
   </div>
   
<?php

}
function fw_fussnote_my_txtbox($atts, $content = null) {

extract(shortcode_atts(array(
    'textcolor' => '#555555',
    'bgcolor' => '#eeeeee',
    'bordercolor' => '#dddddd',
    'border' => '2px',
    'bordertype' => 'solid'
  ), $atts));
    return "<p style=\"padding: 2px 6px 4px 6px; color: $textcolor; background-color: $bgcolor; border: $bordercolor $border $bordertype\">$content</p>";

}

add_shortcode('fussnote_txtbox','fw_fussnote_my_txtbox');

function fw_custom_field($atts,$content=null) {
global $post;

extract(shortcode_atts(array(     'key' => 'Ort',      ), $atts));
$thekey= get_post_meta($post->ID, $key , true) ;
   return $thekey;





}


add_shortcode('cf', 'fw_fussnote_custom_field');

function fw_fussnote_ausgabe($atts, $content)
{
extract(shortcode_atts(array(
'id'=>'',
'name'=>'',
'link'=>'',
), $atts));



 $argf=array(
  	
  	'post_type' => 'fussnote',
  	
  	'posts_per_page'=>'-1',
  	'post__in' => array( $id),
);
/*
'tax_query' => array(
		//'relation' => 'AND',
		array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => array( $cat )
		),
),
*/
         	$my_queryf = new WP_Query( $argf );
   if ( $my_queryf->have_posts() ) {
       while ( $my_queryf->have_posts() ) { 
           $my_queryf->the_post();
           $curpostid=get_the_ID();
         //  $feld1meta=get_post_meta(get_the_ID(),'eintrag',true);
          // $fusscontent.='Fussnote:'.get_the_title().''.$feld1meta.'<br/>';
           $fcontent.=get_the_content();
           //$my_query->post_content;
}
}
//$out.='<br/>';
	        wp_reset_postdata();
//in_category( $category, $_post )
  


        
//$content.=$out;
//$content=$content.$fusscontent;
//if (!is_admin())
//$content=do_shortcode($content);	

    return $fcontent;


   

}



//add_shortcode('fn', 'fussnote_content');
add_shortcode('fussnote','fw_fussnote_ausgabe');



?>
