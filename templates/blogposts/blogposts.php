<div class="peepso ps-page-profile">
	<?php PeepSoTemplate::exec_template('general', 'navbar'); ?>

	<?php PeepSoTemplate::exec_template('profile', 'focus', array('current'=>'blogposts')); ?>

	<section id="mainbody" class="ps-page-unstyled">
		<section id="component" role="article" class="clearfix">
			<!--<h4 class="ps-page-title">
                <?php echo __('Blog Posts', 'peepsoblogposts'); ?>
            </h4>
-->
			<div class="ps-page-filters">
				<select class="ps-select ps-full ps-js-blogposts-sortby ps-js-blogposts-sortby--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>"
						onchange="peepso.blogposts.sortby(this.value);">
					<option value="desc"><?php _e('Newest first', 'peepso');?></option>
					<option value="asc"><?php _e('Oldest first', 'peepso');?></option>
				</select>
			</div>

			<div class="clearfix mb-20"></div>
			<div class="ps-gallery ps-js-blogposts ps-js-blogposts--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>"></div>
			<div class="ps-gallery-scroll ps-js-blogposts-triggerscroll ps-js-blogposts-triggerscroll--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>">&nbsp;</div>

		</section><!--end component-->
	</section><!--end mainbody-->
</div><!--end row-->
<?php

PeepSoTemplate::exec_template('activity','dialogs');