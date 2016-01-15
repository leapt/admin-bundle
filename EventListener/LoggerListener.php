<?php

namespace Leapt\AdminBundle\EventListener;

use Leapt\AdminBundle\Event\AdminEvents;
use Leapt\AdminBundle\Event\ContentAdminEvent;
use Leapt\AdminBundle\Logger\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LoggerListener
 * @package Leapt\AdminBundle\EventListener
 */
class LoggerListener implements EventSubscriberInterface
{
    /**
     * @var \Leapt\AdminBundle\Logger\Logger
     */
    private $logger;

    /**
     * @param \Leapt\AdminBundle\Logger\Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AdminEvents::CONTENT_CREATE => 'onContentAction',
            AdminEvents::CONTENT_UPDATE => 'onContentAction',
            AdminEvents::CONTENT_DELETE => 'onContentAction',
        ];
    }

    /**
     * @param ContentAdminEvent $event
     * @param string $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function onContentAction(ContentAdminEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $entity = $event->getEntity();
        $admin = $event->getAdmin();

        $this->logger->log('content', $this->getAction($eventName), $admin->getEntityName($entity), $admin->getAlias(), $entity->getId());
    }

    /**
     * @param $eventName
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getAction($eventName)
    {
        switch ($eventName) {
            case AdminEvents::CONTENT_CREATE:
                return 'content_create';
                break;
            case AdminEvents::CONTENT_UPDATE:
                return 'content_update';
                break;
            case AdminEvents::CONTENT_DELETE:
                return 'content_delete';
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Cannot process event "%s"', $eventName));
                break;
        }
    }
}