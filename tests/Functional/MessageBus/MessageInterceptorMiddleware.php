<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Functional\MessageBus;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class MessageInterceptorMiddleware implements MiddlewareInterface
{
    public $messages = [];

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $this->messages[] = $envelope->getMessage();

        return $stack->next()->handle($envelope, $stack);
    }
}
