# Symfony Domain Events Bundle
This bundle adds Domain Event dispatching on doctrine events via a [MessageBus](https://symfony.com/doc/current/components/messenger.html).

![CI](https://github.com/andythorne/domain-events/workflows/CI/badge.svg)

## Installation
```bash
composer require andy-thorne/domain-events-bundle
```

## Configuration
```yaml
# Defaults
domain_events:
    orm: true
    odm: false
    bus: domain_event.bus
    transport: async_domain_events

framework:
    messenger:
        transports:
            async_domain_events: "%env(ASYNC_MESSENGER_TRANSPORT_DSN)%"
```

The bundle will also configure these messenger settings based on your `domain_events` config. If you already have buses
configured, you will need to specify a `framework.messenger.default_bus`.
```yaml
# The bundle also configures your
framework:
    messenger:
        # Set up a bus that will allows no handlers
        buses:
            <domain_events.bus>:
                default_middleware: allow_no_handlers

        # Route all domain events to the domain event transport
        routing:
            'AndyThorne\Components\DomainEventsBundle\Events\DomainEventInterface': <domain_events.transport>
```

## Message Bus
Domain Events uses the app's MessageBus to transport domain events. The default Messenger Component is configured to be
synchronous and requires at least one handler to be defined for each Message. For Domain Events to work, we need to
configure an asynchronous MessageBus and allow it to have no handlers:

### Why Asynchronous?
Domain Events are dispatched within a doctrine postFlush lifecycle event
