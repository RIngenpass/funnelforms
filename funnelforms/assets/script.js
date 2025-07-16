console.log('script.js wurde geladen');
document.addEventListener('DOMContentLoaded', () => {
    const steps = document.querySelectorAll('.funnel-step');
    const progress = document.querySelector('.funnel-progressbar .bar');
    const btnBack = document.getElementById('funnel-back');
    const btnNext = document.getElementById('funnel-next');

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            console.log('BtnNext wurde geklickt');
        });
    }


    let currentStep = 0;

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === index);
        });

        currentStep = index;
        progress.style.width = ((index + 1) / steps.length * 100) + '%';
        btnBack.style.display = index > 0 ? 'inline-block' : 'none';
        btnNext.textContent = (index === steps.length - 1) ? 'Absenden' : 'Weiter';
    }

    function goToStep(targetIndex) {
        targetIndex = parseInt(targetIndex);
        if (!isNaN(targetIndex) && steps[targetIndex]) {
            showStep(targetIndex);
        }
    }

    btnNext.addEventListener('click', () => {
        const current = steps[currentStep];
        const inputs = current.querySelectorAll('input, select, textarea');
        let valid = true;

        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value) {
                input.classList.add('funnel-error');
                valid = false;
            } else {
                input.classList.remove('funnel-error');
            }
        });

        if (!valid) {
            alert('Bitte alle Pflichtfelder ausfüllen.');
            return;
        }

        if (currentStep === steps.length - 1) {
            submitFunnel();
        } else {
            // Hat der aktuelle Step einen expliziten next?
            const customNext = current.dataset.next;
            if (customNext !== '') {
                goToStep(customNext);
            } else {
                showStep(currentStep + 1);
            }
        }
    });

    btnBack.addEventListener('click', () => {
        if (currentStep > 0) {
            showStep(currentStep - 1);
        }
    });

    // Klick auf Bild
    document.querySelectorAll('.image-choice-group .image-choice').forEach(choice => {
        choice.addEventListener('click', () => {
            const group = choice.closest('.image-choice-group');
            const input = choice.querySelector('input[type="radio"]');

            if (input) {
                input.checked = true;
            }

            group.querySelectorAll('.image-choice').forEach(c => c.classList.remove('selected'));
            choice.classList.add('selected');

            const targetStep = choice.dataset.nextStep;
            if (targetStep !== '') {
                goToStep(targetStep);
            }
        });
    });

    function submitFunnel() {
        const results = [];
        steps.forEach(step => {
            const question = step.querySelector('h2')?.textContent || 'Frage';
            const inputs = step.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                let value = '';

                if (input.type === 'radio') {
                    if (input.checked) value = input.value;
                    else return;
                } else {
                    value = input.value;
                }

                results.push({
                    label: input.name || 'feld',
                    question: question,
                    answer: value
                });
            });
        });

        fetch('/wp-admin/admin-ajax.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=funnelforms_submit&answers=' + encodeURIComponent(JSON.stringify(results))
        })
            .then(res => res.json())
            .then(data => {
                alert('Vielen Dank! Ihre Daten wurden gesendet.');
                // Weiterleitung oder Reset hier möglich
            })
            .catch(() => alert('Fehler beim Senden des Formulars.'));
    }

    // Initial anzeigen
    showStep(currentStep);
});
