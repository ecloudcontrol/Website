<?php
/**
 * Child theme functions
 */

/**
 * Enqueue child stylesheet + inject grid CSS
 */
function theme_enqueue_styles() {
  $ver  = wp_get_theme()->get('Version');
  $deps = array(); // If Avada exposes a main handle, you can depend on it.

  // Enqueue child theme style.css
  wp_enqueue_style(
    'child-style',
    get_stylesheet_uri(), // /style.css
    $deps,
    $ver
  );

  // Inject responsive grid + title clamp CSS
  $inline_css = <<<CSS
/* Grid layout for shortcode cards */
.pages-grid {
  display: grid;
  gap: 28px;
  grid-template-columns: 1fr;              /* mobile */
}
@media (min-width: 640px) {                 /* tablet */
  .pages-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
}
@media (min-width: 1024px) {                /* desktop */
  .pages-grid { grid-template-columns: repeat(3, minmax(0,1fr)); }
}
/* Optional: more columns on very wide screens */
/*
@media (min-width: 1400px) {
  .pages-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }
}
*/

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
  aspect-ratio: 16 / 9;   /* uniform card height */
  object-fit: cover;
  border-radius: 8px;
  display: block;
  margin-bottom: 10px;
}

/* Clamp long titles to 2 lines */
.pages-grid__item h3 {
  margin: 0 0 10px;
  line-height: 1.3;
  font-size: 1.25rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: calc(1.3em * 2); /* reserve height equal to 2 lines */
}
.pages-grid__item h3 a { text-decoration: none; }
.pages-grid__item h3 a:hover { text-decoration: underline; }

.pages-grid__item p { margin: 0; }
CSS;

  wp_add_inline_style('child-style', $inline_css);
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles', 20 );

/**
 * Load child theme textdomain (Avada)
 */
function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

/**
 * Google Analytics 4 in <head>
 */
function mychildtheme_add_google_analytics() {
  // Skip GA for admins to avoid skewed data.
  if ( current_user_can( 'manage_options' ) ) {
    return;
  }
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
add_action( 'wp_head', 'mychildtheme_add_google_analytics' );

/**
 * Shortcode: [pages_by_page_type value="AI Agents" per_page="12"]
 * Lists Pages where ACF text field "page_type" equals the given value.
 * If value is omitted, shows all pages where page_type is non-empty.
 */
add_shortcode( 'pages_by_page_type', function( $atts ) {
  $atts = shortcode_atts( array(
    'value'    => '',   // e.g. "AI Agents"
    'per_page' => 12,
  ), $atts, 'pages_by_page_type' );

  $value    = sanitize_text_field( $atts['value'] );
  $per_page = max( 1, intval( $atts['per_page'] ) );

  // Pagination that works on normal Pages
  $paged = max( 1, get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 1 );

  $meta_query = array();
  if ( $value !== '' ) {
    $meta_query[] = array(
      'key'     => 'page_type',
      'value'   => $value,
      'compare' => '=',
    );
  } else {
    // If no value provided, list any page that has a non-empty page_type
    $meta_query[] = array(
      'key'     => 'page_type',
      'value'   => '',
      'compare' => '!=',
    );
  }

  $q = new WP_Query( array(
    'post_type'      => 'page',
    'posts_per_page' => $per_page,
    'paged'          => $paged,
    'meta_query'     => $meta_query,
    'orderby'        => 'menu_order title',
    'order'          => 'ASC',
  ) );

  ob_start();

  if ( $q->have_posts() ) {
    echo '<div class="pages-grid">';

    while ( $q->have_posts() ) { $q->the_post();

      echo '<article class="pages-grid__item">';

        // Linked Title (clamped to 2 lines via CSS)
        echo '<h3><a href="' . esc_url( get_permalink() ) . '">'
             . esc_html( get_the_title() )
             . '</a></h3>';

        // Linked Thumbnail (if any)
        if ( has_post_thumbnail() ) {
          echo '<a href="' . esc_url( get_permalink() ) . '">'
               . get_the_post_thumbnail( get_the_ID(), 'medium' )
               . '</a>';
        }

        // Excerpt
        the_excerpt();

      echo '</article>';
    }

    echo '</div>';

    // Pagination
    echo paginate_links( array(
      'total'   => $q->max_num_pages,
      'current' => $paged,
    ) );

    wp_reset_postdata();
  } else {
    echo '<p>No pages found.</p>';
  }

  return ob_get_clean();
} );
/**
 * Move reCAPTCHA badge to the bottom-left corner of the screen
 * without affecting chatbot placement.
 */
function childtheme_move_recaptcha_to_left() {
    // Inline CSS to reposition badge
    $css = "
    /* Move reCAPTCHA badge to bottom-left corner */
    .grecaptcha-badge,
    .grecaptcha-badge iframe {
      position: fixed !important;
      left: 16px !important;      /* stick to left side */
      right: auto !important;     /* disable default right position */
      bottom: 16px !important;    /* keep near bottom edge */
      z-index: 2147483647 !important; /* ensure visible above other elements */
      pointer-events: auto !important;
    }

    /* Optional: adjust for small screens */
    @media (max-width: 600px) {
      .grecaptcha-badge {
        left: 16px !important;
        bottom: 78px !important;  /* raise slightly if needed on mobile */
      }
    }
    ";

    // Attach inline CSS to the child theme stylesheet
    if ( wp_style_is( 'child-style', 'enqueued' ) || wp_style_is( 'child-style', 'registered' ) ) {
        wp_add_inline_style( 'child-style', $css );
    } else {
        echo "<style type='text/css'>" . $css . "</style>";
    }

    // JS fallback: handle dynamically loaded reCAPTCHA
    wp_register_script( 'child-recaptcha-left', false, array(), null, true );
    wp_enqueue_script( 'child-recaptcha-left' );

    $js = "
    (function() {
      function moveBadge(el) {
        if (!el || !el.style) return;
        try {
          el.style.position = 'fixed';
          el.style.left = '16px';
          el.style.right = 'auto';
          el.style.bottom = '16px';
          el.style.zIndex = '2147483647';
          el.style.pointerEvents = 'auto';
        } catch(e) {}
      }

      // Apply to existing badges
      document.querySelectorAll('.grecaptcha-badge').forEach(moveBadge);

      // Observe for dynamically added badges
      var mo = new MutationObserver(function(muts) {
        muts.forEach(function(m) {
          m.addedNodes && m.addedNodes.forEach(function(node) {
            if (!(node instanceof HTMLElement)) return;
            if (node.matches && node.matches('.grecaptcha-badge')) moveBadge(node);
            node.querySelectorAll && node.querySelectorAll('.grecaptcha-badge').forEach(moveBadge);
          });
        });
      });

      mo.observe(document.body, { childList: true, subtree: true });
    })();
    ";

    wp_add_inline_script( 'child-recaptcha-left', $js );
}
add_action( 'wp_enqueue_scripts', 'childtheme_move_recaptcha_to_left', 30 );

// Move Iubenda consent button to bottom-left corner
function move_iubenda_button_to_left() {
    ?>
    <style id="iubenda-move-style">
      button.iubenda-tp-btn[data-tp-float="bottom-right"] {
        right: auto !important;
        left: 20px !important;
        bottom: 90px !important; /* adjust if overlapping with chatbot */
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
          btn.setAttribute('data-tp-float', 'bottom-left');
          btn.style.zIndex = '2147483647';
          return true;
        }
        return false;
      }

      // Try immediately
      if (!moveIubendaButton()) {
        // Retry a few times since iubenda loads async
        var attempts = 0;
        var interval = setInterval(function() {
          if (moveIubendaButton() || attempts++ > 20) clearInterval(interval);
        }, 500);
      }
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'move_iubenda_button_to_left', 100 );
