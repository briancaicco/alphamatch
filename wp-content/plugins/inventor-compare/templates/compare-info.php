<?php $config = Inventor_Compare_Logic::get_config(); ?>
<?php $permalink = empty( $config['page'] ) ? '#' : get_permalink( $config['page'] ); ?>

<div id="compare-info" class="<?php echo $count > 0 ? 'visible' : 'hidden'; ?>">
    <i class="fa fa-exchange"></i>
    <a href="<?php echo $permalink; ?>">
        <?php echo sprintf( _n( 'Compare %d item', 'Compare %d items', $count, 'inventor-compare' ), $count ); ?>
    </a>
</div><!-- /.compare-info -->