<?php

/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */

/************************ IgesDF ************************/

include('custom-shortcodes.php');

/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */

/* =========================
   OPEN GRAPH DINÂMICO
========================= */
function add_dynamic_og_image()
{

    if (is_admin() || !is_singular()) {
        return;
    }

    $post_id = get_queried_object_id();
    if (!$post_id) return;

    $title = get_the_title($post_id);
    $url   = get_permalink($post_id);

    $description = get_the_excerpt($post_id);
    if (!$description) {
        $description = wp_trim_words(
            wp_strip_all_tags(get_post_field('post_content', $post_id)),
            30
        );
    }

    if (function_exists('get_field')) {
        $acf_desc = get_field('resumo', $post_id);
        if ($acf_desc) {
            $description = $acf_desc;
        }
    }

    $image = '';

    if (has_post_thumbnail($post_id)) {
        $image = get_the_post_thumbnail_url($post_id, 'full');
    }

    if (!$image) {
        $meta = get_post_meta($post_id, 'og_image', true);
        if ($meta) {
            $image = is_numeric($meta)
                ? wp_get_attachment_image_url((int)$meta, 'full')
                : esc_url_raw($meta);
        }
    }

    if (!$image) {
        $image = 'https://igesdf.org.br/wp-content/uploads/2022/02/logo-home-1.png';
    }

    $image = esc_url($image);
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:image" content="' . $image . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:type" content="article">' . "\n";
    echo '<meta property="og:logo" content="https://igesdf.org.br/wp-content/uploads/2022/02/logo-home-1.png">' . "\n";
}

/* =========================
   TITLE DINÂMICO
========================= */
add_filter('pre_get_document_title', function ($title) {
    return is_singular() ? get_the_title() : $title;
}, 99);
add_action('wp_head', 'add_dynamic_og_image',5);
// // This theme uses wp_nav_menu() in two locations.
register_nav_menus(array(
    'menu_topo'  => __('Menu Topo'),
    'menu_social'  => __('Menu Social'),
    'menu_principal'  => __('Menu Principal'),
    'menu_unidades'  => __('Menu Unidades'),
));

// VLIBRAS
function vlibras_widget()
{
    echo <<<EOF
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>

    <script>
        new window.VLibras.Widget();
    </script>
EOF;
}

function vlibras_enqueue()
{
    wp_enqueue_script('vlibrasjs', 'https://vlibras.gov.br/app/vlibras-plugin.js', array(), '1.0');
    wp_add_inline_script('vlibrasjs', 'try{vlibrasjs.load({ async: true });}catch(e){}');
}

add_action('wp_footer', 'vlibras_widget');
add_action('wp_enqueue_scripts', 'vlibras_enqueue');

// Menu Superior
add_action('wp_enqueue_scripts', function () {
    if (!class_exists('\Elementor\Core\Files\CSS\Post')) {
        return;
    }
    $template_id = 25594;
    $css_file = new \Elementor\Core\Files\CSS\Post($template_id);
    $css_file->enqueue();
});

// Remove JQUERY MIGRATE
function remove_jquery_migrate($scripts)
{
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];

        if ($script->deps) { // Check whether the script has any dependencies
            $script->deps = array_diff($script->deps, ['jquery-migrate']);
        }
    }
}
add_action('wp_default_scripts', 'remove_jquery_migrate');

// Elementor Custom Post Types
function create_posttypes()
{
    /* Post tipo Ato */
    register_post_type('ato', [
        'labels' => [
            'name' => __('Estimativas'),
            'singular_name' => __('Estimativa'),
            'all_items' => __('Todas as estimativas')
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-chart-area',
        'rewrite' => ['slug' => 'ato'],
        'can_export' => true,
        'taxonomies' => ['category'],
    ]);
    add_post_type_support('ato', 'thumbnail');

    /* Post tipo noticia */
    register_post_type('noticia', [
        'labels' => [
            'name' => __('Notícias'),
            'singular_name' => __('Notícia'),
            'all_items' => __('Todas as Notícias')
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon'   => 'dashicons-megaphone',
        'rewrite' => ['slug' => 'noticia'],
        'can_export' => true,
        'publicly_queryable' => true,
        'show_in_rest' => true,
        'taxonomies' => ['category', 'post_tag'],
    ]);
    add_post_type_support('noticia', 'thumbnail', 'editor');

    /* Post tipo impresso */
    register_post_type('impresso', [
        'labels' => [
            'name' => __('Impresso'),
            'singular_name' => __('Impresso'),
            'all_items' => __('Todas os Impressos')
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-welcome-widgets-menus',
        'rewrite' => ['slug' => 'impresso'],
        'can_export' => true,
        'show_in_rest' => true,
        'taxonomies' => ['category', 'post_tag'],
    ]);
    add_post_type_support('impresso', 'thumbnail', 'editor');

    /* Post tipo Indexibilidade */
    register_post_type('dispensa', [
        'labels' => [
            'name' => __('Inexigibilidade / Dispensa'),
            'singular_name' => __('Inexigibilidade / Dispensa'),
            'all_items' => __('Todos os processos de compras inexigíveis / dispensados')
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-media-spreadsheet',
        'rewrite' => ['slug' => 'dispensa'],
        'can_export' => true,
        'taxonomies' => ['category'],
    ]);
    add_post_type_support('dispensa', 'thumbnail');

    /* Post tipo produções */
    register_post_type('producao', [
        'labels' => [
            'name' => __('Produções'),
            'singular_name' => __('Produção'),
            'all_items' => __('Todas as Produções')
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-media-spreadsheet',
        'rewrite' => ['slug' => 'producao'],
        'can_export' => true,
        'taxonomies' => ['category']
    ]);
    add_post_type_support('producao', 'thumbnail');

    /* Post tipo processo seletivo 
    register_post_type('processo', [
        'labels' => [
            'name' => __('Processo Seletivo'),
            'singular_name' => __('Processo seletivo'),
            'all_items' => __('Todos os Processos Seletivos')
        ],
        'public' => true,
        'has_archive' => false,
        'menu_icon'   => 'dashicons-groups',
        'rewrite' => ['slug' => 'processo'],
        'can_export' => true,
        'taxonomies' => ['category'],
    ]);
    add_post_type_support('processo', 'thumbnail');
    */
}
add_action('init', 'create_posttypes');

add_filter('tablepress_wp_search_integration', '__return_false');

// Cache
function get_cache_file_name()
{
    return 'cache_' . md5($_SERVER['REQUEST_URI']) . '.html';
}

function serve_cache()
{
    if (is_user_logged_in() || is_singular()) {
        return false;
    }
    $cache_file = ABSPATH . 'cache/'  . get_cache_file_name();

    // Verifica se o cache existe e é recente
    if (file_exists($cache_file)) {
        echo file_get_contents($cache_file);
        exit();
    }
}

function cache_output()
{
    if (is_user_logged_in() || is_404() || is_search()) {
        return;
    }

    ob_start(function ($buffer) {
        // Safety Net: Do not cache if the page contains 404-like text.
        if (strpos($buffer, 'The page can&rsquo;t be found.') !== false) {
            return $buffer;
        }
        $timestamp = date_i18n('Y-m-d H:i:s');
        $buffer .= "\n<!-- Página cacheada em $timestamp -->";
        $buffer .= "\n<!-- Desenvolvedor: Marcos Cordeiro - Email: marcosc974@gmail.com -->";
        $cache_file =  ABSPATH . 'cache/' . get_cache_file_name();
        file_put_contents($cache_file, $buffer);
        return $buffer;
    });
}

function clear_all_cache()
{
    // Substitua pelo caminho correto do diretório de cache
    $cache_dir = ABSPATH . 'cache/';
    $files = glob($cache_dir . 'cache_*'); // obtém todos os arquivos de cache

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // exclui o arquivo
        }
    }
}

add_action('save_post', 'clear_all_cache');
add_action('deleted_post', 'clear_all_cache');
add_action('edit_post', 'clear_all_cache');
add_action('init', 'serve_cache');
add_action('wp', 'cache_output');
