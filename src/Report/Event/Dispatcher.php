<?php

namespace Report\Event;


class Dispatcher implements DispatcherInterface
{
    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @param string $eventName
     * @param string|array $listener
     */
    public function attachListener($eventName, $listener)
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * @param string $eventName
     * @param string|array $listener
     */
    public function detachListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName]))
            return;

        foreach ($this->listeners[$eventName] as $i => &$l) {
            if ($l == $listener) {
                unset($this->listeners[$eventName][$i]);
                if (empty($this->listeners[$eventName]))
                    unset($this->listeners[$eventName]);
                return;
            }
        }
    }

    /**
     * @param Event $event
     */
    public function dispatch(Event $event) {

        if (!($this->listeners && is_array($this->listeners)
            && isset($this->listeners[$event::getName()]) && count($this->listeners[$event::getName()])))
            return;

        foreach ($this->listeners[$event::getName()] as $listener) {
            if (is_array($listener)) {
                $object = $listener[0];
                $method = $listener[1];
                if (method_exists($object, $method)) {
                    $object->$method($event);
                }
            } else {
                $listener($event);
            }
        }

    }
}
