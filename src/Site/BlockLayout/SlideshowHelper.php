<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\File\ThumbnailManager;
use Laminas\Form\Element\Select;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * A helper class to collect and render slideshow attachment
 * options.
 * 
 * For each option constant defined, also define a corresponding
 * thumbnail_{option_name}_options() function.
 * Make sure to include any relevant base css associated with the
 * new option in asset/css/agile_slideshow.css
 * Make sure to update the edit display css, specifically the grid layout
 * in asset/css/agile_theme_tools_admin_styles.css
 */

class SlideshowHelper {
  const THUMBNAIL_FIT_OPTIONS = ['contain', 'cover'];
  const THUMBNAIL_POSITION_OPTIONS = [
    'top-center',
    'center-center',
    'bottom-center',
    'top-left',
    'center-left',
    'bottom-left',
    'top-right',
    'center-right',
    'bottom-right'
  ];
  const ATTACHMENT_OPTIONS = ['size' => 0, 'fit' => 1, 'position' => 1];

  public function __construct($thumbnailManager) {
    $this->thumbnailObjectSizes = $thumbnailManager->getTypes();
  }

  public function thumbnail_fit_options() {
    return self::THUMBNAIL_FIT_OPTIONS;
  }

  public function thumbnail_position_options() {
    return self::THUMBNAIL_POSITION_OPTIONS;
  }

  public function thumbnail_size_options() {
    return $this->thumbnailObjectSizes;
  }

  public function attachment_options() {
    return self::ATTACHMENT_OPTIONS;
  }

  public function attachment_values_init() {
    foreach ($this->attachment_options() as $option => $defaultVal) {
      $this->{'attachment' . ucfirst($option) . 'Value'} = [];
    }
  }

  public function attachment_values(SitePageBlockRepresentation $block, $key) {
    foreach ($this->attachment_options() as $option => $defaultVal) {
      array_push($this->{'attachment' . ucfirst($option) . 'Value'}, $this->{'thumbnail_' . $option . '_options'}()[$block->dataValue('attachment_' . $option . '_select_option_' . $key, $defaultVal)]);
    }
  }

  public function render_values() {
    $render_values = [];
    foreach ($this->attachment_options() as $option => $defaultVal) {
      $render_values['attachment' . ucfirst($option)] = $this->{'attachment' . ucfirst($option) . 'Value'};
    }
    return $render_values;
  }

  public function slideshow_options_form_html(PhpRenderer $view, SitePageBlockRepresentation $block) {
    $html = '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Attachment Options'). '</h4></a>';
    $html .= '<div class="collapsible slideshow-options">';
    $html .= '<div><b>Note:</b> After adding a new attachment, save the page to see options for the new attachment.</div>';
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
        foreach ($this->attachment_options() as $option => $defaultVal) {
            $html .= '<div><div>' . $view->translate(ucfirst($option)) . '</div>';
            ${'attachment' . ucfirst($option) . 'SelectedOption' . $key} = $block ? $block->dataValue('attachment_' . $option . '_select_option_' . $key, $defaultVal) : $defaultVal;
            ${'attachment' . ucfirst($option) . 'Select' . $key} = new Select('o:block[__blockIndex__][o:data][attachment_' . $option . '_select_option_' . $key . ']');
            ${'attachment' . ucfirst($option) . 'Select' . $key}->setValueOptions($this->{'thumbnail_' . $option . '_options'}())->setValue(${'attachment' . ucfirst($option) . 'SelectedOption' . $key});
            $html .= $view->formRow(${'attachment' . ucfirst($option) . 'Select' . $key});
            $html .= '</div>';
        }
        $html .= '</div>';
    }
    $html .= '</div>';

    return $html;
  }
}