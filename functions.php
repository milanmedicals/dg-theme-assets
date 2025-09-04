<?php
/**
 * Pharmacy Astra Child Theme functions and definitions
 */

if (!defined('ABSPATH')) exit;

// Enqueue styles and scripts from GitHub
add_action('wp_enqueue_scripts', function () {
    // Get CSS from GitHub
    $css_url = 'https://raw.githubusercontent.com/yourusername/pharmacy-theme/main/style.css';
    wp_enqueue_style('pharma-github-css', $css_url, [], null);
    
    // Get JS from GitHub
    $js_url = 'https://raw.githubusercontent.com/yourusername/pharmacy-theme/main/script.js';
    wp_enqueue_script('pharma-github-js', $js_url, ['jquery'], null, true);
    
    // Fonts & Icons (from CDN)
    wp_enqueue_style('pharma-google-fonts', 'https://fonts.googleapis.com/css2?family=Linux+Libertine:wght@400;500;700&display=swap', [], null);
    wp_enqueue_style('pharma-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', [], '6.4.0');
}, 20);

/**
 * ----------------------------------------------------
 * 0) Early init (placeholder for priority ordering)
 * ----------------------------------------------------
 */
add_action('init', function () {}, 0);

/**
 * ----------------------------------------------------
 * 1) Remove Astra + Woo wrappers we replace
 * ----------------------------------------------------
 */
add_action('init', function () {
    // Astra header pieces
    @remove_action('astra_header', 'astra_header_markup');
    @remove_action('astra_masthead', 'astra_masthead_primary_template');
    @remove_action('astra_masthead_content', 'astra_primary_header');
    @remove_action('astra_masthead_content', 'astra_mobile_primary_header');
    @remove_action('astra_header', 'astra_top_header_bar');
    @remove_action('astra_masthead_content', 'astra_header_branding');
    @remove_action('astra_masthead_content', 'astra_primary_navigation_markup');
    @remove_action('astra_masthead_content', 'astra_woo_header_cart');

    // Woo default wrappers (we render our own)
    @remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    @remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    @remove_action('woocommerce_before_main_content', 'astra_woo_primary_start', 10);
    @remove_action('woocommerce_after_main_content', 'astra_woo_primary_end', 10);

    // Woo: less clutter on cart/checkout
    @remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10);

    // Woo: single product summary layout
    remove_all_actions('woocommerce_single_product_summary');
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 15);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 20);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
}, 1);

// ---------- Force remove Add-to-Cart (hooks + filters) ----------
add_action('init', function () {
    // Remove Add to Cart button from loop (shop/archive)
    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10);
    remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

    // Remove Add to Cart button from single product
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

    // Remove Astra cart icon/header cart
    remove_action('astra_masthead_content', 'astra_woocommerce_header_cart', 10);
    remove_action('astra_masthead_content', 'astra_woo_header_cart', 10);
    remove_action('astra_header', 'astra_cart_with_menu', 10);
}, 5);

// Clear loop Add to Cart link completely
add_filter('woocommerce_loop_add_to_cart_link', '__return_empty_string', 100);

// Safety: prevent products being purchasable on archives
add_filter('woocommerce_is_purchasable', function ($purchasable, $product) {
    if (is_admin()) return $purchasable;
    if (is_shop() || is_product_category() || is_product_tag() || is_post_type_archive('product')) {
        return false;
    }
    return $purchasable;
}, 100, 2);

// ---------- Add WhatsApp button on single product ----------
add_action('woocommerce_single_product_summary', function () {
    if (!is_product()) return;
    global $product;
    if (!is_a($product, 'WC_Product')) return;

    $phone = '919911661996';

    // Clean WhatsApp message
    $raw_message = "Hello, I'm interested in this product:\n\n"
                 . $product->get_name() . "\n"
                 . "( " . get_permalink($product->get_id()) . " )\n\n"
                 . "Can you please provide more details ?";
    $msg  = rawurlencode($raw_message);
    $href = "https://wa.me/{$phone}?text={$msg}";

    // WhatsApp icon
    $icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="16" height="16" fill="currentColor" aria-hidden="true" style="margin-right:8px;vertical-align:middle">
        <path d="M16 .5C7.44.5.5 7.44.5 16c0 2.82.74 5.57 2.14 7.98L.52 31.5l7.72-2.03A15.44 15.44 0 0 0 16 31.5c8.56 0 15.5-6.94 15.5-15.5S24.56.5 16 .5zm0 28a12.97 12.97 0 0 1-7.32-1.94l-.52-.3-4.58 1.2 1.22-4.46-.34-.56A12.97 12.97 0 1 1 16 28.5zm7.19-9.7c-.39-.2-2.28-1.12-2.63-1.25s-.61-.2-.87.2-1 1.25-1.22 1.51-.45.29-.84.1c-.39-.2-1.64-.6-3.12-1.91-1.15-1.03-1.92-2.3-2.14-2.69s-.02-.6.18-.79c.18-.18.39-.45.59-.68.2-.23.26-.39.39-.65s.07-.49-.04-.68c-.1-.2-.87-2.1-1.2-2.89-.32-.77-.64-.67-.87-.68h-.74c-.26 0-.68.1-1.04.49-.36.39-1.36 1.33-1.36 3.23s1.39 3.74 1.58 4c.2.26 2.73 4.18 6.61 5.87.92.4 1.64.63 2.2.81.92.29 1.75.25 2.41.15.73-.11 2.28-.93 2.6-1.82.32-.9.32-1.66.23-1.82-.1-.15-.36-.25-.74-.45z"/>
    </svg>';

    echo '<a href="' . esc_url($href) . '" target="_blank" rel="noopener" aria-label="Chat about ' . esc_attr($product->get_name()) . ' on WhatsApp" class="button alt dg-whatsapp-button">'
         . $icon . '<span>' . __('Chat on WhatsApp', 'pharmacy-theme') . '</span></a>';
}, 31);

/**
 * ----------------------------------------------------
 * 2) Theme setup
 * ----------------------------------------------------
 */
add_action('after_setup_theme', function () {
    add_theme_support('woocommerce');
    add_theme_support('title-tag');
    register_nav_menus([
        'primary' => __('Primary Menu', 'pharmacy-theme'),
        'footer'  => __('Footer Menu', 'pharmacy-theme'),
    ]);
}, 20);

/**
 * ----------------------------------------------------
 * 4) Header / Body Open (site chrome + search + nav)
 * ----------------------------------------------------
 */
add_action('wp_body_open', function () { ?>
    <header class="header-top-line" role="banner">
        <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
        <div class="contact-info">
            <a href="tel:+919911661996"><i class="fas fa-phone"></i> +91 9911661996</a>
            <a href="https://wa.me/919911661996" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            <a href="https://t.me/milanmedicals" target="_blank" rel="noopener noreferrer" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
        </div>
    </header>

    <section class="header-shipping-info">
        <strong>World wide shipping</strong><br>Medicines from all over the world. We will help you find any drug in 24 hours.
    </section>

    <nav class="primary-menu" role="navigation" aria-label="Primary Menu">
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'items_wrap' => '<ul>%3$s</ul>']); ?>
    </nav>

    <section class="search-cart-bar" role="search">
        <label for="search-input">Search for a drug:</label>
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" id="search-input" name="s" placeholder="Enter drug name..." value="<?php echo esc_attr(get_search_query()); ?>" />
            <input type="hidden" name="post_type" value="product" />
            <button type="submit" aria-label="Search"><i class="fas fa-search"></i></button>
        </form>
        <?php if (class_exists('WooCommerce')) : ?>
            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="cart-icon-wrapper" aria-label="View cart">
                <i class="fas fa-shopping-cart"></i><span class="cart-count-bubble"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
            </a>
        <?php endif; ?>
    </section>

    <button class="hamburger-menu" aria-label="Toggle Product Catalog"><i class="fas fa-bars"></i> Product Catalog</button>

    <div class="page-layout-wrapper">
        <aside class="sidebar-categories" role="complementary" aria-label="Product Categories Sidebar"><?php pharma_custom_product_catalog_sidebar(); ?></aside>
        <main class="main-content-area" role="main">
            <?php if (is_front_page()) : ?>
            <div class="international-service">
                <h2>International drug discovery service</h2>
                <p>Dear patient, drugs in different countries have different commercial names. If the active substance and dosage are the same, the drugs are interchangeable. The site was developed taking into account the recommendations of doctors and automatically determines interchangeable drugs.</p>
                <div class="flags">
                    <img src="https://upload.wikimedia.org/wikipedia/en/4/41/Flag_of_India.svg" alt="India" width="140" height="93"/>
                   <img src="https://upload.wikimedia.org/wikipedia/commons/0/0d/Flag_of_Saudi_Arabia.svg" alt="Saudi Arabia" width="140" height="93"/>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/cb/Flag_of_the_United_Arab_Emirates.svg" alt="UAE" width="140" height="93"/>
                    <img src="https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg" alt="UK" width="140" height="93"/>
                   <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg" alt="USA" width="140" height="93"/>
                </div>
            </div>
            <?php endif; ?>
<?php
}, 5);

/**
 * ----------------------------------------------------
 * 5) Footer (rich, with disclaimers and links)
 * ----------------------------------------------------
 */
add_action('wp_footer', function () { 
    echo '</main></div>'; 
    ?>
    <footer class="custom-footer" role="contentinfo">
        <div class="footer-content">
            <div class="footer-columns">
                <div class="footer-col">
                    <h4><?php esc_html_e('About Us', 'pharmacy-theme'); ?></h4>
                    <p><?php esc_html_e('Drugs General is a trusted global platform listing rare and hard-to-find medicines, connecting patients with verified suppliers worldwide.', 'pharmacy-theme'); ?></p>
                </div>
                <div class="footer-col">
                    <h4><?php esc_html_e('Quick Links', 'pharmacy-theme'); ?></h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'footer-links',
                        'fallback_cb'    => function () {
                            echo '<ul class="footer-links">';
                            echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'pharmacy-theme') . '</a></li>';
                            echo '<li><a href="' . esc_url(get_post_type_archive_link('product')) . '">' . esc_html__('Shop', 'pharmacy-theme') . '</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . esc_html__('Contact', 'pharmacy-theme') . '</a></li>';
                            echo '<li><a href="' . esc_url(home_url('/privacy-policy')) . '">' . esc_html__('Privacy Policy', 'pharmacy-theme') . '</a></li>';
                            echo '</ul>';
                        },
                        'depth'         => 1
                    ]);
                    ?>
                </div>
                <div class="footer-col">
                    <h4><?php esc_html_e('Contact', 'pharmacy-theme'); ?></h4>
                    <ul>
                        <li><strong><?php esc_html_e('Email:', 'pharmacy-theme'); ?></strong> support@drugsgeneral.com</li>
                        <li><strong><?php esc_html_e('Phone:', 'pharmacy-theme'); ?></strong> +91-9911661996</li>
                        <li><strong><?php esc_html_e('Address:', 'pharmacy-theme'); ?></strong> <?php esc_html_e('Drugs General, India', 'pharmacy-theme'); ?></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4><?php esc_html_e('Trust & Compliance', 'pharmacy-theme'); ?></h4>
                    <ul>
                        <li><i class="fas fa-shield-alt" style="color:#3366cc;"></i> <?php esc_html_e('Global Medicine Directory', 'pharmacy-theme'); ?></li>
                        <li><i class="fas fa-lock" style="color:#3366cc;"></i> <?php esc_html_e('Secure Payments', 'pharmacy-theme'); ?></li>
                        <li><i class="fas fa-globe" style="color:#3366cc;"></i> <?php esc_html_e('Fast Worldwide Shipping', 'pharmacy-theme'); ?></li>
                        <li><i class="fas fa-check-circle" style="color:#3366cc;"></i> <?php esc_html_e('Verified Medicines', 'pharmacy-theme'); ?></li>
                    </ul>
                </div>
            </div>
            <p class="price-disclaimer"><?php esc_html_e('Prices are for informational purposes only and subject to change.', 'pharmacy-theme'); ?></p>
            <div class="footer-disclaimer">
                <p><strong><?php esc_html_e('Disclaimer:', 'pharmacy-theme'); ?></strong> <?php esc_html_e('Drugs General is an independent medicine directory . We are not affiliated with, nor endorsed by, the trademark holders of the medicines we supply. Products offered, including generics, are subject to availability and are provided only where legally permissible. Buyers are solely responsible for compliance with their local laws and regulations. All medicines should be used strictly under the guidance of a licensed healthcare professional.', 'pharmacy-theme'); ?></p>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo esc_html(date('Y')); ?> <?php esc_html_e('Drugs General. All Rights Reserved.', 'pharmacy-theme'); ?></p>
            </div>
        </div>
    </footer>
    <?php
    // Debug footer menu (sanitized logging)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $locations = get_nav_menu_locations();
        error_log('Footer Menu Location: ' . wp_json_encode($locations));
        if (isset($locations['footer'])) {
            $menu = wp_get_nav_menu_object($locations['footer']);
            error_log('Footer Menu Object: ' . wp_json_encode($menu));
            $menu_items = wp_get_nav_menu_items($locations['footer']);
            error_log('Footer Menu Items: ' . wp_json_encode($menu_items));
        } else {
            error_log('No Footer Menu Assigned');
        }
    }
}, 5);

/**
 * ----------------------------------------------------
 * 6) Product catalog sidebar helper
 * ----------------------------------------------------
 */
if (!function_exists('pharma_custom_product_catalog_sidebar')) {
    function pharma_custom_product_catalog_sidebar() {
        if (class_exists('WooCommerce')) {
            $cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 7, 'orderby' => 'name', 'order' => 'ASC']);
            if (!is_wp_error($cats) && !empty($cats)) {
                echo '<h3>Product Catalog</h3><ul class="pharma-product-categories">';
                foreach ($cats as $cat) {
                    echo '<li><a href="' . esc_url(get_term_link($cat)) . '">' . esc_html
