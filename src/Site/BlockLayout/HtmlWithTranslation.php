<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use AgileThemeTools\View\Helper\AvailableLanguagesHelper;
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

class HtmlWithTranslation extends AbstractBlockLayout
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
        $this->availableLanguages = new AvailableLanguagesHelper;
        $this->default_language = $this->availableLanguages->getDefaultAvailableLanguage();
    }

    public function getLabel()
    {
        return 'HTML with Translation(s)'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $html = isset($data['html']) ? $this->htmlPurifier->purify($data['html']) : '';
        $data['html'] = $html;

        // find any newly created translations
        foreach ($data as $key => $val) {
            if (preg_match("/translation_html_language_([0-9]+)/", $key)) {
                $data[$key] = $val;
              }
            if (preg_match("/translation_html_([0-9]+)/", $key)) {
                $data[$key] = $val;
            }
        }

        $block->setData($data);
    }

    public function prepareForm(PhpRenderer $view) {
        $view->headLink()->appendStylesheet($view->assetUrl('css/agile_theme_tools_admin_styles.css', 'AgileThemeTools'));
        $view->headScript()->appendFile($view->assetUrl('js/agile_theme_tools_admin.js', 'AgileThemeTools'));
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $textarea = new Textarea("o:block[__blockIndex__][o:data][html]");
        $textarea->setAttribute('class', 'block-html full wysiwyg');
        $textarea->setAttribute('rows',20);

        $translationLanguageList = $this->availableLanguages->getAvailableLanguages();
        $language_select_template_default = $this->default_language;

        $language_select_template = new Select('o:block[__blockIndex__][o:data][translation_html_language_{idx}]');
        $language_select_template->setValueOptions($translationLanguageList)->setValue($language_select_template_default);

        $textarea_translation_template = new Textarea("o:block[__blockIndex__][o:data][translation_html_{idx}]");
        $textarea_translation_template->setAttribute('class', 'block-html full wysiwyg');
        $textarea_translation_template->setAttribute('id', 'translation_html_{idx}');
        $textarea_translation_template->setAttribute('rows',20);

        if ($block) {
            $textarea->setAttribute('value', $block->dataValue('html'));
            $translations = [];
            foreach ($block->data() as $key => $val) {
                if (preg_match("/translation_html_language_([0-9]+)/", $key)) {
                    ${$key} = new Select("o:block[__blockIndex__][o:data][" . $key . "]");
                    ${$key}->setValueOptions($translationLanguageList);        
                    ${$key}->setAttribute('value', $block->dataValue("{$key}"));
                    array_push($translations, $view->formRow(${$key}));
                  }
                if (preg_match("/translation_html_([0-9]+)/", $key)) {
                    ${$key} = new Textarea("o:block[__blockIndex__][o:data][" . $key . "]");
                    ${$key}->setAttribute('class', 'block-html full wysiwyg');
                    ${$key}->setAttribute('id', $key);
                    ${$key}->setAttribute('rows',20);
                    ${$key}->setAttribute('value', $block->dataValue("{$key}"));
                    array_push($translations, $view->formRow(${$key}));
                }
            }
        }
        else {
            $translations = [];
        }

        return $view->partial(
            'site-admin/block-layout/html-with-translation.phtml',
            [
                'htmlform' => $view->formRow($textarea),
                'languageSelectTemplate' => $view->formRow($language_select_template),
                'translationTemplate' => $view->formRow($textarea_translation_template),
                'translations' => $translations,
                'data' => $block ? $block->data() : []
            ]
        );


    }

    public function prepareRender(PhpRenderer $view)
    {
        //$view->headScript()->appendFile($view->assetUrl('js/regional_html_handler.js', 'AgileThemeTools'));
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {

        $data = $block->data();
        return $view->partial(
            'common/block-layout/html-with-translation.phtml',
            [
                'html' => $data['html'],
                'blockId' => $block->id(),
            ]
        );
    }
}
