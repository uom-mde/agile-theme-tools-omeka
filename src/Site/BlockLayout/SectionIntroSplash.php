<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use AgileThemeTools\View\Helper\SlideshowHelper;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\FormElementManager as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\File\ThumbnailManager as ThumbnailManager;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\View\Renderer\PhpRenderer;

class SectionIntroSplash extends AbstractBlockLayout
{

    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager, ThumbnailManager $thumbnailManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
        $this->slideshowHelper = new SlideshowHelper($thumbnailManager);
        $this->slideshowHelper->attachment_values_init();
    }

    public function getLabel()
    {
        return 'Section Introduction Splash'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $data['introduction'] = isset($data['introduction']) ? $this->htmlPurifier->purify($data['introduction']) : '';
        $data['title'] = isset($data['title']) ? $this->htmlPurifier->purify($data['title']): '';
        $block->setData($data);
    }

    public function prepareForm(PhpRenderer $view) {
        $view->headLink()->appendStylesheet($view->assetUrl('css/agile_theme_tools_admin_styles.css', 'AgileThemeTools'));
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $title = new Text("o:block[__blockIndex__][o:data][title]");
        $title->setAttribute('class', 'block-title');
        $title->setLabel('Section Title');

        $introductionField = new Textarea("o:block[__blockIndex__][o:data][introduction]");
        $introductionField->setLabel('Section Intro Text');
        $introductionField->setAttribute('class', 'block-introduction full wysiwyg');
        $introductionField->setAttribute('rows',20);

        $alignmentLeftField = new Checkbox("o:block[__blockIndex__][o:data][alignment]");
        $alignmentLeftField->setLabel('Align Left');
        $alignmentLeftField->setCheckedValue("left");
        $alignmentLeftField->setUncheckedValue("right");

        $region = new RegionMenuSelect();

        if ($block) {
            $title->setAttribute('value',$block->dataValue('title'));
            $introductionField->setAttribute('value', $block->dataValue('introduction'));
            $alignmentLeftField->setAttribute('value', $block->dataValue('alignment'));
            $region->setAttribute('value', $block->dataValue('region'));
        } else {
            $region->setAttribute('value','region:splash');
        }

        $html = $view->formRow($title);
        $html .= $view->blockAttachmentsForm($block);
        if ($block) {
            $html .= $this->slideshowHelper->slideshow_options_form_html($view, $block);
        }
        else {
            $html .= '<div><b>Note:</b> After adding an attachment, save the page to see options for the attachment.</div>';
        }
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($introductionField);
        $html .= $view->formRow($alignmentLeftField);
        $html .= $view->formRow($region);
        $html .= $view->blockShowTitleSelect($block);
        $html .= '</div>';
        return $html;
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headLink()->appendStylesheet($view->basePath('modules/AgileThemeTools/node_modules/@accessible360/accessible-slick/slick/accessible-slick-theme.min.css'));
        $view->headLink()->appendStylesheet($view->basePath('modules/AgileThemeTools/node_modules/@accessible360/accessible-slick/slick/slick.min.css'));
        $view->headLink()->appendStylesheet($view->assetUrl('css/agile_theme_tools_slideshow_options.css', 'AgileThemeTools'));
        $view->headScript()->appendFile($view->basePath('modules/AgileThemeTools/node_modules/@accessible360/accessible-slick/slick/slick.min.js'));
        $view->headScript()->appendFile($view->assetUrl('js/slideshow.js', 'AgileThemeTools'));
        $view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'AgileThemeTools'));
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $attachments = $block->attachments();
        if (!$attachments) {
            return '';
        }
        $attachment_scale_values = [];

        foreach($attachments as $key => $attachment) {
            $this->slideshowHelper->attachment_values($block, $key);
            array_push($attachment_scale_values, $this->slideshowHelper->attachment_scale_values($block->dataValue('attachment_scale_' . $key, 1), $key));
        }

        $data = $block->data();
        $showTitleOption = $block->dataValue('show_title_option', 'item_title');
        list($scope,$region) = explode(':',$data['region']);

        $render_values = [
            'block' => $block,
            'useTitleSlide' => !empty($data['title']) || !empty($data['introduction']),
            'titleSlideAttachment' => $attachments[0],
            'titleSlideItem' => $attachments[0]->item(),
            'titleSlideMedia' => $attachments[0]->media() ?: $attachments[0]->primaryMedia(),
            'titleSlideTitle' => $data['title'],
            'titleSlideIntro' => $data['introduction'],
            'titleSlideTextAlignment' => $data['alignment'],
            'attachments' => $attachments,
            'showTitleOption' => $showTitleOption,
            'blockId' => $block->id(),
            'regionClass' => 'region-' . $region,
            'targetID' => '#' . $region,
            'attachmentOptions' => $this->slideshowHelper->attachment_options(),
            'attachmentScaleValues' => $attachment_scale_values,
        ];

        $attachment_render_values = $this->slideshowHelper->render_values();

        return $view->partial('common/block-layout/section-intro-splash', array_merge($render_values, $attachment_render_values));
    }
}
