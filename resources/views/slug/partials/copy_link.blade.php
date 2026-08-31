@php
    $namaPria = $heroInvitation->nama_lengkap_pria ?? 'Pengantin 1';
    $namaWanita = $heroInvitation->nama_lengkap_wanita ?? 'Pengantin 2';

    $frontendUrl = rtrim(
        env('FRONTEND_URL', 'https://royalweddinginvitiation.com'),
        '/'
    );

    $linkUndangan = $frontendUrl . '/' . $slug->slug;

    $formatUndangan = "Kepada Yth.\n"
        . "Bapak/Ibu/Saudara/i\n"
        . "Tamu Undangan\n"
        . "__\n\n"
        . "Assalamualaikum Wr. Wb.\n\n"
        . "Bismillahirrahmanirrahim.\n\n"
        . "Tanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i, teman sekaligus sahabat, untuk menghadiri acara pernikahan kami:\n\n"
        . $namaPria . "\n"
        . "&\n"
        . $namaWanita . "\n\n"
        . "Berikut link untuk info lengkap acara kami:\n\n"
        . $linkUndangan . "\n\n"
        . "Merupakan suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu kepada kami.\n\n"
        . "Jangan lupa mengisi Guestbook ya 😊\n\n"
        . "Wassalamualaikum Wr. Wb";
@endphp

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <h4 class="mb-3">Copy Format Undangan</h4>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Mempelai 1</label>
            <input
                type="text"
                class="form-control"
                value="{{ $namaPria }}"
                readonly
            >
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Mempelai 2</label>
            <input
                type="text"
                class="form-control"
                value="{{ $namaWanita }}"
                readonly
            >
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Link Undangan</label>

            <div class="input-group">
                <input
                    type="text"
                    id="linkUndangan"
                    class="form-control"
                    value="{{ $linkUndangan }}"
                    readonly
                >

                <button
                    type="button"
                    class="btn btn-outline-primary"
                    onclick="copyLinkUndangan()"
                >
                    Copy Link
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Format Siap Kirim</label>

            <textarea
                id="formatUndangan"
                class="form-control"
                rows="22"
                readonly
            >{{ $formatUndangan }}</textarea>
        </div>

        <div class="d-flex gap-2 flex-wrap">

            <button
                type="button"
                class="btn btn-primary"
                onclick="copyFormatUndangan()"
            >
                Copy Format
            </button>

            <button
                type="button"
                class="btn btn-outline-primary"
                onclick="copyLinkUndangan()"
            >
                Copy Link
            </button>

            <a
                href="{{ $linkUndangan }}"
                target="_blank"
                class="btn btn-success"
            >
                Buka Undangan
            </a>

        </div>

        <div
            id="copyStatus"
            class="alert alert-success mt-3 mb-0 d-none"
        ></div>

    </div>
</div>

<script>
    function showCopyStatus(message) {
        const status = document.getElementById('copyStatus');

        status.innerText = message;
        status.classList.remove('d-none');

        setTimeout(() => {
            status.classList.add('d-none');
        }, 2000);
    }

    function copyText(text, successMessage) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(() => {
                    showCopyStatus(successMessage);
                })
                .catch(() => {
                    fallbackCopy(text, successMessage);
                });
        } else {
            fallbackCopy(text, successMessage);
        }
    }

    function fallbackCopy(text, successMessage) {
        const textarea = document.createElement('textarea');

        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';

        document.body.appendChild(textarea);

        textarea.focus();
        textarea.select();

        document.execCommand('copy');

        document.body.removeChild(textarea);

        showCopyStatus(successMessage);
    }

    function copyFormatUndangan() {
        const format = document.getElementById('formatUndangan').value;

        copyText(
            format,
            'Format undangan berhasil dicopy.'
        );
    }

    function copyLinkUndangan() {
        const link = document.getElementById('linkUndangan').value;

        copyText(
            link,
            'Link undangan berhasil dicopy.'
        );
    }
</script>