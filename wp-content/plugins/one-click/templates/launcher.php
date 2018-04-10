<div class="wrap">
	<h2><?php echo __( 'One Click Installation', 'one-click' ); ?></h2>

	<div class="one-click-wrapper">
		<?php if ( false == One_Click_Launcher::exports_exists() ) : ?>
			<div class="one-click-state-title-wrapper no-margin">
				<div class="one-click-state-icon">
					<i class="dashicons dashicons-warning"></i>
				</div><!-- /.one-click-state-icon -->

				<h1 class="one-click-state-title">
					<span><?php echo __( 'Exports folder in your current theme does not exists.', 'one-click' ); ?></span>
				</h1><!-- /.one-click-state-title -->
			</div><!-- /.one-click-state-title-wrapper -->
		<?php elseif ( true === One_Click_Launcher::is_completed() ) : ?>
			<div class="one-click-state-title-wrapper">
				<div class="one-click-state-icon">
					<i class="dashicons dashicons-thumbs-up"></i>
				</div><!-- /.one-click-state-icon -->

				<h1 class="one-click-state-title">
					<span><?php echo __( 'All available steps has been successfully processed.', 'one-click' ); ?></span>
				</h1><!-- /.one-click-state-title -->
			</div><!-- /.one-click-state-title-wrapper -->
		<?php elseif ( 0 === count( One_Click_Launcher::get_missing_plugins() ) ) : ?>

			<?php if ( ! One_Click_Launcher::is_correct_configuration() ) : ?>
				<p><?php echo __( 'For proper demo content import it is <u>recommended</u> to set following PHP settings:', 'one-click' ); ?></p>

				<table class="one-click-settings">
					<thead>
					<tr>
						<th><?php echo __( 'Setting', 'one-click' ); ?></th>
						<th><?php echo __( 'Required value', 'one-click' ); ?></th>
						<th><?php echo __( 'Current value', 'one-click' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach( One_Click::$required_settings as $setting => $info ): ?>
						<?php $status = One_Click_Launcher::is_setting_fulfilled( $setting ) ? 'OK' : 'FAILED'; ?>
						<tr>
							<td><a href="<?php echo $info['docs']; ?>" target="_blank"><?php echo $setting; ?></a></td>
							<td><?php echo $info['min']; ?></td>
							<td class="status-<?php echo strtolower( $status ); ?>"><?php echo One_Click_Launcher::get_setting_value( $setting ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<br>
				<small><?php echo __( 'These values can be reverted back to defaults after successful import.', 'one-click' ); ?></small>
				<br>
				<small><?php echo __( 'They are <strong>NOT required</strong> for running website.', 'one-click' ); ?></small>

				<br><br>
			<?php endif; ?>

			<a class="one-click-run">
				<strong><i class="dashicons dashicons-controls-play"></i></strong>
				<?php if ( true === One_Click_Launcher::is_missing() ) : ?>
					<span><?php echo __( 'Run Missing Steps', 'one-click' ); ?></span>
				<?php else: ?>
					<span><?php echo __( 'Launch Installation', 'one-click' ); ?></span>
				<?php endif; ?>
			</a>
		<?php else : ?>
			<div class="one-click-state-title-wrapper">
				<div class="one-click-state-icon">
					<i class="dashicons dashicons-warning"></i>
				</div><!-- /.one-click-state-icon -->

				<h1 class="one-click-state-title">
					<span><?php echo __( 'Missing Plugins Detected', 'one-click' ); ?></span>
				</h1><!-- /.one-click-state-title -->
			</div><!-- /.one-click-state-title-wrapper -->

			<div class="one-click-missing-plugins-content">
				<p>
					<?php echo __( 'You are missing plugins which are required for running one click installation. Please install plugins listed below.', 'one-click' ); ?>
				</p>

				<?php $plugins = One_Click_Launcher::get_missing_plugins(); ?>
				<?php $plugins_count = count( $plugins ); ?>
				<?php $index = 0; ?>

				<?php if ( 0 !== $plugins_count ) : ?>
					<ul class="one-click-missing-plugins-list">
						<?php foreach( $plugins as $plugin ): ?>
							<li>
								<a href="https://wordpress.org/plugins/<?php echo esc_attr( $plugin['slug'] ); ?>">
									<?php echo esc_attr( $plugin['name'] ); ?>
								</a><?php if ( $index + 1 != $plugins_count ) : ?>, <?php endif; ?>
							</li>
							<?php $index++; ?>
						<?php endforeach; ?>
					</ul><!-- /.one-click-missing-plugins-list -->
				<?php endif; ?>
			</div><!-- /.one-click-missing-plugins-content -->

			<hr>
		<?php endif; ?>

		<?php $steps = One_Click::get_imports(); ?>
		<?php if ( ! empty( $steps ) ): ?>
			<ul class="one-click-steps">
				<?php $index = 1; ?>
				<?php foreach ( $steps as $step ) : ?>
					<?php if ( One_Click_Launcher::import_found( $step['file' ] ) ) : ?>
						<?php if ( '1' == get_option( 'one_click_process_step_' . $step['id'], false ) ) : ?>
							<li class="one-click-step completed">
						<?php else: ?>
							<li class="one-click-step" data-action="<?php echo $step['action']; ?>">
						<?php endif; ?>
							<div class="one-click-step-counter"><?php echo esc_attr( $index ); ?></div>

							<div class="one-click-step-content">
								<div class="one-click-step-title"><?php echo $step['title']; ?></div>

								<div class="one-click-step-description"><?php echo $step['description']; ?></div><!-- /.one-click-step-description -->
							</div><!-- /.one-click-step-content -->
						</li><!-- /.one-click-step -->

						<?php $index++; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul><!-- /.one-click-steps -->
		<?php endif; ?>
	</div><!-- /.one-click-wrapper -->
</div><!-- /.wrap -->