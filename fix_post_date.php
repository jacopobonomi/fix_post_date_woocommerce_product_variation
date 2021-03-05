<?php
function regenerate_data(){
	global $wpdb;
	$variable_products = $wpdb->get_results( "select * from mod267_posts where post_type = 'product_variation'" );
	foreach($variable_products as $variable_product){
		$parent_post = $wpdb->get_results( "select * from mod267_posts where post_type='product' AND ID = ".$variable_product->post_parent);		
		if(isset($parent_post[0])){
			$update_post_date_query = $wpdb->get_results("UPDATE mod267_posts SET post_date = '".$parent_post[0]->post_date."' WHERE mod267_posts.ID =".$variable_product->ID);
			$update_post_date_gmt_query = $wpdb->get_results("UPDATE mod267_posts SET post_date_gmt = '".$parent_post[0]->post_date_gmt."' WHERE mod267_posts.ID = ".$variable_product->ID);
		}
	}
}
add_action('wp_footer', 'regenerate_data');
?>
