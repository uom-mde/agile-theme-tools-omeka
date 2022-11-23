class HtmlAlternate {
    constructor(blockId) {
        const self = this
        this.alternatesDiv = $('#' + blockId)
        this.ariaLive = this.alternatesDiv.find('.alternate-html-block-live')
        this.alternatesDiv.find('select').on('change', function() {
            const activeAlternate = $(this).val()
            const currentView = $(this).closest('div[class^=alternate-html-elements]')
            self.swapVisibleAlternate(activeAlternate, currentView)
            self.updateAria()
        })
    }

    updateAria() {
        this.ariaLive.html('Switched to new alternate')
    }

    swapVisibleAlternate(activeAlternate, currentView) {
        currentView.find('[class^=alternate-option-]').removeClass('active')
        currentView.find('.' + activeAlternate).addClass('active')
    }
}