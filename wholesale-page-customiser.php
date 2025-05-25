<?php
/**
 * Plugin Name: Product PDF Tab for WooCommerce
 * Description: Adds a custom PDF tab to WooCommerce product data panel with a media uploader.
 * Version: 1.0
 * Author: Dhanesh Kumar
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// 1. Add new tab
add_filter('woocommerce_product_data_tabs', 'add_pdf_product_data_tab');
function add_pdf_product_data_tab($tabs)
{
    $tabs['pdf_tab'] = array(
        'label'    => __('PDF', 'woocommerce'),
        'target'   => 'pdf_product_data',
        'class'    => array(),
        'priority' => 90,
    );
    return $tabs;
}

// 2. Add fields to the new tab
add_action('woocommerce_product_data_panels', 'add_pdf_product_data_fields');
function add_pdf_product_data_fields()
{
    global $post;
?>
    <div id='pdf_product_data' class='panel woocommerce_options_panel'>
        <div class='options_group'>
            <p class="form-field">
                <label for="_product_pdf_url"><?php _e('Upload Product PDF', 'woocommerce'); ?></label>
                <input type="text" class="short" name="_product_pdf_url" id="_product_pdf_url"
                    value="<?php echo esc_attr(get_post_meta($post->ID, '_product_pdf_url', true)); ?>" />
                <button type="button" class="button upload_pdf_button"><?php _e('Upload PDF'); ?></button>
                <span class="description"><?php _e('Upload or select a PDF file from media library.', 'woocommerce'); ?></span>
            </p>
        </div>
    </div>
<?php
}

// 3. Save field value
add_action('woocommerce_process_product_meta', 'save_pdf_product_data_fields');
function save_pdf_product_data_fields($post_id)
{
    if (isset($_POST['_product_pdf_url'])) {
        update_post_meta($post_id, '_product_pdf_url', esc_url_raw($_POST['_product_pdf_url']));
    }
}

// 4. Enqueue media uploader script
add_action('admin_footer', 'product_pdf_upload_js');
function product_pdf_upload_js()
{
    global $pagenow;
    if ($pagenow !== 'post.php' && $pagenow !== 'post-new.php') return;
?>
    <script>
        jQuery(function($) {
            let file_frame;

            $('.upload_pdf_button').on('click', function(e) {
                e.preventDefault();

                if (file_frame) {
                    file_frame.open();
                    return;
                }

                file_frame = wp.media({
                    title: 'Select or Upload a PDF',
                    button: {
                        text: 'Use this PDF'
                    },
                    library: {
                        type: 'application/pdf'
                    },
                    multiple: false
                });

                file_frame.on('select', function() {
                    const attachment = file_frame.state().get('selection').first().toJSON();
                    if (attachment.url.endsWith('.pdf')) {
                        $('#_product_pdf_url').val(attachment.url);
                    } else {
                        alert('Please select a PDF file.');
                    }
                });

                file_frame.open();
            });
        });
    </script>
    <?php
}
//

// Show PDF download below product image
add_action('woocommerce_product_thumbnails', 'display_pdf_download_link', 20);
function display_pdf_download_link() {
    global $post;
    $pdf_url = get_post_meta($post->ID, '_product_pdf_url', true);

    if (!empty($pdf_url)) {
        ?>
        <div class="product-pdf-download" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
            <img src="<?php echo esc_url(plugins_url('pdf-icon.png', __FILE__)); ?>" alt="PDF Icon" style="width: 20px; height: 20px; object-fit: contain;" />
            <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" class="button"><?php _e('Download PDF', 'woocommerce'); ?></a>
        </div>
        <?php
    }
}
