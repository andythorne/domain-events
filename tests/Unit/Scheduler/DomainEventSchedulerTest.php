<?php

namespace Tests\AndyThorne\Components\DomainEventsBundle\Unit\Scheduler;

use AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface;
use AndyThorne\Components\DomainEventsBundle\Scheduler\DomainEventScheduler;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class DomainEventSchedulerTest extends TestCase
{
    private $messageBus;
    private $testSubject;

    public function setUp(): void
    {
        $this->prophet = new Prophet();
        $this->messageBus = $this->prophet->prophesize(MessageBusInterface::class);

        $this->testSubject = new DomainEventScheduler(
            $this->messageBus->reveal()
        );
    }

    public function testScheduleWithDomainEventAddsDomainEventToQueue()
    {
        $event = $this->prophet->prophesize(DomainEventInterface::class);

        $this->testSubject->schedule($event->reveal());

        $this->assertEquals(1, $this->testSubject->size());
    }

    public function testProcessWithUnsortedDomainEventsDispatchesEventsInChronologicalOrder()
    {
        $baseTime = new DateTimeImmutable('2019-12-03T12:00:00', new DateTimeZone('Europe/London'));

        $eventOldest = $this->createDomainEventWithDate($baseTime);
        $eventOld = $this->createDomainEventWithDate($baseTime->add(new DateInterval('P1D')));
        $eventNew = $this->createDomainEventWithDate($baseTime->add(new DateInterval('P2D')));
        $eventNewest = $this->createDomainEventWithDate($baseTime->add(new DateInterval('P3D')));

        $this->testSubject->schedule($eventNewest->reveal());
        $this->testSubject->schedule($eventOld->reveal());
        $this->testSubject->schedule($eventNew->reveal());
        $this->testSubject->schedule($eventOldest->reveal());

        // This matcher ensures the order dispatch() is called in is chronological
        $this->messageBus->dispatch($eventOldest->reveal())
            ->shouldBeCalledOnce()
            ->will(function ($e1) use ($eventOld, $eventNew, $eventNewest) {
                $this->dispatch($eventOld->reveal())
                    ->shouldBeCalledOnce()
                    ->will(function ($e2) use ($eventNew, $eventNewest) {
                        $this->dispatch($eventNew->reveal())
                            ->shouldBeCalledOnce()
                            ->will(function ($e3) use ($eventNewest) {
                                $this->dispatch($eventNewest->reveal())
                                    ->shouldBeCalledOnce()
                                    ->will(function ($e4) {
                                        return new Envelope($e4[0]);
                                    });

                                return new Envelope($e3[0]);
                            });

                        return new Envelope($e2[0]);
                    });

                return new Envelope($e1[0]);
            });

        $this->assertEquals(4, $this->testSubject->size());

        $this->testSubject->process();

        $this->assertEquals(0, $this->testSubject->size());
    }

    public function testProcessDomainEventsQueuesAnotherDomainEventDispatchesInitialEventsOnly()
    {
        $baseTime = new DateTimeImmutable('2019-12-03T12:00:00', new DateTimeZone('Europe/London'));

        $eventOldest = $this->createDomainEventWithDate($baseTime);
        $eventOld = $this->createDomainEventWithDate($baseTime->add(new DateInterval('P1D')));

        $eventNewlyDispatched = $this->createDomainEventWithDate($baseTime->add(new DateInterval('P2D')));

        $this->testSubject->schedule($eventOldest->reveal());
        $this->testSubject->schedule($eventOld->reveal());

        $scheduler = $this->testSubject;

        // This matcher ensures the order dispatch() is called in is chronological
        $this->messageBus->dispatch($eventOldest->reveal())
            ->shouldBeCalledOnce()
            ->will(function ($e1) use ($scheduler, $eventOld, $eventNewlyDispatched) {
                $scheduler->schedule($eventNewlyDispatched->reveal());

                $this->dispatch($eventOld->reveal())
                    ->shouldBeCalledOnce()
                    ->will(function ($e2) use ($eventNewlyDispatched) {
                        $this->dispatch($eventNewlyDispatched->reveal())
                            ->shouldBeCalledOnce()
                            ->will(function ($e3) {
                                return new Envelope($e3[0]);
                            });

                        return new Envelope($e2[0]);
                    });

                return new Envelope($e1[0]);
            });

        $this->assertEquals(2, $this->testSubject->size());

        $this->testSubject->process();

        $this->assertEquals(0, $this->testSubject->size());
    }

    private function createDomainEventWithDate(DateTimeImmutable $createdAt)
    {
        $event = $this->prophet->prophesize(DomainEventInterface::class);
        $event->getCreatedAt()->willReturn($createdAt);

        return $event;
    }
}
