document.addEventListener('DOMContentLoaded', () => {
    const countrySelect = document.getElementById('country_code');
    const taxTypeSelect = document.getElementById('tax_number_type');
    // Скрипт работает только на странице создания компании.
    if (!countrySelect || !taxTypeSelect) {
        return;
    }
    const countries = JSON.parse(countrySelect.dataset.countries || '{}');
    const oldTaxType = countrySelect.dataset.oldTaxType || '';
    /**
     * Обновляет список типов налоговых номеров
     * в зависимости от выбранной страны.
     */
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
    // Восстанавливаем ранее выбранный тип после ошибки валидации.
    updateTaxTypes(oldTaxType);
});