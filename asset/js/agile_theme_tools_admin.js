// Manage admin related interactions for ccustom blocks

(function($) {
    $(document).ready(function() {

        $('body').on('click', '#add-html-with-alternate-alternate' ,function(e){
            e.preventDefault();

            // count the existing
            const numalternates = $('.html-with-alternate-wrapper:not(.html-alternate-templates) select').length;
            console.log(numalternates)
            const nextId = numalternates + 1;

            let newalternate = $('#html-with-alternate-templates-wrapper > .html-alternate-templates').clone();
            newalternate.html(newalternate.html().replace(/{idx}/gi, nextId))
            newalternate.appendTo('#html-with-alternates')

            // initialize ckeditor
            const ckId = 'alternate_html_' + nextId
            CKEDITOR.inline(ckId)
        });

        $('body').on('click', '.html-with-alternate-remove-alternate', function(e) {
            e.preventDefault()
            $(this).closest('.alternates-grid').remove()
            $(this).closest('.html-alternate-templates').remove()
            // rename all form names and ids so that adding new alternates doesn't result
            // in misnumbering
            $('.alternates-grid:not(.alternates-grid-templates').each(function(idx, el,) {
                const index = idx + 1
                const selectEl = $(el).find('select[id*="[o:data][alternate_html_language_"]')
                selectEl.attr({
                    'id' : selectEl.attr('id').replace(/alternate_html_language_[0-9]+/i, 'alternate_html_language_' + index),
                    'name' : selectEl.attr('name').replace(/alternate_html_language_[0-9]+/i, 'alternate_html_language_' + index)
                })
                const typeEl = $(el).find('textarea[id*="[o:data][alternate_html_type"]')
                alternateEl.attr({
                    'id' : alternateEl.attr('id').replace(/alternate_html_type_[0-9]+/i, 'alternate_html_type' + index),
                    'name' : alternateEl.attr('name').replace(/alternate_html_type_[0-9]+/i, 'alternate_html_type' + index)
                }) 
                const alternateEl = $(el).find('textarea[id*="[o:data][alternate_html_"]')
                alternateEl.attr({
                    'id' : alternateEl.attr('id').replace(/alternate_html_[0-9]+/i, 'alternate_html_' + index),
                    'name' : alternateEl.attr('name').replace(/alternate_html_[0-9]+/i, 'alternate_html_' + index)
                }) 
            })
        })
    });
})(jQuery);