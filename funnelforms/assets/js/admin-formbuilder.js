console.log('✅ FunnelBuilder JS wurde geladen');

document.addEventListener('DOMContentLoaded', () => {
    const saved = document.getElementById('funnel-json')?.value;
    const data = saved
        ? JSON.parse(saved).map(step => ({
            ...step,
            elements: Array.isArray(step.elements) ? step.elements : []
        }))
        : [];

    window.funnelApp = new Vue({
        el: '#funnelformbuilderapp',
        template: '#funnelformbuilder-template',
        data: {
            steps: data
        },
        methods: {
            addStep() {
                this.steps.push({
                    title: '',
                    elements: [],
                    next_step: null,
                    is_final: false,
                    on_complete: false
                });
                this.normalizeSteps();
            },

            moveStepUp(index) {
                if (index > 0) {
                    const temp = this.steps[index];
                    this.steps.splice(index, 1);
                    this.steps.splice(index - 1, 0, temp);
                    this.normalizeSteps();
                    this.saveAfterMove();
                }
            },

            moveStepDown(index) {
                if (index < this.steps.length - 1) {
                    const temp = this.steps[index];
                    this.steps.splice(index, 1);
                    this.steps.splice(index + 1, 0, temp);
                    this.normalizeSteps();
                    this.saveAfterMove();
                }
            },

            normalizeSteps() {
                this.steps.forEach((step, index) => {
                    step.key = 'step_' + index;
                    step.next_step = index < this.steps.length - 1 ? index + 1 : null;
                    step.is_final = (index === this.steps.length - 1);
                });
            },

            saveAfterMove() {
                if (typeof generateFunnelJSON === 'function') {
                    generateFunnelJSON(); // aktualisiert #funnel-json
                }

                const formId = document.querySelector('[name="form_id"]')?.value || '';
                const data = document.getElementById('funnel-json')?.value;

                if (formId && data) {
                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'funnelforms_save_json',
                            form_id: formId,
                            data: data
                        })
                    })
                        .then(res => res.json())
                        .then(response => {
                            if (response.success) {
                                console.log('✅ Schritte erfolgreich gespeichert');
                            } else {
                                console.warn('⚠️ Fehler beim Speichern:', response);
                            }
                        })
                        .catch(err => console.error('❌ AJAX Fehler:', err));
                }
            },

            confirmDeleteStep(index) {
                if (confirm('Diesen Schritt wirklich löschen?')) {
                    this.steps.splice(index, 1);
                    this.normalizeSteps();
                    this.saveAfterMove();
                }
            },

            addElement(step) {
                if (!Array.isArray(step.elements)) {
                    this.$set(step, 'elements', []);
                }

                const newField = {
                    type: 'image-choice',
                    label: '',
                    name: 'feld_' + step.title.toLowerCase().replace(/\s+/g, '_') + '_image',
                    options: [{
                        label: '',
                        src: '',
                        next: null
                    }],
                    shortcode: '',
                    dynamic: false
                };

                step.elements.push(newField);
            },

            addImageOption(step) {
                if (!Array.isArray(step.elements)) {
                    this.$set(step, 'elements', []);
                }

                let imageChoice = step.elements.find(el => el.type === 'image-choice');

                if (!imageChoice) {
                    imageChoice = {
                        type: 'image-choice',
                        label: '',
                        name: 'feld_' + step.title.toLowerCase().replace(/\s+/g, '_') + '_image',
                        options: [],
                        shortcode: '',
                        dynamic: false
                    };
                    step.elements.push(imageChoice);
                }

                imageChoice.options.push({
                    label: '',
                    src: '',
                    next: ''
                });
            },

            addOption(el) {
                if (!el.options) this.$set(el, 'options', []);
                el.options.push({
                    label: '',
                    src: '',
                    next: null
                });
            }
            ,

            onFieldTypeChange(el) {
                if (el.type === 'image-choice') {
                    if (!el.options || el.options.length === 0) {
                        this.$set(el, 'options', [{
                            label: '',
                            src: '',
                            next: null
                        }]);
                    }
                } else if (el.type !== 'select') {
                    if (el.options) {
                        this.$set(el, 'options', []);
                    }
                }
            },

        mounted() {
            this.normalizeSteps();
        }
    }});

    // Exportfunktion zur Speicherung in DB
    window.generateFunnelJSON = function () {
        const textarea = document.getElementById('funnel-json');

        const cleanSteps = window.funnelApp.steps.map((step, sIndex) => {
            return {
                title: step.title,
                key: 'step_' + sIndex,
                next_step: step.next_step,
                is_final: !!step.is_final,
                on_complete: !!step.on_complete,
                elements: step.elements.map((el, eIndex) => {
                    // Name generieren, wenn leer
                    if (!el.name || el.name.trim() === '') {
                        el.name = 'feld_' + step.title.toLowerCase().replace(/\s+/g, '_') + '_' + eIndex;
                    }

                    // KEIN automatisches label setzen für info-text oder andere
                    if ((el.type === 'contact7' || el.type === 'select') && el.shortcode) {
                        el.shortcode = el.shortcode.replace(/\\"/g, '"').replace(/"/g, '\\"');
                    }

                    return {
                        type: el.type,
                        label: el.label || '',
                        name: el.name,
                        options: el.options || [],
                        shortcode: el.shortcode || '',
                        dynamic: !!el.dynamic,
                        src: el.src || '',
                        required: !!el.required // ✅ Pflichtfeld speichern
                    };

                })
            };
        });

        textarea.value = JSON.stringify(cleanSteps, null, 2);
        console.log('📦 Generiertes JSON:', cleanSteps);
    };
});
