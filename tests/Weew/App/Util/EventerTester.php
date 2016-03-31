<?php

namespace Tests\Weew\App\Util;

use PHPUnit_Framework_ExpectationFailedException;
use Weew\Eventer\IEventer;

class EventerTester {
    /**
     * @var IEventer
     */
    private $eventer;

    /**
     * @var array
     */
    private $expectedEvents = [];

    /**
     * @var array
     */
    private $occurredEvents = [];

    /**
     * EventerTester constructor.
     *
     * @param IEventer $eventer
     */
    public function __construct(IEventer $eventer) {
        $this->eventer = $eventer;
    }

    /**
     * @param array $events
     */
    public function setExpectedEvents(array $events) {
        $this->occurredEvents = [];
        $this->expectedEvents = $events;
        $occurredEvents = &$this->occurredEvents;

        foreach ($events as $event) {
            $this->eventer->subscribe($event, function() use ($event, &$occurredEvents) {
                $occurredEvents[] = $event;
            });
        }
    }

    /**
     * @throws PHPUnit_Framework_ExpectationFailedException
     */
    public function assert() {
        if ($this->expectedEvents != $this->occurredEvents) {
            $message = s(
                'Failed asserting that events occurred in expected order:\n' .
                'Expected: \n    %s\n' .
                'Actual: \n    %s',
                implode(',\n    ', $this->expectedEvents),
                implode(', \n    ', $this->occurredEvents)
            );
            throw new PHPUnit_Framework_ExpectationFailedException($message);
        }
    }
}
