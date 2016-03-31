<?php

namespace Tests\Weew\App\Util;

use PHPUnit_Framework_ExpectationFailedException;
use PHPUnit_Framework_TestCase;
use Weew\Eventer\Eventer;

class EventerTesterTest extends PHPUnit_Framework_TestCase {
    public function test_assert_with_event() {
        $eventer = new Eventer();
        $tester = new EventerTester($eventer);

        $tester->setExpectedEvents(['foo.bar']);
        $eventer->dispatch('foo.bar');
        $tester->assert();
    }

    public function test_assert_without_event() {
        $eventer = new Eventer();
        $tester = new EventerTester($eventer);

        $tester->setExpectedEvents(['foo.bar']);
        $exception = null;

        try {
            $tester->assert();
        } catch (PHPUnit_Framework_ExpectationFailedException $ex) {
            $exception = $ex;
        }

        $this->assertTrue($exception instanceof PHPUnit_Framework_ExpectationFailedException);
    }
}
