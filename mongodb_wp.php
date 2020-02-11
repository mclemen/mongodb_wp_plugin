<?php
/**
 *
 * @package SRDatabasePlugin
 *
*/
/**
 * Plugin Name: Sportradar Database
 * Description: Fetch data from MongodDB
 * Author: Michael Clemen
 * Version: 1.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: SR-Database

THis is a free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
This is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with SR-DB. 

*/

defined( 'ABSPATH' ) or die('Nothing to see here!');


function display_db() {
	$manager = new MongoDB\Driver\Manager("mongodb://server_ip:27017");
	
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$perPage = isset($_GET['per-page']) && $_GET['per-page'] <= 50 ? (int)$_GET['per-page'] : 319;

	$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;
	$options = [
		"limit" => $perPage,
		"skip" => $start
	];

	$firstPage = 1;
	$nextPage = $page + 1;
	$previousPage = $page - 1;

	$collection = 'dbname.collections';
	$query = new MongoDB\Driver\Query([], $options);
	$cursor = $manager->executeQuery( $collection, $query);

	$stats = new MongoDB\Driver\Command(["count" => "collections"]);
	$res = $manager->executeCommand("dbname", $stats);
	$total = ($res->toArray()[0]->n);
	$pages = $total / $perPage;

	?>
	<table class="table table-bordered">
		<tbody><?php
		foreach ($cursor as $row) {
			echo "<tr>";
			if( !empty( $row->league ) ) {
				echo "<td style='width:65%;'><strong>League: <i>".implode($row->league)."</i></strong></td>";
			}
			echo "</tr>";
			echo "<tr>";
			if( !empty( $row->team ) ) {
				echo "<tr><td style='width:50%;'>Teams: ".implode(", ", $row->team)."</td>";
			}
			if( !empty( $row->totalscore ) ) {
				echo "<td>Total Score: ".implode($row->totalscore)."</td></tr>";
			}
			echo "</tr>";
		}
		?>
	</tbody>
</table>
<nav aria-label="Page navigation example">
			<ul class="pagination">
				<?php if ($page > 1): ?>
				<li class="prev page-item"><a class="page-link" href="?page=<?php echo $page-1 ?>">Prev</a></li>
				<?php endif; ?>

				<?php if ($page-2 > 0): ?><li class="page-item"><a class="page-link" href="?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
				<?php if ($page-1 > 0): ?><li class="page-item"><a class="page-link" href="?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

				<li class="currentpage page-item active"><a class="page-link" href="?page=<?php echo $page ?>"><?php echo $page ?></a></li>

				<?php if ($page+1 < $pages +1): ?><li class="page-item"><a  class="page-link" href="?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
	<?php if ($page+2 < $pages +1): ?><li class="page-item"><a  class="page-link" href="?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

				<?php if ($page < $pages): ?>
				<li class="next page-item"><a class="page-link" href="?page=<?php echo $page+1 ?>">Next</a></li>
				<?php endif; ?>

				
			</ul>
		</nav>
<?php

}

add_shortcode('srdb', 'display_db');
