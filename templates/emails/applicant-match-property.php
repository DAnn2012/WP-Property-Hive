<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="margin-bottom:10px;">
	<tr>
		<td width="20%"><?php 
			$image = $property->get_main_photo_src();
			if ($image !== FALSE)
			{
		?>
		<a href="<?php echo get_the_permalink( $property->id ); ?>"><img src="<?php echo $image; ?>" alt="<?php echo get_the_title( $property->id ); ?>"></a>
		<?php
			}
		?></td>
		<td>
			<h2><a href="<?php echo get_the_permalink( $property->id ); ?>"><?php echo get_the_title( $property->id ); ?></a></h2>
			<p><strong><?php echo $property->get_formatted_price(); ?></strong> | <?php echo $property->bedrooms . ' bed ' . $property->get_property_type(); ?> | <?php echo $property->get_availability(); ?></p>
			<p><?php echo $property->post_excerpt; ?></p>
		</td>
	</tr>
</table>