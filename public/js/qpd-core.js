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

    postloadActions() {
        const att_options = document.querySelectorAll('.qpd-attr')

        if (att_options.length > 0) {
            att_options.forEach(item => {
                item.addEventListener('click', (evt) => {
                    item.classList.toggle('selected')
                })
            })
        }

        const variationRows = document.querySelectorAll('.variation-row')

        if (variationRows.length > 0) {
            variationRows.forEach(row => {
                const options = row.querySelectorAll('.qpd-attr')
                
                options.forEach(option => {
                    option.addEventListener('click', (evt) => {
                        // Remover 'selected' de todos los botones del mismo atributo
                        options.forEach(opt => opt.classList.remove('selected'))
                        // Agregar 'selected' solo al botón clickeado
                        option.classList.add('selected')
                        
                        // Opcional: Verificar si todos los atributos están seleccionados
                        this.checkVariationSelection()
                    })
                })
            })
        }
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
        this.postloadActions()
    }

    addToCart( product_id, quantity, variation_id = null ) {

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
        if (window.innerWidth <= 576) {
            this.closeEffectMobile()
        } else {
            document.querySelector('.apd-overlay').classList.add('apd-hidden')
            document.querySelector('.apd-content-container').innerHTML = ''
            jQuery('body').css('overflow', 'auto')
        }
    }

    // Método adicional para verificar selecciones completas
    checkVariationSelection() {
        const variationRows = document.querySelectorAll('.variation-row')
        const selections = {}
        let allSelected = true

        variationRows.forEach(row => {
            const attributeName = row.querySelector('span').textContent.replace(':', '')
            const selectedOption = row.querySelector('.qpd-attr.selected')
            
            if (selectedOption) {
                selections[attributeName] = selectedOption.value
            } else {
                allSelected = false
            }
        })

        if (allSelected) {
            console.log('Selecciones completas:', selections)
            // Aquí puedes llamar a otro método para manejar la variación seleccionada
            // this.variationSelection(selections)
        }
    }

    closeEffectMobile() {
        jQuery('.apd-modal').animate({
            bottom: '-1000px'
        }, 500, function() {
            document.querySelector('.apd-overlay').classList.add('apd-hidden')
            document.querySelector('.apd-content-container').innerHTML = ''
            jQuery('body').css('overflow', 'auto')
            document.querySelector('.apd-modal').removeAttribute('style')
        })
    }

}

const QPD = new QuickProductDetails()
QPD.loadActions()