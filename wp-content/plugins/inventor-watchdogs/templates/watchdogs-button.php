<?php if ( $type == INVENTOR_WATCHDOG_TYPE_SEARCH_QUERY ): ?>
    <?php $connect_with = strpos( $_SERVER['REQUEST_URI'], '?' ) === false ? '?' : '&'; ?>
    <?php if ( Inventor_Watchdogs_Logic::is_my_watchdog( $type, $_GET ) ) : ?>
        <a class="inventor-watchdog-btn" href="<?php echo $_SERVER["REQUEST_URI"] . $connect_with ?>watchdog-remove=true">
            <i class="fa fa-eye-slash"></i> <?php echo __( 'Unwatch this search query', 'inventor-watchdogs' ); ?>
        </a><!-- /.inventor-watchdog-btn -->
    <?php else: ?>
        <a class="inventor-watchdog-btn" href="<?php echo $_SERVER["REQUEST_URI"] . $connect_with ?>watchdog-add=true">
            <i class="fa fa-eye"></i> <?php echo __( 'Watch this search query', 'inventor-watchdogs' ); ?>
        </a><!-- /.inventor-watchdog-btn -->
    <?php endif ; ?>
<?php endif ; ?>
