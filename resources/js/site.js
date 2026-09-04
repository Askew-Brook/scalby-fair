document.querySelectorAll('[data-stall-booking-form]').forEach((form) => {
    const quantityInputs = form.querySelectorAll('[data-stall-booking-item]');
    const total = form.querySelector('[data-stall-booking-total]');

    if (!total) return;

    const updateTotal = () => {
        const totalPence = Array.from(quantityInputs).reduce((sum, input) => {
            const quantity = Math.max(0, Number.parseInt(input.value || '0', 10));
            const unitAmount = Number.parseInt(input.dataset.unitAmount || '0', 10);

            return sum + (quantity * unitAmount);
        }, 0);

        total.textContent = new Intl.NumberFormat('en-GB', {
            style: 'currency',
            currency: 'GBP',
        }).format(totalPence / 100);
    };

    quantityInputs.forEach((input) => input.addEventListener('input', updateTotal));
    updateTotal();
});

document.querySelectorAll('[data-walk-booking-form]').forEach((form) => {
    const prices = {
        adult: Number.parseInt(form.dataset.adultPrice || '0', 10),
        junior: Number.parseInt(form.dataset.juniorPrice || '0', 10),
    };
    const total = form.querySelector('[data-walk-booking-total]');
    const donation = form.querySelector('[data-walk-donation]');
    let nextIndex = 100;
    const maximumPerGroup = 10;

    const walkerRows = (type) => form.querySelectorAll(`[data-walker-list="${type}"] [data-walker-row]`);
    const dogRows = () => form.querySelectorAll('[data-dog-list] [data-dog-row]');

    const updateSummary = () => {
        const adultCount = walkerRows('adult').length;
        const juniorCount = walkerRows('junior').length;
        const donationPence = Math.max(0, Math.round(Number.parseFloat(donation?.value || '0') * 100) || 0);
        const totalPence = (adultCount * prices.adult) + (juniorCount * prices.junior) + donationPence;

        form.querySelectorAll('[data-walker-count]').forEach((element) => {
            element.textContent = String(element.dataset.walkerCount === 'adult' ? adultCount : juniorCount);
        });

        const dogCount = form.querySelector('[data-dog-count]');
        if (dogCount) dogCount.textContent = String(dogRows().length);

        if (total) {
            total.textContent = new Intl.NumberFormat('en-GB', {
                style: 'currency',
                currency: 'GBP',
            }).format(totalPence / 100);
        }

        ['adult', 'junior'].forEach((type) => {
            walkerRows(type).forEach((row, index) => {
                const label = row.querySelector('[data-walker-label]');
                if (label) label.textContent = `${type === 'adult' ? 'Adult' : 'Under-18'} walker ${index + 1}`;
            });

            const addButton = form.querySelector(`[data-add-walker="${type}"]`);
            if (addButton) addButton.disabled = walkerRows(type).length >= maximumPerGroup;
        });

        dogRows().forEach((row, index) => {
            const label = row.querySelector('[data-dog-label]');
            if (label) label.textContent = `Dog ${index + 1}`;
        });

        const addDogButton = form.querySelector('[data-add-dog]');
        if (addDogButton) addDogButton.disabled = dogRows().length >= maximumPerGroup;
    };

    form.querySelectorAll('[data-add-walker]').forEach((button) => {
        button.addEventListener('click', () => {
            const type = button.dataset.addWalker;
            const list = form.querySelector(`[data-walker-list="${type}"]`);
            const template = form.querySelector(`[data-walker-template="${type}"]`);

            if (!list || !template || walkerRows(type).length >= maximumPerGroup) return;

            const wrapper = document.createElement('div');
            wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
            nextIndex += 1;

            const row = wrapper.firstElementChild;
            if (!row) return;

            list.append(row);
            row.querySelector('input, select')?.focus();
            updateSummary();
        });
    });

    form.querySelector('[data-add-dog]')?.addEventListener('click', () => {
        const list = form.querySelector('[data-dog-list]');
        const template = form.querySelector('[data-dog-template]');

        if (!list || !template || dogRows().length >= maximumPerGroup) return;

        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
        nextIndex += 1;

        const row = wrapper.firstElementChild;
        if (!row) return;

        list.append(row);
        row.querySelector('input')?.focus();
        updateSummary();
    });

    form.addEventListener('click', (event) => {
        const button = event.target instanceof Element ? event.target.closest('[data-remove-walker]') : null;
        if (!button) return;

        button.closest('[data-walker-row]')?.remove();
        updateSummary();
    });

    form.addEventListener('click', (event) => {
        const button = event.target instanceof Element ? event.target.closest('[data-remove-dog]') : null;
        if (!button) return;

        button.closest('[data-dog-row]')?.remove();
        updateSummary();
    });

    donation?.addEventListener('input', updateSummary);
    updateSummary();
});
