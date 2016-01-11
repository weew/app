<?php

namespace Weew\App\Events;

use Weew\Eventer\Event;
use Weew\Kernel\IKernel;

class KernelBootedEvent extends Event {
    /**
     * @var IKernel
     */
    private $kernel;

    /**
     * KernelBootedEvent constructor.
     *
     * @param IKernel $kernel
     */
    public function __construct(IKernel $kernel) {
        $this->kernel = $kernel;
    }

    public function getKernel() {
        return $this->kernel;
    }
}
