<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Select;
use Zend\View\Renderer\PhpRenderer;

class ResponsiveEmbed extends AbstractBlockLayout
{
    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
    }

    public function getLabel()
    {
        return 'Responsive Embed'; // @translate
    }


    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $html = isset($data['html']) ? $this->htmlPurifier->purify($data['html']) : '';
        $data['html'] = $html;
        $block->setData($data);
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $textarea = new Textarea("o:block[__blockIndex__][o:data][html]");
        $textarea->setAttribute('class', 'block-html full wysiwyg');
        $textarea->setAttribute('rows',20);
        
        $aspectOptions = [
            'aspect16_9' => '16 x 9', // @translate
            'aspect4_3' => '4 x 3', // @translate
        ];
        $aspectSelectedOption = $block ? $block->dataValue('show_aspect_option', 'aspect16_9') : 'aspect16_9';
        $aspectSelect = new Select('o:block[__blockIndex__][o:data][show_aspect_option]');
        $aspectSelect->setValueOptions($aspectOptions)->setValue($aspectSelectedOption);

        $region = new RegionMenuSelect();

        if ($block) {
            $textarea->setAttribute('value', $block->dataValue('html'));
            $region->setAttribute('value', $block->dataValue('region'));
        }

        return $view->partial(
            'site-admin/block-layout/responsive-embed',
            [
                'htmlform' => $view->formRow($textarea),
                'regionform' => $view->formRow($region),
                'aspectform' => $view->formRow($aspectSelect),
                'data' => $block ? $block->data() : []
            ]
        );


    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'AgileThemeTools'));
        $view->headLink()->appendStylesheet($view->assetUrl('css/responsive_embed.css', 'AgileThemeTools'));
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {

        $data = $block->data();
        
        // remove the height, width, and style attributes from the iFrame
        $editedEmbed = preg_replace('/width="\d+"/','', $data['html']);
        $editedEmbed = preg_replace('/height="\d+"/','', $editedEmbed);
        $editedEmbed = preg_replace('/style="[^"]*"/','', $editedEmbed);
        
        list($scope,$region) = explode(':',$data['region']);
        return $view->partial(
            'common/block-layout/responsive-embed',
            [
                'html' => $editedEmbed,
                'blockId' => $block->id(),
                'regionClass' => 'region-' . $region,
                'targetID' => '#' . $region,
                'aspectRatio' => $data['show_aspect_option']
            ]
        );
    }


}


