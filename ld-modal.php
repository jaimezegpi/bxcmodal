<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: BxC Modal
Plugin URI: https://jaime.zegpi.cl/develop/modal
Description: Super Basic Plugin for Modal Windows. Programmable on specific Dates and with content suitable for Desktop and Mobile. You can select where to display it or simply have it displayed across your website.
Author: Jaime A. Zegpi B:
*/
function bxc_date_picker(){
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('bxc-admin-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
}
add_action('admin_enqueue_scripts', 'bxc_date_picker'); 

function bxc_popup_create_posttype() {
  register_post_type( 'bxcmodal',
    array(
      'labels' => array(
        'name' => __( 'BXC-Modal' ),
        'singular_name' => __( 'bxcmodal' )
      ),
      	'public' => true,
      	'has_archive' => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'capability_type'    => 'page',
		'hierarchical'       => true,
		'menu_position'      => 3,
		'menu_icon'           => 'dashicons-welcome-view-site',
		'supports'           => array( 'title', 'editor', 'author','custom-fields'),
      'rewrite' => array('slug' => 'bxcmodal'),
    )
  );
}
add_action( 'init', 'bxc_popup_create_posttype' );

function bxc_bxcmodal_add_my_post_types_to_query( $query ) {
  if ( is_home() && $query->is_main_query() )
    $query->set( 'post_type', array( 'post', 'page', 'bxcmodal' ) );
  return $query;
}
add_action( 'pre_get_posts', 'bxc_bxcmodal_add_my_post_types_to_query' );

function bxc_bxcmodal_add_meta_boxes(){
	add_meta_box("bxc_publish_period_meta", "Publish Period", "add_publish_period_meta_box", "bxcmodal", "normal", "low");
}

function add_publish_period_meta_box(){
	global $post;
	$custom = get_post_custom( $post->ID );
 
	?>

	<style>.width99 {width:99%;}</style>

	<p>
		<label>Show in :</label><br />
		<input type="text" name="show_in" value="<?= @$custom["show_in"][0] ?>" placeHolder="?" />
		<p>* = In All pages</p>
		<p>/ = Only in homePage</p>
		<p>services = Only in services pages -use slug text</p>
		<p>services, contact, notice = Show in services, contact and notice  -use slug text</p>
	</p>
	<hr>
	<p>
		<label>Date start:</label><br />
		<input id="remo" type="text" name="date_start" value="<?= @$custom["date_start"][0] ?>" class="width99 datepicker" />
	</p>

	<p>
		<label>Date End:</label><br />
		<input type="text"  name="date_end" value="<?= @$custom["date_end"][0] ?>" class="width99 datepicker" />
	</p>
	<hr>
	<p>
		<label>Image Desktop URL:</label><br />
		<input type="text"  class="media-input" name="image" value="<?= @$custom["image"][0] ?>" class="width99" />
		<button class="media-button button">Select Image</button>
	</p>
	<p>
		<label>Imagen Mobile URL:</label><br />
		<input type="text" class="media-input" name="image_mobile" value="<?= @$custom["image_mobile"][0] ?>" class="width99" />
		<button class="media-button button">Select Image</button>
	</p>

	<p>
		<label>Link:</label><br />
		<input type="text"  name="link" value="<?= @$custom["link"][0] ?>" class="width99" />
	</p>

	<p>
		If you Set a Post Content this images dont show.
	</p>

	<hr>
	<br>

	<p>
		<label>Script JS:</label><br />
		<textarea name="script_js" value="<?= @$custom["script_js"][0] ?>" class="width99" ><?= @$custom["script_js"][0] ?></textarea>
	</p>

	<p>
		<label>Script CSS:</label><br />
		<textarea name="script_css" value="<?= @$custom["script_css"][0] ?>" class="width99" ><?= @$custom["script_css"][0] ?></textarea>
	</p>



	<p>
		* Iexplorer and Safary show errors in date format. use YY/mm/dd for enter date.
	</p>
	<script type="text/javascript">
    	jQuery(function() { jQuery( ".datepicker" ).datepicker({ dateFormat : "yy/mm/dd"}); });

var bxc_media_init = function(selector, button_selector)  {
    var clicked_button = false;
 	console.log("original "+selector);
    jQuery(selector).each(function (i, input) {
        var button = jQuery(input).next(button_selector);
        button.click(function (event) {
            event.preventDefault();
            var selected_img;
            clicked_button = jQuery(this);
 
            // check for media manager instance
            if(wp.media.frames.gk_frame) {
                wp.media.frames.gk_frame.open();
                return;
            }
            // configuration of the media manager new instance
            wp.media.frames.gk_frame = wp.media({
                title: 'Select image',
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Use selected image'
                }
            });
 
            // Function used for the image selection and media manager closing
            var bxc_media_set_image = function() {
                var selection = wp.media.frames.gk_frame.state().get('selection');
 
                // no selection
                if (!selection) {
                    return;
                }
 
                // iterate through selected elements
                selection.each(function(attachment) {
                    var url = attachment.attributes.url;
                    clicked_button.prev(selector).val(url);
                    console.log("asignando "+selector);

                });
            };
 
            // closing event for media manger
            wp.media.frames.gk_frame.on('close', bxc_media_set_image);
            // image selection event
            wp.media.frames.gk_frame.on('select', bxc_media_set_image);
            // showing media manager
            wp.media.frames.gk_frame.open();
        });
   });
};
bxc_media_init('.media-input', '.media-button');

	</script>
	<?php
}

/**
 * Save custom field data when creating/updating posts
 */
function save_bxc_bxcmodal_custom_fields(){
  global $post;
 
  if ( $post )
  {
  	update_post_meta($post->ID, "show_in", @$_POST["show_in"]);
    update_post_meta($post->ID, "date_start", @$_POST["date_start"]);
    update_post_meta($post->ID, "date_end", @$_POST["date_end"]);

	update_post_meta($post->ID, "image", @$_POST["image"]);
	update_post_meta($post->ID, "image_mobile", @$_POST["image_mobile"]);
	update_post_meta($post->ID, "link", @$_POST["link"]);

    update_post_meta($post->ID, "script_js", @$_POST["script_js"]);
    update_post_meta($post->ID, "script_css", @$_POST["script_css"]);
  }
}
add_action( 'admin_init', 'bxc_bxcmodal_add_meta_boxes' );
add_action( 'save_post', 'save_bxc_bxcmodal_custom_fields' );

//---

function bxc_head(){
	?>
<!-- Add the slick-theme.css if you want default styling -->
<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>/css/tiny-slider.css">
<!--[if (lt IE 9)]><script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.helper.ie8.js"></script><![endif]-->
	<?php
}

function bxc_admin_head(){
	?>
<!--<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__);?>/css/jquery-ui.css" type="text/css" media="all" />-->
<!-- <script src="<?php echo plugin_dir_url(__FILE__);?>/js/jquery-ui.min.js"></script>-->
	<?php
}


function bxc_PopUp(){
	//if (!is_front_page()){ return false; }
	global $post;
	$array = array(
		'post_type' => 'bxcmodal',
		'posts_per_page' => -1,
		'order_by' => 'date',
		'order' => 'ASC',
		'post_status' => 'publish'
	);

	$bxcmodallist = get_posts( $array );
	$bxcmodallist_n = count($bxcmodallist);
	if ( !$bxcmodallist ){ return false; }
	$date_current = date('Ymd');
   
    $post_slug=$post->post_name;
    $pop_render = "";
    $pop_render_js = "";
    $pop_render_css = "";
    $pop_render_item_active = false;
	
	foreach ($bxcmodallist AS $key => $pop) {
		$show_in = get_post_meta($pop->ID,'show_in',true);

		$active = "";	
		$show_in = trim( strtolower($show_in) );
		//if ($show_in == '/' && !is_front_page()){ continue; }

		if ( $show_in=="/" && !is_front_page()){ continue; }

		if ($show_in !== '*' && $show_in !== '/' && $show_in !== '' ){
			$show_array = explode(",", $show_in);
			if ( !in_array($post_slug, $show_array) ){ continue; }
		}
		$date_start = str_replace("/","", get_post_meta($pop->ID,'date_start',true) );
		$date_end = str_replace("/","", get_post_meta($pop->ID,'date_end',true) );

		if ( $date_current>=$date_start && $date_current<=$date_end ){
				
			$pop_render_css.= get_post_meta($pop->ID,'script_css');
			
			if ( $pop->post_content!=='' ){
				if (!$pop_render_item_active){ $active = "active";}
				$pop_render.= '<div class="slide '.$active.' pop_id_'.$pop->ID.'" onClick="bxc_closeModal();"><div class="slide_interior">'.apply_filters('the_content',$pop->post_content).'</div></div>';
				$pop_render_item_active = $pop_render_item_active + 1;
			}else{
				if (!$pop_render_item_active){ $active = "active";}
				$image = get_post_meta($pop->ID,'image');
				$image_mobile = get_post_meta($pop->ID,'image_mobile');
				$link = get_post_meta($pop->ID,'link');
				if ( is_array($link) ){$link=$link[0];}
				if ( $image ){
					$image_generic = $image[0];
					if (wp_is_mobile()){
						$image_generic = $image_mobile[0];
					}
					$pop_render.= '<div class="slide '.$active.' pop_id_'.$pop->ID.'" onClick="bxc_closeModal();"><div class="slide_interior" style="background-image:url('.$image_generic.'); background-size:contain; background-repeat:no-repeat; background-position:center; height:80vh;" onClick="window.location.href=\'".$link."\'" ><a href="'.$link.'" style="width:100%; height:100%;" >&nbsp;</a>';
					$pop_render.='</div></div>';
				}
				$pop_render_item_active = $pop_render_item_active + 1;
			}
		}
	}

	if ($pop_render){
	?>

			<style type="text/css">
				.bxc_bxcmodal_frame{
					font-family: "Arial";
					position: fixed;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
					background-color: rgba(0,0,0,0.8);
					z-index:999998;
				}

				.bxc_bxcmodal_frame_container{
					position: relative;
					top: 5%;
					left: 5%;
					height: 90%;
					width: 90%;
					color:#1c1c1c;
					text-align: center;
				    display: flex;
				    justify-content: center;
				    flex-direction: column;
				}

				.bxc_bxcmodal_frame_container img{
					width: auto;
					max-height: 80vh;
				}

				.bxc_bxcmodal_frame_container_close{
					position: absolute;
					right: 5px;
					top: 5px;
					background-color: #1c1c1c;
					color: #fff;
					border-radius: 5px;
					width: 22px;
					height: 22px;
					text-align: center;
					cursor: pointer;
					padding: 5px;
				}

				.bxc_bxcmodal_frame_vertical, .slide{
					width: 100%;
					height: 100%;
				}
				.bxc_bxcmodal_frame .tns-outer{
					min-width: 80vw;
				}
				.bxc_bxcmodal_frame .slide{
					display: none;
				}

				.slide.active{
					display: block;
				}

				.slide_interior{
					display: flex;
					align-items: center;
					flex-direction: column;
					justify-content: center;
				}

				.bxc_bxcmodal_frame_container_close svg{
					padding: 3px;
				}

				.bxc_bxcmodal_frame_container_bg_solid{
					background-color: rgba(255,255,255,0.9);
				}

				.bxc_bxcmodal_frame_container.bxc_bxcmodal_frame_container_bg_solid{
					display: flex;
					align-items: center;
					flex-direction: column;
					justify-content: center;
				}

				.bxc_bxcmodal_arrow_prev{
					position: absolute;
					left: 10px;
				}

				.bxc_bxcmodal_arrow{
					cursor: pointer;
				}

				.bxc_bxcmodal_arrow_next{
					position: absolute;
					right: 10px;
				}

				.bxc_mobile{
					display:none;
				}

				@media (max-width: 780px) {
					.bxc_mobile{
						display:block;
					}

					.bxc_desktop{
						display:none;
					}
				}

				<?php
				echo $pop_render_css;
				?>

			</style>

			<div id="bxc_bxcmodal_frame" class="bxc_bxcmodal_frame uno">
				<div class="bxc_bxcmodal_frame_container <?php if ( $pop->post_content!=='' ){ echo "bxc_bxcmodal_frame_container_bg_solid"; }?>">
					<div class="bxc_bxcmodal_frame_container_close" onClick="bxc_closeModal();" >
						<svg height="16" width="16">
						<line x1="0" y1="0" x2="16" y2="16" style="stroke:#fff;stroke-width:2" />
						<line x1="16" y1="0" x2="0" y2="16" style="stroke:#fff;stroke-width:2" />
						</svg>
					</div>
					<div class="bxc_bxcmodal_frame_vertical my-slider" >
							<?php echo $pop_render; ?>
					</div>
				</div>
			</div>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/tiny-slider/2.9.2/min/tiny-slider.js"></script> -->
<script src="<?php echo plugin_dir_url(__FILE__);?>/js/tiny-slider.js"></script>

<!-- NOTE: prior to v2.2.1 tiny-slider.js need to be in <body> -->
	<script type="text/javascript">
		<?php
		echo $pop_render_js
		?>
	var slider = tns({
		container: '.my-slider',
		items: 1,
		slideBy: 'page',
		autoplay: <?php if ($bxcmodallist_n>1){ echo "true"; }else{echo "false";} ?>,
		controls: false,
		autoplayButtonOutput: false,
		nav: false,
	});

	function bxc_closeModal(){
		document.getElementById('bxc_bxcmodal_frame').style.display="none";
	}

	function bxc_showSlide(direction){
		var next_slide = $(".slide.active").nextElementSibling;
		var prev_slide = $(".slide.active").previousElementSibling;

		if ( direction=="p" && prev_slide ){
			$(".slide").style.display="none";
			$(".slide.active").classList.remove('active');
			prev_slide.classList.add('active');
			prev_slide.style.display="block";
			console.log("prev");
		}

		if ( direction=="n" && next_slide ){
				$(".slide").style.display="none";
				$(".slide.active").classList.remove('active');
				next_slide.classList.add('active');
		}
	}
	var arrow_next = document.getElementsByClassName("bxc_bxcmodal_arrow_next");
	if (arrow_next.length){

arrow_next[0].addEventListener("click",function(){
		bxc_showSlide("n");
	});
	}
	
	</script>


	<?php

	}


}

add_action( 'wp_footer', 'bxc_PopUp' );
add_action( 'wp_head', 'bxc_head' );
add_action( 'admin_head', 'bxc_admin_head' );


add_filter( 'manage_edit-bxcmodal_columns', 'bxc_customColumn' ) ;
function bxc_customColumn( $columns ) {

	$columns = array(
		'title' => __( 'Title' ),
		'date_start' => __( 'Date Start' ),
		'date_end' => __( 'Date End' ),
		'date' => __( 'Creation Date' )
	);

	return $columns;
}

add_action( 'manage_bxcmodal_posts_custom_column', 'bxc_columns', 10, 2 );

function bxc_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'duration' column. */
		case 'date_start' :
			$date_start = get_post_meta( $post_id, 'date_start', true );
			if ( empty( $date_start ) )
				echo __( 'Unknown' );
			else
				printf( __( '%s' ), $date_start );
			break;
		case 'date_end' :
			$date_start = get_post_meta( $post_id, 'date_end', true );
			if ( empty( $date_start ) )
				echo __( 'Unknown' );
			else
				printf( __( '%s' ), $date_start );
			break;


		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}