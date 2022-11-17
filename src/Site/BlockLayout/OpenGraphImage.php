<?php
namespace AgileThemeTools\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Stdlib\HtmlPurifier;
use Laminas\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Laminas\View\Renderer\PhpRenderer;
use AgileThemeTools\Form\Element\RegionMenuSelect;

class OpenGraphImage extends AbstractBlockLayout
{
    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;


    public function getLabel()
    {
        return 'Open Graph Image (og:image)'; // @translate
    }

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
    }


    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $region = new RegionMenuSelect();

        if ($block) {
            $region->setAttribute('value', $block->dataValue('region'));
        }


        $html = '<p>Am Open Graph Image (og:image) is used as a thumbnail when this content is shared on social media. It does <strong>not</strong> appear on the primary page.';
        $html .= $view->blockAttachmentsForm($block);
        return $html;
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $attachments = $block->attachments();
        if (!$attachments) {
            return '';
        }

        $data = $block->data();
        $thumbnailType = 'medium';
        
        return $view->partial('common/block-layout/open-graph-image', [
            'block' => $block,
            'attachment' => $attachments[0],
            'thumbnailType' => $thumbnailType,
        ]);
    }
}