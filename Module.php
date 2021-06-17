<?php

namespace AgileThemeTools;

use Doctrine\ORM\Events;
use AltText\Db\Event\Listener\DetachOrphanMappings;
use AltText\Entity\AltText as AltTextEntity;
use Omeka\Api\Representation\MediaRepresentation;
use Omeka\Entity\Media as MediaEntity;
use Omeka\Module\AbstractModule;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    const NAMESPACE = __NAMESPACE__;


    public function getConfig()

    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);
        // $acl = $this->getServiceLocator()->get('Omeka\Acl');
        //$acl->allow(null, [\AgileThemeTools\Controller\BlockOptionsAdminController::class]);
        $services = $this->getServiceLocator();
        $em = $services->get('Omeka\EntityManager');
        if (class_exists(\AltText\Db\Event\Listener\DetachOrphanMappings::class)) {
            $em->getEventManager()->addEventListener(
                Events::preFlush,
                new DetachOrphanMappings
            );
        }
    }
    
    public function install(ServiceLocatorInterface $services) {
        if (!class_exists(\AltText\Db\Event\Listener\DetachOrphanMappings::class)) {
            $translator = $services->get('MvcTranslator');
            $message = new \Omeka\Stdlib\Message(
                $translator->translate('This module requires the module "%s".'), // @translate
                'AltText'
            );
            throw new \Omeka\Module\Exception\ModuleCannotInstallException((string) $message);
        }
    }
    
    
    // Code borrowed from the AltText module (with thanks!). Substitutes dc:description if no Alt tag provided.
    // Defaults to the title of the file, which is often just the filename.
    
    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            '*',
            'view_helper.thumbnail.attribs',
            function (Event $event) {
                $media = $event->getParam('primaryMedia');
                if (!$media) {
                    return;
                }

                $attribs = $event->getParam('attribs');
                
                if (empty($attribs['alt'])) {
                  $item = $media->item();
                  $description = $item->value('dcterms:description');
                  $attribs['alt'] = !empty($description) ? htmlspecialchars(strip_tags($description)) : $media->displayTitle();
                }

                $event->setParam('attribs', $attribs);
            }
        );
      }

}