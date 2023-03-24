<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\EscapioEventsExporter\tests\Integration;

use Piwik\Plugins\EscapioEventsExporter\API;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group CustomDimensions
 * @group ApiTest
 * @group Plugins
 */
class ApiTest extends IntegrationTestCase {
	/**
	 * @var API
	 */
	private $api;

	public function setUp() : void {
		parent::setUp();

		Fixture::createWebsite('2000-01-01 00:00:00');
		$this->api = API::getInstance();
	}

	/** @dataProvider getValidationTestData */
	public function testValidation($id_site, $dates, $expected_error) {
		$this->expectException(\Exception::class);
		$this->expectExceptionMessage($expected_error);
		$this->api->getEvents($id_site, $dates);
	}

	public static function getValidationTestData() : iterable {
		yield "id_site: invalid data type" => [
			'id_side' => 'no integer',
			'dates' => '2023-01-01',
			'expected_error' => 'An unexpected website was found in the request',
		];
		yield "id_site: unknown id" => [
			'id_side' => '23',
			'dates' => '2023-01-01',
			'expected_error' => 'An unexpected website was found in the request',
		];
		yield "dates: invalid date range format" => [
			'id_side' => '1',
			'dates' => '2023-01-01',
			'expected_error' => 'Invalid date range format. Should be \'YYYY-MM-DD,YYYY-MM-DD\'',
		];
	}
}
