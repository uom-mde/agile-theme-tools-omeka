<?php

namespace AgileThemeTools;

use Doctrine\ORM\Events;
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
    
    public function upgrade($oldVersion, $newVersion, ServiceLocatorInterface $serviceLocator) {
        $conn = $serviceLocator->get('Omeka\Connection');
        $conn->exec('UPDATE media, alt_text SET media.alt_text = alt_text.alt_text WHERE media.id = alt_text.media_id');
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