<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\EscapioEventsExporter\tests\Fixtures;

use Piwik\Tests\Framework\Fixture;

class EventsFixture extends Fixture {
	public const DATE_TIME = '2023-01-01 11:22:33';

	public const SITE_ID = 1;

	/**
	 * @var \DateTimeImmutable
	 */
	private $date_time;

	public function setUp() : void {
		$this->date_time = new \DateTimeImmutable(self::DATE_TIME);
		if (!self::siteCreated($idSite = 1)) {
			self::createWebsite($this->date_time->format(\DateTimeImmutable::ATOM));
		}

		$this->trackVisits();
	}

	private function trackVisits() {
		$t = self::getTracker(self::SITE_ID, $this->date_time->getTimestamp(), $defaultInit = true);
		$t->setTokenAuth(self::getTokenAuth());
		$t->enableBulkTracking();

		$events_per_user = [
			'user_1' => [
				'lang_id' => 'fi',
				'events' => [
					[
						'date_time' => $this->date_time,
						'category' => 'Event category 1',
						'action' => 'Event action 1',
						'name' => 'Event name 1',
						'count' => 1,
					],
					[
						'date_time' => $this->date_time,
						'category' => 'Event category 1',
						'action' => 'Event action 2',
						'name' => 'Event name 2',
						'count' => 2,
					],
					[
						'date_time' => $this->date_time->modify('+1 day'),
						'category' => 'Event category 1',
						'action' => 'Event action 1',
						'name' => 'Event name 1',
						'count' => 4,
					],
				],
			],
			'user_2' => [
				'lang_id' => 'ko',
				'events' => [
					[
						'date_time' => $this->date_time,
						'category' => 'Event category 1',
						'action' => 'Event action 1',
						'name' => 'Event name 1',
						'count' => 8,
					],
					[
						'date_time' => $this->date_time,
						'category' => 'Event category 2',
						'action' => 'Event action 1',
						'name' => 'Event name 3',
						'count' => 16,
					],
					[
						'date_time' => $this->date_time->modify('-1 day'),
						'category' => 'Event category 2',
						'action' => 'Event action 1',
						'name' => 'Event name 4',
						'count' => 32,
					],
				],
			],
		];

		foreach ($events_per_user as $user_id => $data) {
			$t->setUserId($user_id);
			$t->setForceNewVisit();
			$lang_id = $data['lang_id'];
			$t->setUrl("https://{$lang_id}.example.org/some/page");

			foreach ($data['events'] as $event) {
				$t->setForceVisitDateTime($event['date_time']->getTimestamp());
				foreach (range(1, $event['count']) as $_) {
					$t->doTrackEvent($event['category'], $event['action'], $event['name']);
				}
			}
		}
		self::checkBulkTrackingResponse($t->doBulkTrack());
	}
}
