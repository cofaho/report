<?php

namespace Report\Event;


interface DispatcherInterface
{
    /**
     * @param string $eventName
     * @param string|array $listener
     */
    public function attachListener($eventName, $listener);

    /**
     * @param string $eventName
     * @param string|array $listener
     */
    public function detachListener($eventName, $listener);

    /**
     * @param Event $event
     */
    public function dispatch(Event $event);
}
