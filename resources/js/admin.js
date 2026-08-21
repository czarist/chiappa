import './bootstrap';

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

document.addEventListener('change', (event) => {
    const fileInput = event.target.closest('[data-seo-upload]');
    if (!fileInput?.files?.[0]) {
        return;
    }

    const picker = fileInput.closest('.image-picker');
    const field = picker?.querySelector('input[name]');
    const preview = picker?.querySelector('.image-picker-preview');
    const data = new FormData();
    data.append('file', fileInput.files[0]);
    data.append('_token', csrfToken());
    picker?.classList.add('is-busy');

    fetch(fileInput.dataset.seoUpload, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: data,
        credentials: 'same-origin',
    })
        .then((res) => res.json().then((json) => ({ res, json })))
        .then(({ res, json }) => {
            if (!res.ok || !json.success) {
                throw new Error(json.message || 'Falha ao enviar imagem.');
            }
            if (field) {
                field.value = json.data.url;
            }
            if (preview) {
                preview.innerHTML = `<img src="${json.data.url}" alt="">`;
            }
        })
        .catch((error) => alert(error.message))
        .finally(() => {
            picker?.classList.remove('is-busy');
            fileInput.value = '';
        });
});

document.addEventListener('input', (event) => {
    const picker = event.target.closest('.seo-panel .image-picker');
    if (!picker || event.target.name !== 'seo[og_image]') {
        return;
    }
    const preview = picker.querySelector('.image-picker-preview');
    if (!preview) {
        return;
    }
    preview.innerHTML = event.target.value
        ? `<img src="${event.target.value}" alt="">`
        : '<i class="fas fa-image"></i>';
});
