console.log('✅ script.js Version 27 wurde geladen');

document.addEventListener('DOMContentLoaded', () => {
    const progress = document.querySelector('.funnel-progressbar .bar');
    const btnBack = document.getElementById('funnel-back');
    const btnNext = document.getElementById('funnel-next');

    let currentStep = 0;
    let stepHistory = [];
    let funnelPath = [];

    function getSteps() {
        return [...document.querySelectorAll('.funnel-step')];
    }

    function buildFullPath(startIndex) {
        const steps = getSteps();
        const path = [];
        const visited = new Set();
        let current = parseInt(startIndex);

        while (!visited.has(current)) {
            path.push(current);
            visited.add(current);

            const stepEl = steps.find(s => parseInt(s.dataset.step) === current);
            if (!stepEl) break;

            let next = stepEl.dataset.next;

            const selectedChoice = stepEl.querySelector('.image-choice.selected');
            if ((!next || isNaN(next)) && selectedChoice) {
                next = selectedChoice.dataset.nextStep;
            }

            if ((!next || isNaN(next)) && stepEl.querySelectorAll('.image-choice').length > 0) {
                stepEl.querySelectorAll('.image-choice').forEach(ic => {
                    if (!next && ic.dataset.nextStep) next = ic.dataset.nextStep;
                });
            }

            if (!next || isNaN(next)) break;
            current = parseInt(next);
        }

        return path;
    }

    function updateProgressbar(currentIndex) {
        const indexInPath = funnelPath.indexOf(parseInt(currentIndex));
        let percent = 0;

        if (funnelPath.length > 1 && indexInPath >= 0) {
            percent = Math.round((indexInPath / (funnelPath.length - 1)) * 100);
        }

        const currentStep = getSteps()[currentIndex];
        if (currentStep?.dataset.final === 'true') {
            percent = 100;
        }

        progress.style.width = percent + '%';
    }

    function validateCurrentStep() {
        const current = document.querySelector('.funnel-step.active');
        const requiredFields = current.querySelectorAll('[required]');
        let valid = true;

        requiredFields.forEach(field => {
            if (field.type === 'radio') {
                const radios = current.querySelectorAll(`input[type="radio"][name="${field.name}"]`);
                const oneChecked = Array.from(radios).some(r => r.checked);
                if (!oneChecked) valid = false;
            } else if (!field.value || field.value.trim() === '') {
                valid = false;
            }
        });

        btnNext.disabled = !valid;
    }

    function showStep(index) {
        const steps = getSteps();
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === index);
        });

        currentStep = index;
        updateProgressbar(index);
        if (index === 0) {
            btnNext.style.display = 'none';
        } else {
            btnNext.style.display = 'inline-block';
        }

        btnBack.style.display = stepHistory.length > 0 ? 'inline-block' : 'none';
        btnNext.textContent = steps[index]?.dataset.final === 'true' ? 'Absenden' : 'Weiter';

        // Pflichtfelder beobachten
        validateCurrentStep();
        const current = getSteps()[index];
        const inputs = current.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', validateCurrentStep);
            if (input.type === 'radio') {
                input.addEventListener('change', validateCurrentStep);
            }
        });
    }

    function goToStep(index) {
        const steps = getSteps();
        index = parseInt(index);
        if (!isNaN(index)) {
            const current = document.querySelector('.funnel-step.active');
            const currentIndex = parseInt(current?.dataset.step ?? currentStep);

            if (index !== currentIndex) {
                stepHistory.push(currentIndex);
            }

            showStep(index);
        }
    }

    btnNext.addEventListener('click', () => {
        const current = document.querySelector('.funnel-step.active');
        if (!current) return;

        if (current.dataset.final === 'true') {
            submitFunnel();
            return;
        }

        let next = current.dataset.next;

        if ((!next || isNaN(next))) {
            const selected = current.querySelector('.image-choice.selected');
            if (selected && selected.dataset.nextStep) {
                next = selected.dataset.nextStep;
            }
        }

        if (!next || isNaN(next)) {
            next = parseInt(current.dataset.step) + 1;
        }

        funnelPath = buildFullPath(0);
        goToStep(parseInt(next));
    });

    btnBack.addEventListener('click', () => {
        if (stepHistory.length > 0) {
            const previous = stepHistory.pop();
            showStep(previous);
        }
    });

    document.querySelectorAll('.image-choice-group').forEach(group => {
        group.querySelectorAll('.image-choice').forEach(choice => {
            choice.addEventListener('click', () => {
                group.querySelectorAll('.image-choice').forEach(c => c.classList.remove('selected'));
                choice.classList.add('selected');

                const radio = choice.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                const nextStep = choice.dataset.nextStep;
                if (nextStep !== undefined && nextStep !== '') {
                    funnelPath = buildFullPath(0);
                    goToStep(parseInt(nextStep));
                }
            });
        });
    });

    function submitFunnel() {
        const steps = getSteps();
        const results = [];

        steps.forEach(step => {
            const question = step.querySelector('h2')?.textContent || 'Frage';
            const inputs = step.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                let value = '';
                if (input.type === 'radio') {
                    if (!input.checked) return;
                    value = input.value;
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

        fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=funnelforms_submit&answers=' + encodeURIComponent(JSON.stringify(results))
        })
            .then(res => res.json())
            .then(data => {
                console.log('Antwort vom Server:', data);
                if (data.success) {
                    const steps = getSteps();
                    const thankYouStep = steps.find(s => s.dataset.onComplete === "true");

                    if (thankYouStep) {
                        const index = steps.indexOf(thankYouStep);
                        showStep(index);
                    } else {
                        alert("✅ Formular gesendet. (Kein Abschluss-Schritt definiert)");
                    }
                }

            })
            .catch(err => {
                console.error('Fehler beim Senden:', err);
                alert('Fehler beim Senden des Formulars.');
            });
    }

    funnelPath = buildFullPath(0);
    showStep(currentStep);
});