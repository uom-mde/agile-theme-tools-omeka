<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use AgileThemeTools\View\Helper\AvailableLanguagesHelper;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Laminas\Form\FormElementManager as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\View\Renderer\PhpRenderer;

class HtmlWithAlternate extends AbstractBlockLayout
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
        $this->alternateLanguageList = $this->availableLanguages->getAvailableLanguages();

        $this->type_default = 'translation';
        $this->alternateTypeList = ['other' => 'Other','transcription' => 'Transcription','translation' => 'Translation'];
    }

    public function getLabel()
    {
        return 'HTML with alternate text block(s)'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $html = isset($data['html']) ? $this->htmlPurifier->purify($data['html']) : '';
        $data['html'] = $html;

        // find any newly created alternates
        foreach ($data as $key => $val) {
            if (preg_match("/alternate_html_title_([0-9]+)/", $key)) {
                $data[$key] = $val;
            }
            if (preg_match("/alternate_html_language_([0-9]+)/", $key)) {
                $data[$key] = $val;
            }
            if (preg_match("/alternate_html_type_([0-9]+)/", $key)) {
                $data[$key] = $val;
            }
            if (preg_match("/alternate_html_([0-9]+)/", $key)) {
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

        $textareaTitle = new Text("o:block[__blockIndex__][o:data][html_title]");
        $textareaTitle->setAttribute('class', 'html-alternate-original-title');
        $textareaTitle->setLabel('Title');

        $textarea_language = new Select('o:block[__blockIndex__][o:data][html_language]');
        $textarea_language->setValueOptions($this->alternateLanguageList)->setValue($this->default_language);
        $textarea_language->setLabel('Select original language');

        $displayAlternateBlock = new Checkbox("o:block[__blockIndex__][o:data][display_alternate_block]");
        $displayAlternateBlock->setLabel('Show Alternates in separate area?');
        $displayAlternateBlock->setCheckedValue("yes");
        $displayAlternateBlock->setUncheckedValue("no");

        $textarea = new Textarea("o:block[__blockIndex__][o:data][html]");
        $textarea->setAttribute('class', 'block-html full wysiwyg');
        $textarea->setAttribute('rows',20);

        $title_template = new Text("o:block[__blockIndex__][o:data][alternate_html_title_{idx}]");
        $title_template->setAttribute('class', 'html-alternate-title');
        $title_template->setLabel('Title');

        $type_template = new Select('o:block[__blockIndex__][o:data][alternate_html_type_{idx}]');
        $type_template->setValueOptions($this->alternateTypeList)->setValue($this->type_default);
        $type_template->setLabel('Select alternate type');

        $language_select_template = new Select('o:block[__blockIndex__][o:data][alternate_html_language_{idx}]');
        $language_select_template->setValueOptions($this->alternateLanguageList)->setValue($this->default_language);
        $language_select_template->setLabel('Select alternate language');

        $textarea_alternate_template = new Textarea("o:block[__blockIndex__][o:data][alternate_html_{idx}]");
        $textarea_alternate_template->setLabel('Alternate');
        $textarea_alternate_template->setAttribute('class', 'block-html full');
        $textarea_alternate_template->setAttribute('id', 'alternate_html_{idx}');
        $textarea_alternate_template->setAttribute('rows',20);

        $alternates = [];
        $alternate_languages = [];
        $alternate_types = [];
        $alternate_titles = [];

        if ($block) {
            $textarea_language->setAttribute('value', $block->dataValue('html_language'));
            $textarea->setAttribute('value', $block->dataValue('html'));
            $textareaTitle->setAttribute('value', $block->dataValue('html_title'));
            $displayAlternateBlock->setAttribute('value', $block->dataValue('display_alternate_block'));

            foreach ($block->data() as $key => $val) {
                if (preg_match("/alternate_html_title_([0-9]+)/", $key)) {
                    ${$key} = new Text("o:block[__blockIndex__][o:data][" . $key . "]");
                    ${$key}->setAttribute('class', 'html-alternate-title');
                    ${$key}->setLabel('Title');
                    ${$key}->setAttribute('value', $block->dataValue("{$key}"));
                    array_push($alternate_titles, $view->formRow(${$key}));
                }
                if (preg_match("/alternate_html_language_([0-9]+)/", $key)) {
                    ${$key} = new Select("o:block[__blockIndex__][o:data][" . $key . "]");
                    ${$key}->setLabel('Select alternate language');
                    ${$key}->setValueOptions($this->alternateLanguageList);        
                    ${$key}->setAttribute('value', $block->dataValue("{$key}"));
                    array_push($alternate_languages, $view->formRow(${$key}));
                }
                if (preg_match("/alternate_html_type_([0-9]+)/", $key)) {
                    ${$key} = new Select("o:block[__blockIndex__][o:data][" . $key . "]");
                    ${$key}->setLabel('Select alternate type');
                    ${$key}->setValueOptions($this->alternateTypeList);        
                    ${$key}->setAttribute('value', $block->dataValue("{$key}"));
                    array_push($alternate_types, $view->formRow(${$key}));
                }
                if (preg_match("/alternate_html_([0-9]+)/", $key)) {
                    ${$key} = new Textarea("o:block[__blockIndex__][o:data][" . $key . "]");
                    ${$key}->setLabel('Alternate');
                    ${$key}->setAttribute('class', 'block-html full wysiwyg');
                    ${$key}->setAttribute('id', $key);
                    ${$key}->setAttribute('rows',5);
                    ${$key}->setAttribute('value', $block->dataValue("{$key}"));
                    array_push($alternates, $view->formRow(${$key}));
                }
            }
        }

        return $view->partial(
            'site-admin/block-layout/html-with-alternate.phtml',
            [
                'htmlform' => $view->formRow($textarea),
                'htmlformTitle' => $view->formRow($textareaTitle),
                'selectform' => $view->formRow($textarea_language),
                'displayAlternateBlock' => $view->formRow($displayAlternateBlock),
                'typeTemplate' => $view->formRow($type_template),
                'languageSelectTemplate' => $view->formRow($language_select_template),
                'titleTemplate' => $view->formRow($title_template),
                'alternateTemplate' => $view->formRow($textarea_alternate_template),
                'alternateTitles' => $alternate_titles,
                'alternateTypes' => $alternate_types,
                'alternateLanguages' => $alternate_languages,
                'alternates' => $alternates,
                'data' => $block ? $block->data() : []
            ]
        );


    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headLink()->appendStylesheet($view->assetUrl('css/html_alternate.css', 'AgileThemeTools'));
        $view->headScript()->appendFile($view->assetUrl('js/html_alternate.js', 'AgileThemeTools'));
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block) {

        $data = $block->data();
        $alternates = [];
        $alternate_types = [];
        $alternate_languages = [];
        $alternate_language_codes = [];
        $alternate_titles = [];

        foreach ($data as $key => $val) {
            if (preg_match("/alternate_html_title_([0-9]+)/", $key)) {
                array_push($alternate_titles, $val);
            }
            if (preg_match("/alternate_html_language_([0-9]+)/", $key)) {
                array_push($alternate_language_codes, $val);
                array_push($alternate_languages, $this->alternateLanguageList[$val]);
            }
            if (preg_match("/alternate_html_type_([0-9]+)/", $key)) {
                array_push($alternate_types, $this->alternateTypeList[$val]);
            }
            if (preg_match("/alternate_html_([0-9]+)/", $key)) {
                array_push($alternates, $val);
            }
        }
        return $view->partial(
            'common/block-layout/html-with-alternate.phtml',
            [
                'html' => $data['html'],
                'htmlTitle' => $data['html_title'],
                'originalLanguageCode' => $data['html_language'],
                'displayAlternateBlock' => $data['display_alternate_block'],
                'originalLanguage' => $this->alternateLanguageList[$data['html_language']],
                'alternates' => $alternates,
                'alternateTitles' => $alternate_titles,
                'alternateTypes' => $alternate_types,
                'alternateLanguageCodes' => $alternate_language_codes,
                'alternateLanguages' => $alternate_languages,
                'blockId' => $block->id(),
            ]
        );
    }
}
