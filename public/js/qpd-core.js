function qpd_trigger(product_id) {

}

class QuickProductDetails {

    constructor() {

    }

    loadActions() {
        const QpdTriggers = document.querySelectorAll('.apd-quick-view')

        QpdTriggers.forEach(trigger => (
            trigger.addEventListener('click', async (evt) => {
                evt.preventDefault()
                this.preloadAnimation()
                const product_id    = trigger.getAttribute('product-id')
                const the_content   = await this.getProductContent(product_id)
                if (the_content === false) return
                this.setContent(the_content)
            })
        ))

        this.groupedCloseActions()
    }

    async getProductContent(product_id) {
        if (!product_id || isNaN(product_id) || product_id <= 0) return false

        try {
            const formData = new FormData();
            formData.append('action', 'qpd_get_product');
            formData.append('product_id', product_id);

            const promise = await fetch(qpd_site_config.ajax_url, {
                method: 'POST',
                body: formData
            });

            const contents = await promise.text()
            return contents
        } catch (error) {
            console.error("El error", error);
            return false;
        }
    }

    setContent( content ) {
        document.querySelector('.apd-modal .loading').classList.add('d-none')
        document.querySelector('.apd-content-container').innerHTML = content
    }

    addToCart( product_id, quantity, variation_id ) {

    }

    variationSelection( variation_id ) {

    }

    preloadAnimation() {
        const clientWidth = document.body.clientWidth
        document.querySelector('.apd-modal .loading').classList.remove('d-none')
        jQuery( '.apd-overlay' ).removeClass('apd-hidden')
        jQuery('body').css('overflow', 'hidden')

        if ( clientWidth <= 600 ) {
            jQuery( '.apd-modal' ).css('bottom', '-100px')
            jQuery( '.apd-modal' ).animate({
                bottom: '0px'
            }, 200)
        }
    }

    postloadAnimation() {
        
    }

    groupedCloseActions() {
        const overlay = document.querySelector('.apd-overlay');
        const modal = document.querySelector('.apd-modal');

        overlay.addEventListener( 'click', (e) => {
            if ( e.target === overlay)
            this.closeModal()
        })
        document.querySelector('.modal-close').addEventListener( 'click', () => {
            this.closeModal()
        })
        document.querySelector('.close-handler').addEventListener( 'click', () => {
            this.closeModal()
        })
    }

    closeModal() {
        document.querySelector('.apd-overlay').classList.add('apd-hidden')
        document.querySelector('.apd-content-container').innerHTML = ''
        jQuery('body').css('overflow', 'auto')
    }

}

const QPD = new QuickProductDetails()
QPD.loadActions()