document.addEventListener('DOMContentLoaded', function () {
    const ktpInput = document.getElementById('ktp');
    const uploadTitle = document.getElementById('uploadTitle');
    const uploadDescription = document.getElementById('uploadDescription');
    const uploadError = document.getElementById('uploadError');
    const verificationForm = document.getElementById('verificationForm');

    // Validasi file KTP
    if (ktpInput) {
        ktpInput.addEventListener('change', function () {
            const file = this.files[0];

            uploadError.textContent = '';

            if (!file) {
                uploadTitle.textContent = 'Pilih file KTP';
                uploadDescription.textContent =
                    'Klik untuk mengunggah dokumen';
                return;
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'application/pdf'
            ];

            const maxSize = 5 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                uploadError.textContent =
                    'Format file harus JPG, PNG, atau PDF.';

                this.value = '';
                return;
            }

            if (file.size > maxSize) {
                uploadError.textContent =
                    'Ukuran file maksimal adalah 5 MB.';

                this.value = '';
                return;
            }

            uploadTitle.textContent = file.name;
            uploadDescription.textContent =
                'File berhasil dipilih.';
        });
    }

    // Validasi formulir sebelum dikirim
    if (verificationForm) {
        verificationForm.addEventListener('submit', function (event) {
            const gender = document.getElementById('gender');
            const genderConsent =
                document.getElementById('genderConsent');

            if (!ktpInput.files.length) {
                event.preventDefault();
                uploadError.textContent =
                    'Silakan unggah dokumen KTP terlebih dahulu.';
                return;
            }

            if (!gender.value) {
                event.preventDefault();
                alert('Silakan pilih gender terlebih dahulu.');
                return;
            }

            if (!genderConsent.checked) {
                event.preventDefault();
                alert('Silakan berikan persetujuan penggunaan data.');
                return;
            }

            // Backend belum dihubungkan.
            event.preventDefault();
            alert(
                'Validasi berhasil! Fitur penyimpanan akan dihubungkan ke backend.'
            );
        });
    }
});
