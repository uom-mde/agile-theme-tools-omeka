<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Laminas\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Omeka\File\ThumbnailManager as ThumbnailManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\View\Renderer\PhpRenderer;

class Slideshow extends AbstractBlockLayout
{

    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;
    /**
     * @var ThumbnailManager
     */
    protected $thumbnailManager;
    /**
     * @var array
     */
    protected $thumbnailObjectSizes;
    /**
     * @var array
     */
    protected $thumbnailObjectFits;
    /**
     * @var array
     */
    protected $thumbnailObjectPositions;
    /**
     * @var array
     */
    protected $attachmentOptions;

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager, ThumbnailManager $thumbnailManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
        $this->thumbnailManager = $thumbnailManager;
        $this->thumbnailObjectSizes = $this->thumbnailManager->getTypes();
        $this->thumbnailObjectFits = ['contain','cover'];
        $this->thumbnailObjectPositions = ['top','center','bottom'];
        $this->attachmentOptions = ['size' => 0, 'fit' => 1, 'position' => 1];
    }

    public function getLabel()
    {
        return 'Slideshow'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $data['introduction'] = isset($data['introduction']) ? $this->htmlPurifier->purify($data['introduction']) : '';
        $data['title'] = isset($data['title']) ? $this->htmlPurifier->purify($data['title']): '';
        $block->setData($data);
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $view->headLink()->appendStylesheet($view->assetUrl('css/agile_theme_tools_admin_styles.css', 'AgileThemeTools'));

        $title = new Text("o:block[__blockIndex__][o:data][title]");
        $title->setAttribute('class', 'block-title');
        $title->setLabel('Slideshow Title (optional)');

        $introductionField = new Textarea("o:block[__blockIndex__][o:data][introduction]");
        $introductionField->setLabel('Introductory Text (optional)');
        $introductionField->setAttribute('class', 'block-introduction full wysiwyg');
        $introductionField->setAttribute('rows',20);

        $region = new RegionMenuSelect();

        if ($block) {
            $title->setAttribute('value',$block->dataValue('title'));
            $introductionField->setAttribute('value', $block->dataValue('introduction'));
            $region->setAttribute('value', $block->dataValue('region'));
        }

        $html = $view->formRow($title);
        $html .= $view->blockAttachmentsForm($block);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Attachment Options'). '</h4></a>';
        $html .= '<div class="collapsible slideshow-options">';
        $html .= '<div>' 
                 . $view->translate("The 'Attachment Fit' option controls how an image fits into the enclosing area. 
                 See <a href='https://developer.mozilla.org/en-US/docs/Web/CSS/object-fit'>Object Fit CSS</a> 
                 for more information.") 
                 . '</div>';
        $html .= '<div>' 
                 . $view->translate("The 'Attachment Position' option aligns the image inside the enclosing area.
                 See <a href='https://developer.mozilla.org/en-US/docs/Web/CSS/object-position'>Object Position CSS</a> 
                 for more information.") 
                 . '</div>';
        foreach ($block->attachments() as $key => $attachment) {
            $item = $attachment->item();
            $key_name = $key + 1;
            $title = $item ? $item->displayTitle() : $key_name;
            $html .= '<h5>' . $view->translate('Options for attachment: <i>') . $title . '</i></h5>';
            $html .= '<div class="slideshow-refinements">';
            foreach ($this->attachmentOptions as $option => $defaultVal) {
                $html .= '<div><div>' . $view->translate(ucfirst($option)) . '</div>';
                ${'attachment' . ucfirst($option) . 'SelectedOption' . $key} = $block ? $block->dataValue('attachment_' . $option . '_select_option_' . $key, '') : '';
                ${'attachment' . ucfirst($option) . 'Select' . $key} = new Select('o:block[__blockIndex__][o:data][attachment_' . $option . '_select_option_' . $key . ']');
                ${'attachment' . ucfirst($option) . 'Select' . $key}->setValueOptions($this->{'thumbnailObject' . ucfirst($option) . 's'})->setValue(${'attachment' . ucfirst($option) . 'SelectedOption' . $key});
                $html .= $view->formRow(${'attachment' . ucfirst($option) . 'Select' . $key});
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Slideshow Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($introductionField);
        $html .= $view->formRow($region);
        $html .= $view->blockShowTitleSelect($block);
        $html .= '</div>';
        return $html;
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headLink()->appendStylesheet($view->basePath('modules/AgileThemeTools/node_modules/@accessible360/accessible-slick/slick/accessible-slick-theme.min.css'));
        $view->headLink()->appendStylesheet($view->basePath('modules/AgileThemeTools/node_modules/@accessible360/accessible-slick/slick/slick.min.css'));
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

        $data = $block->data();
        $showTitleOption = $block->dataValue('show_title_option', 'item_title');
        list($scope,$region) = explode(':',$data['region']);
        foreach ($this->attachmentOptions as $option => $defaultVal) {
            ${'attachment' . ucfirst($option)} = [];
        }
        $allowedMediaTypes = ['image', 'pdf'];
        $image_attachments = [];
        $audio_attachment = null;

        foreach($attachments as $key => $attachment) {
            foreach ($this->attachmentOptions as $option => $defaultVal) {
                array_push(${'attachment' . ucfirst($option)}, $this->{'thumbnailObject' . ucfirst($option) . 's'}[$block->dataValue('attachment_' . $option . '_select_option_' . $key, $defaultVal)]);
            }

            $item = $attachment->item();
            $media = $attachment->media() ?: $item->primaryMedia();

            // Filter for media type. $media->mediaType() returns a MIME type.

            if ($media) {
                foreach ($allowedMediaTypes as $allowedMediaType) {
                    if (strpos($media->mediaType(),$allowedMediaType) !== false) {
                        $image_attachments[] = $attachment;
                    } elseif (strpos($media->mediaType(),'audio') !== false && $audio_attachment == null) {
                        $audio_attachment = $attachment;
                    }
                }
            }
        }

        $renderValues = [
            'block' => $block,
            'useTitleSlide' => !empty($data['title']),
            'titleSlideAttachment' => $image_attachments[0],
            'titleSlideItem' => $image_attachments[0]->item(),
            'titleSlideMedia' => $image_attachments[0]->media() ?: $image_attachments[0]->primaryMedia(),
            'titleSlideTitle' => $data['title'],
            'titleSlideIntro' => $data['introduction'],
            'attachments' => $image_attachments,
            'showTitleOption' => $showTitleOption,
            'blockId' => $block->id(),
            'regionClass' => 'region-' . $region,
            'targetID' => '#' . $region,
            'hasAudioAttachment' => $audio_attachment != null,
            'audioAttachment' => $audio_attachment
        ];

        foreach ($this->attachmentOptions as $option => $defaultVal) {
            $renderValues['attachment' . ucfirst($option)] = ${'attachment' . ucfirst($option)};
        }

        return $view->partial('common/block-layout/slideshow', $renderValues);
    }
}
