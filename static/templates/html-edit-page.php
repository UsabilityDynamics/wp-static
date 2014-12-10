<?php
  /**
   * 
   */
?>

<?php if( !empty( $data[ 'msg' ] ) ) : ?>
  <div class="updated"><p><strong><?php echo $data[ 'msg' ]; ?></strong></p></div>
<?php endif; ?>
  
<div class="wrap">
  <h2><?php _e( 'Script Editor', get_wp_static( 'domain' ) ); ?><span class="ajax-message"></span></h2>
  <form action="themes.php?page=theme-page-static" method="post" id="static-html-form">
    <?php wp_nonce_field( 'update_static_html', 'update_static_html_nonce' ); ?>
    <div class="metabox-holder has-right-sidebar">

      <div class="inner-sidebar">
        <?php do_meta_boxes( get_current_screen()->id, 'side', $data ); ?>
      </div>

      <div id="post-body">
        <div id="post-body-content">
          <div id="global-editor-shell" class="wp-static-editor-shell">
            <textarea id="static-html" class="wp-editor-area" data-editor-status="not-ready" name="content"><?php echo $data[ 'post_content' ]; ?></textarea>
            <label for="static-html"></label>
          </div>
        </div>
      </div>

    </div>
  </form>
</div>