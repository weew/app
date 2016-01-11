<?php

namespace Weew\App\Events;

use Weew\Eventer\Event;
use Weew\Kernel\IKernel;

class KernelInitializedEvent extends Event {
    /**
     * @var IKernel
     */
    private $kernel;

    /**
     * KernelInitializedEvent constructor.
     *
     * @param IKernel $kernel
     */
    public function __construct(IKernel $kernel) {
        $this->kernel = $kernel;
    }

    /**
     * @return IKernel
     */
    public function getKernel() {
        return $this->kernel;
    }
}
