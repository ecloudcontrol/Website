<?php
/**
 * Avada Child Theme - functions.php (Clean + Fixed)
 */

/* ----------------------------------------------------
 * 1. Enqueue Child CSS + Grid CSS
 * -------------------------------------------------- */
function childtheme_enqueue_styles() {

    $ver = wp_get_theme()->get('Version');

    // Load child theme stylesheet
    wp_enqueue_style(
        'child-style',
        get_stylesheet_uri(),
        [],
        $ver
    );

    // Inline CSS
    $inline_css = <<<CSS
/* Grid layout for shortcode cards */
.pages-grid {
  display: grid;
  gap: 28px;
  grid-template-columns: 1fr;
}
@media (min-width: 640px) {
  .pages-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
@media (min-width: 1024px) {
  .pages-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
}

/* Card styling */
.pages-grid__item {
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0,0,0,.04);
  background: #fff;
  height: 100%;
}

.pages-grid__item img {
  width: 100%;
  aspect-ratio: 16/9;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 10px;
}

/* Clamp long titles */
.pages-grid__item h3 {
  margin: 0 0 10px;
  line-height: 1.3;
  font-size: 1.25rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: calc(1.3em * 2);
}
.pages-grid__item h3 a { text-decoration: none; }
.pages-grid__item h3 a:hover { text-decoration: underline; }

.pages-grid__item p { margin: 0; }
CSS;

    wp_add_inline_style('child-style', $inline_css);
}
add_action('wp_enqueue_scripts', 'childtheme_enqueue_styles', 20);



/* ----------------------------------------------------
 * 2. Load Child Theme Textdomain (Translation)
 * -------------------------------------------------- */
function childtheme_lang_setup() {
    $lang = get_stylesheet_directory() . '/languages';
    load_child_theme_textdomain('Avada', $lang);
}
add_action('after_setup_theme', 'childtheme_lang_setup');



/* ----------------------------------------------------
 * 3. Google Analytics 4
 * -------------------------------------------------- */
function childtheme_google_analytics() {
    if ( current_user_can('manage_options') ) return;
    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-EVM06EJTVD"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-EVM06EJTVD');
    </script>
    <?php
}
add_action('wp_head', 'childtheme_google_analytics');



/* ----------------------------------------------------
 * 4. Shortcode: [pages_by_page_type value="AI Agents" per_page="12"]
 * -------------------------------------------------- */
add_shortcode('pages_by_page_type', function($atts) {

    $atts = shortcode_atts(
        [
            'value'    => '',
            'per_page' => 12,
        ],
        $atts,
        'pages_by_page_type'
    );

    $value    = sanitize_text_field($atts['value']);
    $per_page = max(1, intval($atts['per_page']));
    $paged    = max(1, get_query_var('paged') ?: get_query_var('page') ?: 1);

    $meta_query = [];

    if ($value !== '') {
        $meta_query[] = [
            'key'     => 'page_type',
            'value'   => $value,
            'compare' => '=',
        ];
    } else {
        $meta_query[] = [
            'key'     => 'page_type',
            'value'   => '',
            'compare' => '!=',
        ];
    }

    $q = new WP_Query([
        'post_type'      => 'page',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'meta_query'     => $meta_query,
        'orderby'        => 'menu_order title',
        'order'          => 'ASC',
    ]);

    ob_start();

    if ($q->have_posts()) {

        echo '<div class="pages-grid">';

        while ($q->have_posts()) {
            $q->the_post();

            echo '<article class="pages-grid__item">';
            echo '<h3><a href="'.esc_url(get_permalink()).'">'.esc_html(get_the_title()).'</a></h3>';

            if (has_post_thumbnail()) {
                echo '<a href="'.esc_url(get_permalink()).'">'
                     . get_the_post_thumbnail(get_the_ID(), 'medium')
                     . '</a>';
            }

            the_excerpt();

            echo '</article>';
        }

        echo '</div>';

        echo paginate_links([
            'total'   => $q->max_num_pages,
            'current' => $paged,
        ]);

        wp_reset_postdata();
    } else {
        echo '<p>No pages found.</p>';
    }

    return ob_get_clean();
});



/* ----------------------------------------------------
 * 5. Move reCAPTCHA Badge to Bottom-Left
 * -------------------------------------------------- */
function childtheme_move_recaptcha_to_left() {

    $css = "
      .grecaptcha-badge,
      .grecaptcha-badge iframe {
        position: fixed !important;
        left: 16px !important;
        right: auto !important;
        bottom: 16px !important;
        z-index: 2147483647 !important;
      }
      @media (max-width: 600px) {
        .grecaptcha-badge { bottom: 78px !important; }
      }
    ";

    wp_add_inline_style('child-style', $css);

    wp_register_script('child-recaptcha-left', false, [], null, true);
    wp_enqueue_script('child-recaptcha-left');

    $js = "
      (function() {
        function moveBadge(el) {
          if (!el || !el.style) return;
          el.style.position = 'fixed';
          el.style.left = '16px';
          el.style.right = 'auto';
          el.style.bottom = '16px';
          el.style.zIndex = '2147483647';
        }

        document.querySelectorAll('.grecaptcha-badge').forEach(moveBadge);

        new MutationObserver(function(muts){
          muts.forEach(function(m){
            m.addedNodes && m.addedNodes.forEach(function(node){
              if (node.matches && node.matches('.grecaptcha-badge')) moveBadge(node);
              node.querySelectorAll && node.querySelectorAll('.grecaptcha-badge').forEach(moveBadge);
            });
          });
        }).observe(document.body,{childList:true,subtree:true});
      })();
    ";

    wp_add_inline_script('child-recaptcha-left', $js);
}
add_action('wp_enqueue_scripts', 'childtheme_move_recaptcha_to_left', 30);



/* ----------------------------------------------------
 * 6. Move Iubenda Consent Button to Bottom-Left
 * -------------------------------------------------- */
function move_iubenda_button_to_left() {
    ?>
    <style>
      button.iubenda-tp-btn[data-tp-float="bottom-right"] {
        right: auto !important;
        left: 20px !important;
        bottom: 90px !important;
        z-index: 2147483647 !important;
      }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function moveIubendaButton() {
            var btn = document.querySelector('button.iubenda-tp-btn[data-tp-float="bottom-right"]');
            if (btn) {
                btn.style.right = 'auto';
                btn.style.left = '20px';
                btn.style.bottom = '90px';
                btn.style.zIndex = '2147483647';
                btn.setAttribute('data-tp-float', 'bottom-left');
                return true;
            }
            return false;
        }

        var attempts = 0;
        var interval = setInterval(function() {
            if (moveIubendaButton() || attempts++ > 20) {
                clearInterval(interval);
            }
        }, 500);
    });
    </script>
    <?php
}
add_action('wp_footer', 'move_iubenda_button_to_left', 100);
