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
        this.slideToClose()
    }

    postloadActions() {
        // Agregar evento de clic a los botones de variación
        const variationRows = document.querySelectorAll('.variation-row')
        if (variationRows.length > 0) {
            variationRows.forEach(row => {
                const options = row.querySelectorAll('.qpd-attr')
                
                options.forEach(option => {
                    option.addEventListener('click', (evt) => {
                        options.forEach(opt => opt.classList.remove('selected'))
                        option.classList.add('selected')
                        this.checkVariationSelection()
                    })
                })
            })
        }

        // Inicializar el slider de la galería
        this.initGallery()
    }

    checkVariationSelection() {
        const selections = {};
        const variationRows = document.querySelectorAll('.variation-row');
        let allSelected = true;

        // Recolectar las selecciones del usuario
        variationRows.forEach(row => {
            const selectedOption = row.querySelector('.qpd-attr.selected');
            if (selectedOption) {
                const attributeName = selectedOption.getAttribute('atr-name');
                selections[attributeName] = selectedOption.value;
            } else {
                allSelected = false;
            }
        });

        if (allSelected) {
            // Obtener el JSON de variaciones generado por WooCommerce
            const variationData = JSON.parse(document.getElementById('variation-map').value);

            // Buscar la variación que coincida con las selecciones
            const matchingVariation = variationData.find(variation => {
                return Object.entries(variation.attributes).every(([key, value]) => {
                    return selections[key] === value;
                });
            });

            if (matchingVariation) {
                // Actualizar precio
                document.querySelector('.price').innerHTML = matchingVariation.price_html;

                // Actualizar stock
                document.querySelector('.in-stock').textContent = 
                    matchingVariation.is_in_stock ? 'En stock' : 'Sin stock';

                // Actualizar descripción si existe
                if (matchingVariation.variation_description) {
                    document.querySelector('.variation-description').innerHTML = matchingVariation.variation_description;
                }

                // Actualizar imagen si existe
                if (matchingVariation.image && matchingVariation.image.src) {
                    document.querySelector('.product-gallery img.active').src = matchingVariation.image.src;
                }

                // Guardar el ID de la variación seleccionada para agregar al carrito
                document.querySelector('.apd-add-to-cart').setAttribute('data-variation-id', matchingVariation.variation_id);
            }
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

    setContent(content) {
        document.querySelector('.apd-modal .loading').classList.add('d-none');
        document.querySelector('.apd-content-container').innerHTML = content;
        this.postloadActions();
    }

    addToCart(product_id, quantity, variation_id = null) {

    }

    variationSelection(variation_id) {

    }

    preloadAnimation() {
        const clientWidth = document.body.clientWidth
        document.querySelector('.apd-modal .loading').classList.remove('d-none')
        jQuery('.apd-overlay').removeClass('apd-hidden')
        jQuery('body').css('overflow', 'hidden')

        if (clientWidth <= 600) {
            jQuery('.apd-modal').css('bottom', '-100px')
            jQuery('.apd-modal').animate({
                bottom: '0px'
            }, 200)
        }
    }

    postloadAnimation() {

    }

    slideToClose() {
        const handler = document.querySelector('.close-handler');
        const modal = document.querySelector('.apd-modal');
        let startY = 0;
        let currentY = 0;
        let isDragging = false;

        const handleTouchStart = (e) => {
            startY = e.touches[0].clientY;
            isDragging = true;
            modal.style.transition = 'none';
        };

        const handleTouchMove = (e) => {
            if (!isDragging) return;

            currentY = e.touches[0].clientY;
            const diffY = currentY - startY;

            if (diffY > 0) {
                e.preventDefault();
                modal.style.transform = `translateY(${diffY}px)`;
            }
        };

        const handleTouchEnd = (e) => {
            if (!isDragging) return;

            const diffY = currentY - startY;
            modal.style.transition = 'transform 0.3s ease-out';
            isDragging = false;

            if (diffY > 100) {
                e.preventDefault();
                e.stopPropagation();
                modal.style.transform = 'translateY(100%)';
                setTimeout(() => {
                    this.closeEffectMobile();
                }, 300);
            } else {
                modal.style.transform = '';
            }
        };

        handler.addEventListener('touchstart', handleTouchStart, { passive: false });
        handler.addEventListener('touchmove', handleTouchMove, { passive: false });
        handler.addEventListener('touchend', handleTouchEnd);
    }

    groupedCloseActions() {
        const overlay = document.querySelector('.apd-overlay');
        const closeBtn = document.querySelector('.modal-close');
        const handler = document.querySelector('.close-handler');

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                this.closeModal();
            }
        });

        closeBtn.addEventListener('click', () => {
            this.closeModal();
        });

        if (window.innerWidth > 576) {
            handler.addEventListener('click', () => {
                this.closeModal();
            });
        }
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

    closeEffectMobile() {
        jQuery('.apd-modal').animate({
            bottom: '-1000px'
        }, 500, function () {
            document.querySelector('.apd-overlay').classList.add('apd-hidden')
            document.querySelector('.apd-content-container').innerHTML = ''
            jQuery('body').css('overflow', 'auto')
            document.querySelector('.apd-modal').removeAttribute('style')
        })
    }

    initGallery() {
        const slider = document.querySelector('.gallery-slider');
        const slides = slider.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.nav-dot');
        let currentSlide = 0;
        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;

        // Función para actualizar la posición del slider
        const setSliderPosition = (position) => {
            slider.style.transform = `translateX(${position}px)`;
        };

        // Función para cambiar slide
        const goToSlide = (index) => {
            currentSlide = index;
            // Usar el ancho del contenedor en lugar del ancho de la ventana
            const containerWidth = slider.parentElement.offsetWidth;
            currentTranslate = -containerWidth * currentSlide;
            prevTranslate = currentTranslate;
            
            setSliderPosition(currentTranslate);
            
            // Actualizar dots
            dots.forEach(dot => dot.classList.remove('active'));
            dots[index].classList.add('active');
        };

        // Eventos táctiles
        slider.addEventListener('touchstart', (e) => {
            isDragging = true;
            startPos = e.touches[0].clientX;
            slider.style.transition = 'none';
        }, {passive: true});

        slider.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            
            const currentPosition = e.touches[0].clientX;
            const diff = currentPosition - startPos;
            currentTranslate = prevTranslate + diff;
            
            setSliderPosition(currentTranslate);
        }, {passive: true});

        slider.addEventListener('touchend', () => {
            isDragging = false;
            slider.style.transition = 'transform 0.3s ease-in-out';
            
            // Calcular el índice más cercano basado en el deslizamiento
            const movedBy = currentTranslate - prevTranslate;
            
            if (Math.abs(movedBy) > 100) {
                if (movedBy < 0 && currentSlide < slides.length - 1) {
                    currentSlide += 1;
                } else if (movedBy > 0 && currentSlide > 0) {
                    currentSlide -= 1;
                }
            }
            
            goToSlide(currentSlide);
        });

        // Eventos para los dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });

        // Prevenir arrastre de imágenes
        slider.addEventListener('dragstart', (e) => e.preventDefault());
    }
}

const QPD = new QuickProductDetails()
QPD.loadActions()