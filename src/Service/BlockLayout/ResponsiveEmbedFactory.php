<?php
namespace AgileThemeTools\Service\BlockLayout;

use AgileThemeTools\Site\BlockLayout\ResponsiveEmbed;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ResponsiveEmbedFactory implements FactoryInterface
{
    /**
     * Create the Html block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return ResponsiveEmbed
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        $formElementManager = $serviceLocator->get('FormElementManager');
        return new ResponsiveEmbed($htmlPurifier,$formElementManager);
    }
}
