document.addEventListener('DOMContentLoaded', () => {
    const countrySelect = document.getElementById('country_code');
    const taxTypeSelect = document.getElementById('tax_number_type');

    if (countrySelect && taxTypeSelect) {
        const countries = JSON.parse(countrySelect.dataset.countries || '{}');
        const oldTaxType = countrySelect.dataset.oldTaxType || '';

        function updateTaxTypes(selectedType = '') {
            const countryCode = countrySelect.value;
            const identifiers = countries[countryCode]?.tax_identifiers ?? {};
            taxTypeSelect.replaceChildren();
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = countryCode ? 'Выберите тип налогового номера' : 'Сначала выберите страну';
            taxTypeSelect.appendChild(defaultOption);
            Object.values(identifiers).forEach((label) => {
                const option = document.createElement('option');
                option.value = label;
                option.textContent = label;
                option.selected = label === selectedType;
                taxTypeSelect.appendChild(option);
            });
        }

        countrySelect.addEventListener('change', () => {
            updateTaxTypes();
        });
        updateTaxTypes(oldTaxType);
    }

    const clientBaseToggle = document.getElementById('clientBaseToggle');
    const clientBaseSubmenu = document.getElementById('clientBaseSubmenu');
    if (clientBaseToggle && clientBaseSubmenu) {
        clientBaseToggle.addEventListener('click', () => {
            const isExpanded = clientBaseToggle.getAttribute('aria-expanded') === 'true';
            clientBaseToggle.setAttribute('aria-expanded', String(!isExpanded));
            clientBaseSubmenu.hidden = isExpanded;
        });
    }

});
