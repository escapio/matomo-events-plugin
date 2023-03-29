<?php
namespace Piwik\Plugins\EventsExporter\tests\System;

use Piwik\Plugins\EventsExporter\tests\Fixtures\EventsFixture;
use Piwik\Tests\Framework\TestCase\SystemTestCase;

/**
 * Testing Event Exporter API
 *
 * @group EventsExporter
 * @group Plugins
 */
class ApiTest extends SystemTestCase {
	/**
	 * @var EventsFixture
	 */
	public static $fixture = null; // initialized below class definition

	/** @dataProvider getApiForTesting */
	public function testApi($api, $params) {
		$this->runApiTests($api, $params);
	}

	public function getApiForTesting() {
		$date_time = self::$fixture::DATE_TIME;
		$id_site = self::$fixture::SITE_ID;

		yield "only mandatory params, one day" => [
			"api" => [
				'EventsExporter.getEvents',
			],
			"params" => [
				"testSuffix" => '_mandatory-params-one-day',
				'idSite' => $id_site,
				'otherRequestParameters' => [
					'dates' => sprintf(
						"%s,%s",
						(new \DateTimeImmutable($date_time))->format('Y-m-d'),
						(new \DateTimeImmutable($date_time))->format('Y-m-d')
					),
				],
			],
		];
		yield "only mandatory params, multiple days" => [
			"api" => [
				'EventsExporter.getEvents',
			],
			"params" => [
				"testSuffix" => '_mandatory-params-multiple-days',
				'idSite' => $id_site,
				'otherRequestParameters' => [
					'dates' => sprintf(
						"%s,%s",
						(new \DateTimeImmutable($date_time))->format('Y-m-d'),
						(new \DateTimeImmutable($date_time))->modify('+1 day')->format('Y-m-d')
					),
				],
			],
		];
		yield "filter by category" => [
			"api" => [
				'EventsExporter.getEvents',
			],
			"params" => [
				"testSuffix" => '_filter-by-category',
				'idSite' => $id_site,
				'otherRequestParameters' => [
					'dates' => sprintf(
						"%s,%s",
						(new \DateTimeImmutable($date_time))->format('Y-m-d'),
						(new \DateTimeImmutable($date_time))->format('Y-m-d')
					),
					'category' => 'Event category 2',
				],
			],
		];
		yield "filter by action" => [
			"api" => [
				'EventsExporter.getEvents',
			],
			"params" => [
				"testSuffix" => '_filter-by-action',
				'idSite' => $id_site,
				'otherRequestParameters' => [
					'dates' => sprintf(
						"%s,%s",
						(new \DateTimeImmutable($date_time))->format('Y-m-d'),
						(new \DateTimeImmutable($date_time))->format('Y-m-d')
					),
					'action_name' => 'Event action 2',
				],
			],
		];
		yield "filter by action_pattern" => [
			"api" => [
				'EventsExporter.getEvents',
			],
			"params" => [
				"testSuffix" => '_filter-by-action-pattern',
				'idSite' => $id_site,
				'otherRequestParameters' => [
					'dates' => sprintf(
						"%s,%s",
						(new \DateTimeImmutable($date_time))->format('Y-m-d'),
						(new \DateTimeImmutable($date_time))->format('Y-m-d')
					),
					'action_pattern' => 'action 2$',
				],
			],
		];
		yield "filter by lang_id" => [
			"api" => [
				'EventsExporter.getEvents',
			],
			"params" => [
				"testSuffix" => '_filter-by-lang-id',
				'idSite' => $id_site,
				'otherRequestParameters' => [
					'dates' => sprintf(
						"%s,%s",
						(new \DateTimeImmutable($date_time))->format('Y-m-d'),
						(new \DateTimeImmutable($date_time))->format('Y-m-d')
					),
					'lang_id' => 'ko',
				],
			],
		];
		yield "order by event names" => [
			"api" => [
				'EventsExporter.getEvents',
			],
			"params" => [
				"testSuffix" => '_order-by-event-names',
				'idSite' => $id_site,
				'otherRequestParameters' => [
					'dates' => sprintf(
						"%s,%s",
						(new \DateTimeImmutable($date_time))->modify('-1 day')->format('Y-m-d'),
						(new \DateTimeImmutable($date_time))->modify('+1 day')->format('Y-m-d')
					),
					'order_by_names' => '1',
				],
			],
		];
	}

	public static function getOutputPrefix() {
		return 'api';
	}

	public static function getPathToTestDirectory() {
		return dirname(__FILE__);
	}
}

ApiTest::$fixture = new EventsFixture();
