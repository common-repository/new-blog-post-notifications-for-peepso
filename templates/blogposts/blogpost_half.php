<div class="ps-blogposts-container ps-blogposts-container-half">
	<div class="ps-blogposts-container-inside ps-blogposts-container-half-inside">

		<?php if('top' == $image_position) echo $image;?>

		<div class="ps-blogposts-main ps-blogposts-half-main">

			<h2>
				<a href="<?php echo get_permalink($post);?>">
					<?php echo get_the_title($post);?>
				</a>
			</h2>

			<?php if('top' != $image_position) :?>
				<div style="float: <?php echo $image_position;?>;">
					<?php echo $image; ?>
				</div>
			<?php endif; ?>

			<?php if('top' == $date_position) : ?>
			<div class="ps-blogposts-date">
				<?php echo get_the_date('',$post);?>
			</div>
			<?php endif;?>

			<?php if(FALSE !== $post_content):?>
			<div class="ps-blogposts-content">
				<?php echo $post_content; ?>
			</div>
			<?php endif; ?>

			<?php if('bottom' == $date_position) : ?>
				<div class="ps-blogposts-date">
					<?php echo get_the_date('',$post);?>
				</div>
			<?php endif;?>

		</div>
	</div>
</div>