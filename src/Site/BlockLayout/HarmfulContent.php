<?php
namespace AgileThemeTools\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Laminas\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Element\Text;
use Laminas\View\Renderer\PhpRenderer;

class HarmfulContent extends AbstractBlockLayout
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
        return 'Harmful Content Statement'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $html = isset($data['harmful_content']) ? $this->htmlPurifier->purify($data['harmful_content']) : '';
        $data['harmful_content'] = $html;
        $block->setData($data);
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $textarea = new Textarea("o:block[__blockIndex__][o:data][harmful_content]");
        $textarea->setAttribute('class', 'block-html full wysiwyg');
        $textarea->setAttribute('rows',20);
        $textarea->setOptions(['info', 'foobar']);
        $textarea->setLabel('Harmful Content Statement');

        if ($block) {
            $textarea->setAttribute('value', $block->dataValue('harmful_content'));
        }

        $html = '';
        $html .= $view->formRow($textarea);
        return $html;

    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {

        $data = $block->data();

        return $view->partial(
            'common/block-layout/harmful-content',
            [
                'html' => $data['harmful_content'],
                'blockId' => $block->id(),
            ]
        );
    }


}


