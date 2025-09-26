/**
Enqueue Google Analytics 4 code in the site header.
*/
function mychildtheme_add_google_analytics() {
// Only load GA4 if user is not logged in as an admin (optional best practice).
if ( ! current_user_can( 'manage_options' ) ) {
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
}
add_action( 'wp_head', 'mychildtheme_add_google_analytics' );/**
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
