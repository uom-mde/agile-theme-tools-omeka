// Manage admin related interactions for ccustom blocks

(function($) {
    $(document).ready(function() {

        $('body').on('click', '#add-html-with-translation-translation' ,function(e){
            e.preventDefault();

            // count the existing
            const numTranslations = $('.html-with-translation-wrapper:not(.html-translation-templates) select').length;
            console.log(numTranslations)
            const nextId = numTranslations + 1;
            //const removeButton = $(removeTranslationTemplate).clone().attr('id','html-with-translation-remove-translation-' + nextId)

            let newTranslation = $('#html-with-translation-templates-wrapper > .html-translation-templates').clone();
            newTranslation.html(newTranslation.html().replace(/{idx}/gi, nextId))
            newTranslation.appendTo('#html-with-translations')

            // initialize ckeditor
            const ckId = 'translation_html_' + nextId
            CKEDITOR.inline(ckId)
        });

        $('body').on('click', '.html-with-translation-remove-translation', function(e) {
            e.preventDefault()
            $(this).closest('.translations-grid').remove()
            $(this).closest('.html-translation-templates').remove()
            // rename all form names and ids so that adding new translations doesn't result
            // in misnumbering
            $('.translations-grid:not(.translations-grid-templates').each(function(idx, el,) {
                const index = idx + 1
                const selectEl = $(el).find('select[id*="[o:data][translation_html_language_"]')
                selectEl.attr({
                    'id' : selectEl.attr('id').replace(/translation_html_language_[0-9]+/i, 'translation_html_language_' + index),
                    'name' : selectEl.attr('name').replace(/translation_html_language_[0-9]+/i, 'translation_html_language_' + index)
                })
                const translationEl = $(el).find('textarea[id*="[o:data][translation_html_"]')
                translationEl.attr({
                    'id' : translationEl.attr('id').replace(/translation_html_[0-9]+/i, 'translation_html_' + index),
                    'name' : translationEl.attr('name').replace(/translation_html_[0-9]+/i, 'translation_html_' + index)
                }) 
            })
        })
    });
})(jQuery);