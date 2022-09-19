<?php
  namespace AgileThemeTools;

  return array(
      'view_manager' => [
          'template_path_stack' => [
              dirname(__DIR__) . '/view',
          ],
      ],
      'block_layouts' => [
          'factories' => [
              'excerpt' => Service\BlockLayout\ExcerptFactory::class,
              'regionalHtml' => Service\BlockLayout\RegionalHtmlFactory::class,
              'byline' => Service\BlockLayout\BylineFactory::class,
              'representativeImage' => Service\BlockLayout\RepresentativeImageFactory::class,
              'openGraphImage' => Service\BlockLayout\OpenGraphImageFactory::class,
              'slideshow' => Service\BlockLayout\SlideshowFactory::class,
              'poster' => Service\BlockLayout\PosterFactory::class,
              'itemListing' => Service\BlockLayout\ItemListingFactory::class,
              'callout' => Service\BlockLayout\CalloutFactory::class,
              'quotation' => Service\BlockLayout\QuotationFactory::class,
              'deck' => Service\BlockLayout\DeckFactory::class,
              'sectionPageListing' => Service\BlockLayout\SectionPageListingFactory::class,
              'harmfulContent' => Service\BlockLayout\HarmfulContentFactory::class,
              'homepageSplash' => Service\BlockLayout\HomepageSplashFactory::class,
              'sectionIntroSplash' => Service\BlockLayout\SectionIntroSplashFactory::class,
              'responsiveEmbed' => Service\BlockLayout\ResponsiveEmbedFactory::class,
              'sitePromo' => Service\BlockLayout\SitePromoFactory::class,
          ]
      ],
      'form_elements' => [
          'invokables' => [
              'regionmenu' => Form\Element\RegionMenuSelect::class,
              'SectionListingTemplateSelect' => Form\Element\SectionListingTemplateSelect::class,
          ],
          'factories' => [
              'SectionsMenuSelect' => Service\Form\Element\SectionsMenuSelectFactory::class
          ]
      ],
      'service_manager' => [
              'factories' => [
                  'SectionManager' => Service\Controller\SectionManagerFactory::class,
              ],
          ]
 /*         'router' => [
              'routes' => [
                  'add-page-action' => [
                      'type' => \Zend\Router\Http\Segment::class,
                      'options' => [
                          'route' => '/admin/site/s/:site-slug/add-page',
                          'constraints' => [
                              'site-slug' => '[a-zA-Z0-9_-]+',
                          ],
                          'defaults' => [
                              '__NAMESPACE__' => 'RegionalOptionsForm\Controller',
                              'controller' => 'BlockOptionsAdminController',
                              'action' => 'add',
                          ]
                      ]
                  ]
              ]
          ] */

  );