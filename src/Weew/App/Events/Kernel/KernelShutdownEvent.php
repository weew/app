<?php

namespace Weew\App\Events\Kernel;

use Weew\Eventer\Event;
use Weew\Kernel\IKernel;

class KernelShutdownEvent extends Event {
    /**
     * @var IKernel
     */
    private $kernel;

    /**
     * KernelShutdownEvent constructor.
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
