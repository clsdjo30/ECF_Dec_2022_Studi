import {Flipper, spring} from 'flip-toolkit'

/*
* @property {HTMLElement|null} pagination
* @property {HTMLElement|null} form
* @property {HTMLElement|null} sorting
* @property {HTMLElement|null} content
*/
export default class Filter {

    /*
    * @param {HTMLElement|null} element
    */
    constructor(element) {
        if (element === null) {
            return;
        }
        this.form = element.querySelector('.js-filter-form')
        this.sorting = element.querySelector('.js-filter-sorting');
        this.pagination = element.querySelector('.js-filter-pagination');
        this.content = element.querySelector('.js-filter-content');
        this.bindEvents();

    }

    /**
     * Ajoute les comportements aux différents éléments
     */
    bindEvents() {
        const aClickListener = e => {
            if (e.target.tagName === 'a') {
                e.preventDefault()
                this.loadUrl(e.target.getAttribute('href'))
            }
        }
        this.sorting.addEventListener('click', aClickListener)
        this.pagination.addEventListener('click', aClickListener)
        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this))
        })
    }

    async loadForm() {
        const data = new FormData(this.form)
        const url = new URL(this.form.getAttribute('action') || window.location.href)
        const params = new URLSearchParams()
        data.forEach((value, key) => {
            params.append(key, value)
        })
        console.log(params)
        return this.loadUrl(url.pathname + '?' + params.toString())
    }

    async loadUrl (url) {
        const ajaxUrl = url + '&ajax=1'

        const response = await fetch(ajaxUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json()
            this.sorting.innerHTML = data.sorting
            this.flipContent(data.content)
            this.pagination.innerHTML = data.pagination
            history.replaceState({}, '', url)
        } else {
            console.error(response)
        }

    }

    /**
     *Remplace les cartes avec une animation flip
     * @param {string} content
     */
    flipContent (content) {
        const springOptions = 'wobbly'
        const exitSpring = function (element,index, onComplete) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0,-100],
                    opacity: [1, 0]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            });
        }
        const appearSpring = function (element,index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [100,0],
                    opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 15
            });
        }

        const flipper = new Flipper({
            element: this.content
        })
        this.content.children.forEach(element => {
            flipper.addFlipped({
                element,
                spring: springOptions,
                flipId: element.id,
                shouldFlip: false,
                onExit:exitSpring
            })
        })
        flipper.recordBeforeUpdate()
        this.content.innerHTML = content;
        this.content.children.forEach(element => {
            flipper.addFlipped({
                element,
                spring: springOptions,
                flipId: element.id,
                onAppear: appearSpring
            })
        })
        flipper.update()
    }


}