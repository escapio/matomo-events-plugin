<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\EscapioEventsExporter;

use Piwik\{Common, Db, Piwik, Site};
use Piwik\Period\Range;

class API extends \Piwik\Plugin\API {

	public function getEvents(
		$idSite,
		$dates,
		$category = null,
		$action_name = null,
		$action_pattern = null,
		$order_by_names = false,
		$lang_id = null
	) {
		Site::getSite($idSite); // Check if site exists.
		Piwik::checkUserHasViewAccess($idSite);

		$range = Range::parseDateRange($dates);
		if (!$range) {
			throw new \Exception("Invalid date range format. Should be 'YYYY-MM-DD,YYYY-MM-DD'");
		}
		[$_, $date_start, $date_end] = $range;
		$date_end = (new \DateTimeImmutable($date_end))
			->modify('+1 day midnight')
			->format('Y-m-d');

		$database = Db::get();
		$log_link_visit_action_table = Common::prefixTable("log_link_visit_action");
		$log_action_table = Common::prefixTable("log_action");

		$additional_joins = $lang_id
			? "LEFT JOIN {$log_action_table} AS action_url ON mlva.idaction_url = action_url.idaction"
			: "";

		$additional_filters = "";
		$additional_params = [];
		if ($category) {
			$additional_filters .= " AND category.name = ?";
			$additional_params[] = $category;
		}
		if ($action_name) {
			$additional_filters .= " AND action.name = ?";
			$additional_params[] = $action_name;
		}
		if ($action_pattern) {
			$additional_filters .= " AND action.name RLIKE ?";
			$additional_params[] = $action_pattern;
		}
		if ($lang_id) {
			$additional_filters .= " AND action_url.name RLIKE CONCAT('^', ?)";
			$additional_params[] = $lang_id;
		}

		$additional_ordering =
			$order_by_names ? "category.name, action.name, label.name, " : "";

		$query = "
			SELECT
			    
				category.name AS `category`,
				action.name AS `action`,
				label.name AS `label`,
				count(*) AS `count`
			FROM {$log_link_visit_action_table} AS mlva
				LEFT JOIN {$log_action_table} AS label ON mlva.idaction_name = label.idaction
				LEFT JOIN {$log_action_table} AS action ON mlva.idaction_event_action = action.idaction
				LEFT JOIN {$log_action_table} AS category ON mlva.idaction_event_category = category.idaction
				{$additional_joins}
			WHERE
				mlva.idsite = ?
				AND mlva.server_time >= ?
				AND mlva.server_time < ?
				{$additional_filters}
			GROUP BY category.idaction, action.idaction, label.name
			ORDER BY {$additional_ordering} `count` DESC
		";

		return $database->fetchAll(
			$query,
			array_merge(
				[$idSite, $date_start, $date_end],
				$additional_params
			)
		);
	}
}
