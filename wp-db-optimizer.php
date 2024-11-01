<?php
/*
Plugin Name: WP DB Optimizer
Plugin URI: http://www.lunasoft.com.mx/wp-plugins/wp-db-optimizer/
Description: Optimize your database tables without phpMyAdmin in one clic for high performance.
Version: 1.0
Author: vicmx
Author URI: http://www.lunasoft.com.mx
*/


# ---------------------------------- #
# prevent file from being accessed directly
# ---------------------------------- #
if ('wp-db-optimizer.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not access this file directly. Thanks!');

# ---------------------------------- #
# Create Text Domain For Translations
# ---------------------------------- #
add_action('init', 'wp_db_optimizer_textdomain');
function wp_db_optimizer_textdomain() {
	if (function_exists('load_plugin_textdomain')) {
		load_plugin_textdomain('wp-db-optimizer', false,'wp-db-optimizer');
	}
}

# ---------------------------------- #
# Administration menu
# ---------------------------------- #
add_action('admin_menu', 'dbmx_opt');
function dbmx_opt(){
	if (function_exists('add_options_page')) {
		add_options_page('Optimize Database', 'Optimize Database', 8, 'wp-db-optimizer/wp-db-optimizer.php', 'dbmx_options_page');
	}
}

# ---------------------------------- #
# Options Page
# ---------------------------------- #
function dbmx_options_page(){
?>
<div id="message" class="updated fade"><p><?php echo __('Optimized Database!','wp-db-optimizer'); ?></p></div>

<div class="wrap">
<h2><?php echo __('WP DB Optimizer','wp-db-optimizer'); ?></h2>

<h3><?php echo __('Optimize database:','wp-db-optimizer'); ?> '<?=DB_NAME?>'</h3>
<p><?php echo __('Optimized all the tables found in the database.','wp-db-optimizer')?></p>
<table class="widefat fixed" cellspacing="0">
<thead>
	<tr>
	<th scope="col"><?php echo __('Table','wp-db-optimizer'); ?></th>
	<th scope="col"><?php echo __('Size','wp-db-optimizer')?></th>
	<th scope="col"><?php echo __('Status','wp-db-optimizer'); ?></th>
	<th scope="col"><?php echo __('Space Saved','wp-db-optimizer'); ?></th>
	</tr>
</thead>
<tfoot>
	<tr>
	<th scope="col"><?php echo __('Table','wp-db-optimizer'); ?></th>
	<th scope="col"><?php echo __('Size','wp-db-optimizer')?></th>
	<th scope="col"><?php echo __('Status','wp-db-optimizer'); ?></th>
	<th scope="col"><?php echo __('Space Saved','wp-db-optimizer'); ?></th>
	</tr>
</tfoot>
<tbody id="the-list">
<?php
$alternate = ' class="alternate"';
	$db_clean = DB_NAME;
	$tot_data = 0;
	$tot_idx = 0;
	$tot_all = 0;
	$local_query = 'SHOW TABLE STATUS FROM '. DB_NAME;
	$result = mysql_query($local_query);
	if (mysql_num_rows($result)){
		while ($row = mysql_fetch_array($result))
		{
			$tot_data = $row['Data_length'];
			$tot_idx  = $row['Index_length'];
			$total = $tot_data + $tot_idx;
			$total = $total / 1024 ;
			$total = round ($total,3);
			$gain= $row['Data_free'];
			$gain = $gain / 1024 ;
			$total_gain += $gain;
			$gain = round ($gain,3);
			$local_query = 'OPTIMIZE TABLE '.$row[0];
			$resultat  = mysql_query($local_query);
			if ($gain == 0){
				echo "<tr". $alternate .">
					<td class='column-name'>". $row[0] ."</td>
					<td class='column-name'>". $total ." Kb"."</td>
					<td class='column-name'>" .  __('Already Optimized','wp-db-optimizer') . "</td>
					<td class='column-name'>0 Kb</td>
					</tr>\n";
			} else
			{
				echo "<tr". $alternate .">
					<td class='column-name'>". $row[0] ."</td>
					<td class='column-name'>". $total ." Kb"."</td>
					<td class='column-name' style=\"color: #ff0000;\">" .  __('Optimized','wp-db-optimizer') . "</td>
					<td class='column-name'>". $gain ." Kb</td>
					</tr>\n";
			}
			$alternate = ( empty( $alternate ) ) ? ' class="alternate"' : '';
		}
	}
?>
</tbody>
</table>

<?php $total_gain = round ($total_gain,3); ?>
<h3><?php echo __('Optimization Results:','wp-db-optimizer'); ?></h3>
<p><?php echo __('Total Space Saved:','wp-db-optimizer'); ?> <?=$total_gain?> Kb</p>
</div>
<?php
}
?>