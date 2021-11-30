<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Laminas\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Element\Select;
use Laminas\View\Renderer\PhpRenderer;

class SitePromo extends AbstractBlockLayout
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
        return 'Site Promo'; // @translate
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

        $data['sort_by'] = 'title';
        $response = $view->api()->search('sites', $data);
        $sites = $response->getContent();

        $siteList = array();
        foreach($sites as $site) {
            $siteList[$site->slug() . '|' . $site->title()] = $site->title();
        }

        $siteSelectedOption = $block ? $block->dataValue('show_site_select_option', '') : '';
        $siteSelect = new Select('o:block[__blockIndex__][o:data][show_site_select_option]');
        $siteSelect->setValueOptions($siteList)->setValue($siteSelectedOption);

        $region = new RegionMenuSelect();

        if ($block) {
            $textarea->setAttribute('value', $block->dataValue('html'));
            $region->setAttribute('value', $block->dataValue('region'));
        }

        return $view->partial(
            'site-admin/block-layout/site-promo',
            [
                'htmlform' => $view->formRow($textarea),
                'regionform' => $view->formRow($region),
                'sitelist' => $view->formRow($siteSelect),
                'attachment' => $view->blockAttachmentsForm($block),
                'data' => $block ? $block->data() : []
            ]
        );


    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'AgileThemeTools'));
        $view->headLink()->appendStylesheet($view->assetUrl('css/site-promo.css', 'AgileThemeTools'));
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {

        $data = $block->data();
        $link_and_title = explode("|", $data['show_site_select_option']);
        list($scope,$region) = explode(':',$data['region']);
        $siteBaseUrl = $view->basePath() . '/s/';
        return $view->partial(
            'common/block-layout/site-promo',
            [
                'html' => $data['html'],
                'blockId' => $block->id(),
                'regionClass' => 'region-' . $region,
                'targetID' => '#' . $region,
                'siteSelectLink' => $link_and_title[0],
                'siteSelectTitle' => $link_and_title[1],
                'attachments' => $block->attachments(),
                'baseUrl' => $siteBaseUrl
            ]
        );
    }


}
