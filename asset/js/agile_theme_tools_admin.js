// Manage admin related interactions for ccustom blocks

(function($) {
    $(document).ready(function() {
        const removeTranslationTemplate = '<button class="html-with-translation-remove-translation">Remove</button>'

        $('body').on('click', '#add-html-with-translation-translation' ,function(e){
            e.preventDefault();

            // count the existing
            const numTranslations = $('#html-with-translation-wrapper select').length;
            const nextId = numTranslations + 1;
            const removeButton = $(removeTranslationTemplate).clone().attr('id','html-with-translation-remove-translation-' + nextId)

            let newTranslation = $('#html-with-translation-templates-wrapper .html-with-translation-templates').clone();
            newTranslation.html(newTranslation.html().replace(/{idx}/gi, nextId))
            removeButton.prependTo(newTranslation)
            newTranslation.appendTo('#html-with-translation-wrapper')

            // initialize ckeditor
            CKEDITOR.replace('translation_html_' + nextId)
        });

        $('body').on('click', '#remove-html-with-translation-translation', function(e) {
            e.preventDefault()
            const idToRemove = $(this).attr('id').replace('html-with-translation-remove-translation-','')
        })
    });
})(jQuery);