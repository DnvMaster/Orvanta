document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('contact-phone-search');
    const searchButton = document.getElementById('contact-phone-search-button');
    const status = document.getElementById('contact-search-status');
    const contactSelect = document.getElementById('deal-contact');
    const companySelect = document.getElementById('deal-company');
    const quickForm = document.getElementById('quick-contact-form');
    const lastNameInput = document.getElementById('quick-contact-last-name');
    const firstNameInput = document.getElementById('quick-contact-first-name');
    const middleNameInput = document.getElementById('quick-contact-middle-name');
    const quickPhoneInput = document.getElementById('quick-contact-phone');
    const saveButton = document.getElementById('quick-contact-save');
    const cancelButton = document.getElementById('quick-contact-cancel');
    const quickStatus = document.getElementById('quick-contact-status');
    if (!phoneInput || !searchButton || !status || !contactSelect || !companySelect || !quickForm || !lastNameInput || !firstNameInput || !middleNameInput || !quickPhoneInput || !saveButton || !cancelButton || !quickStatus) {
        return;
    }
    function hideQuickForm() {
        quickForm.hidden = true;
        quickStatus.textContent = '';
    }
    function showSuccessMessage(message) {
        status.textContent = message;
        status.hidden = false;
    }
    function showQuickForm(phone) {
        quickForm.hidden = false;
        quickPhoneInput.value = phone;
        quickStatus.textContent = '';
    }
    searchButton.addEventListener('click', async function () {
        const phone = phoneInput.value.trim();
        status.textContent = '';
        hideQuickForm();
        if (!phone) {
            status.textContent = 'Введите номер телефона.';
            return;
        }
        searchButton.disabled = true;
        searchButton.textContent = 'Поиск...';
        try {
            const url = new URL(searchButton.dataset.searchUrl, window.location.origin);
            url.searchParams.set('phone', phone);
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) {
                throw new Error('Не удалось выполнить поиск.');
            }
            const data = await response.json();
            if (!data.supported) {
                status.textContent = 'Поддерживаются только российские и белорусские номера.';
                return;
            }
            if (!data.matches.length) {
                status.textContent = 'Контакт не найден. Вы можете создать новый контакт.';
                showQuickForm(phone);
                return;
            }
            data.matches.forEach(function (contact) {
                const fullName = [
                    contact.last_name,
                    contact.first_name,
                    contact.middle_name,
                ].filter(Boolean).join(' ');

                const label = [
                    fullName,
                    contact.company,
                    contact.phone,
                ].filter(Boolean).join(' — ');

                let option = Array.from(contactSelect.options).find(function (item) {
                    return item.value === String(contact.id);
                });

                if (!option) {
                    option = document.createElement('option');
                    option.value = String(contact.id);
                    contactSelect.appendChild(option);
                }

                option.textContent = label;
                option.dataset.companyId = contact.company_id || '';
            });

            status.textContent =
                'Найдено контактов: ' + data.matches.length +
                '. Выберите контакт в списке ниже.';
        } catch (error) {
            status.textContent = error.message || 'Ошибка при поиске контакта.';
        } finally {
            searchButton.disabled = false;
            searchButton.textContent = 'Найти';
        }
    });

    saveButton.addEventListener('click', async function () {
        quickStatus.textContent = '';

        if (!lastNameInput.value.trim() || !firstNameInput.value.trim()) {
            quickStatus.textContent = 'Заполните фамилию и имя.';
            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = 'Сохранение...';

        try {
            const response = await fetch(saveButton.dataset.storeUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    company_id: companySelect.value || null,
                    last_name: lastNameInput.value.trim(),
                    first_name: firstNameInput.value.trim(),
                    middle_name: middleNameInput.value.trim() || null,
                    phone: quickPhoneInput.value.trim() || null,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors) {
                    quickStatus.textContent = Object.values(data.errors)
                        .flat()
                        .join(' ');
                    return;
                }

                throw new Error(data.message || 'Не удалось создать контакт.');
            }

            const contact = data.contact;

            const fullName = [
                contact.last_name,
                contact.first_name,
                contact.middle_name,
            ].filter(Boolean).join(' ');

            const label = [
                fullName,
                contact.company,
                contact.phone,
            ].filter(Boolean).join(' — ');

            let option = Array.from(contactSelect.options).find(function (item) {
                return item.value === String(contact.id);
            });

            if (!option) {
                option = document.createElement('option');
                option.value = String(contact.id);
                contactSelect.appendChild(option);
            }

            option.textContent = label;
            option.dataset.companyId = contact.company_id || '';

            contactSelect.value = String(contact.id);

            if (contact.company_id) {
                companySelect.value = String(contact.company_id);
            }

            hideQuickForm();
            status.hidden = true;
            showSuccessMessage('Контакт успешно создан и выбран для сделки.');

            lastNameInput.value = '';
            firstNameInput.value = '';
            middleNameInput.value = '';
            quickPhoneInput.value = '';
        } catch (error) {
            quickStatus.textContent = error.message || 'Ошибка при создании контакта.';
        } finally {
            saveButton.disabled = false;
            saveButton.textContent = 'Создать контакт';
        }
    });

    cancelButton.addEventListener('click', hideQuickForm);

    contactSelect.addEventListener('change', function () {
        const selectedOption = contactSelect.selectedOptions[0];

        if (selectedOption && selectedOption.dataset.companyId) {
            companySelect.value = selectedOption.dataset.companyId;
        }
    });

    phoneInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchButton.click();
        }
    });
});
