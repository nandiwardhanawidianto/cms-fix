<style>
    .saved-gallery-item {
        width: 132px;
        margin: 6px;
        padding: 6px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
        text-align: center;
    }

    .saved-gallery-item .galeri-preview {
        width: 118px;
        height: 118px;
        margin: 0 0 6px 0 !important;
        object-fit: cover;
    }

    .saved-gallery-number {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 6px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteUrl = @json(route('galeri.photo.destroy', $slug_id));
    const csrfToken = @json(csrf_token());

    function storagePathFromImage(image) {
        const src = image.getAttribute('src') || '';
        const marker = '/storage/';
        const markerIndex = src.indexOf(marker);

        if (markerIndex === -1) {
            return null;
        }

        const pathWithQuery = src.substring(markerIndex + marker.length);
        const cleanPath = pathWithQuery.split('?')[0];

        try {
            return decodeURIComponent(cleanPath);
        } catch (error) {
            return cleanPath;
        }
    }

    function addDeleteButtons(selector, carouselValue, carouselLabel) {
        const images = Array.from(document.querySelectorAll('#galeri ' + selector));

        images.forEach(function (image, index) {
            if (image.closest('.saved-gallery-item')) {
                return;
            }

            const imagePath = storagePathFromImage(image);

            if (!imagePath) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.className = 'saved-gallery-item';

            image.parentNode.insertBefore(wrapper, image);
            wrapper.appendChild(image);

            const number = document.createElement('div');
            number.className = 'saved-gallery-number';
            number.textContent = 'Foto ' + (index + 1);
            wrapper.appendChild(number);

            const form = document.createElement('form');
            form.action = deleteUrl;
            form.method = 'POST';
            form.addEventListener('submit', function (event) {
                const confirmed = window.confirm(
                    'Hapus Foto ' + (index + 1) + ' dari ' + carouselLabel + '?'
                );

                if (!confirmed) {
                    event.preventDefault();
                }
            });

            const fields = {
                _token: csrfToken,
                _method: 'DELETE',
                carousel: carouselValue,
                image_path: imagePath
            };

            Object.entries(fields).forEach(function ([name, value]) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            });

            const button = document.createElement('button');
            button.type = 'submit';
            button.className = 'btn btn-outline-danger btn-sm w-100';
            button.textContent = 'Hapus Foto Ini';
            form.appendChild(button);

            wrapper.appendChild(form);
        });
    }

    addDeleteButtons('img[alt="Carousel Atas"]', 'atas', 'Carousel Atas');
    addDeleteButtons('img[alt="Carousel Bawah"]', 'bawah', 'Carousel Bawah');
});
</script>
