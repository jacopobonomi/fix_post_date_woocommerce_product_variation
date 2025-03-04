<?php
/**
 * Plugin Name: Sync Variation Dates
 * Plugin URI: https://github.com/jacopobonomi/fix_post_date_woocommerce_product_variation
 * Description: Synchronizes product variation dates with their parent product dates
 * Version: 1.0
 * Author: Jacopo Bonomi
 * Author URI: https://github.com/jacopobonomi
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sync_Variation_Dates {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_admin_menu() {
        add_submenu_page(
            'tools.php',
            'Sync Variation Dates',
            'Sync Variation Dates',
            'manage_options',
            'sync-variation-dates',
            array($this, 'admin_page')
        );
    }
    
    public function register_settings() {
        register_setting('sync_variation_dates_settings', 'sync_variation_dates_last_run');
    }
    
    public function admin_page() {
        global $wpdb;
        
        $message = '';
        $message_class = '';
        
        if (isset($_POST['sync_variation_dates_nonce']) && wp_verify_nonce($_POST['sync_variation_dates_nonce'], 'sync_variation_dates_action')) {
            
            $processed = $this->regenerate_data();
            
            update_option('sync_variation_dates_last_run', current_time('mysql'));
            
            $message = sprintf(__('Success! %d product variations have been synchronized with their parent product dates.', 'sync-variation-dates'), $processed);
            $message_class = 'updated';
        }
        
        $last_run = get_option('sync_variation_dates_last_run');
        ?>
        <div class="wrap">
            <h1><?php _e('Sync Variation Dates', 'sync-variation-dates'); ?></h1>
            
            <?php if ($message): ?>
            <div class="<?php echo $message_class; ?> notice is-dismissible">
                <p><?php echo $message; ?></p>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <h2><?php _e('Synchronize Product Variation Dates', 'sync-variation-dates'); ?></h2>
                <p><?php _e('This tool will update all product variation post dates to match their parent product dates.', 'sync-variation-dates'); ?></p>
                
                <?php if ($last_run): ?>
                <p><?php printf(__('Last run: %s', 'sync-variation-dates'), $last_run); ?></p>
                <?php endif; ?>
                
                <form method="post">
                    <?php wp_nonce_field('sync_variation_dates_action', 'sync_variation_dates_nonce'); ?>
                    <p>
                        <input type="submit" class="button button-primary" value="<?php _e('Synchronize Dates', 'sync-variation-dates'); ?>" />
                    </p>
                </form>
            </div>
        </div>
        <?php
    }
    
    public function regenerate_data() {
        global $wpdb;
        
        $processed = 0;
        
        $posts_table = $wpdb->prefix . 'posts';
        
        $variable_products = $wpdb->get_results("SELECT * FROM {$posts_table} WHERE post_type = 'product_variation'");
        
        foreach ($variable_products as $variable_product) {
            $parent_post = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$posts_table} WHERE post_type = 'product' AND ID = %d",
                $variable_product->post_parent
            ));
            
            if ($parent_post) {
                $wpdb->update(
                    $posts_table,
                    array(
                        'post_date' => $parent_post->post_date,
                        'post_date_gmt' => $parent_post->post_date_gmt
                    ),
                    array('ID' => $variable_product->ID)
                );
                
                $processed++;
            }
        }
        
        return $processed;
    }
}

$sync_variation_dates = new Sync_Variation_Dates();
