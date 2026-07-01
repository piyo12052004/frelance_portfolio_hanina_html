console.log('app.js loaded');

document.addEventListener('alpine:init', () => {
    Alpine.store('modal', {

        open: false,
        title: '',
        size: 'max-w-xl',
        content: '',
        data: null,

        show({ title, template, size = 'max-w-xl', data = null }) {

            this.open = true;
            this.title = title;
            this.size = size;
            this.data = data;

            const el = document.getElementById(template);

            if (!el) {
                this.content = '<p>Template tidak ditemukan</p>';
                return;
            }

            let html = el.innerHTML;

            // replace placeholder
            if (data) {
                html = html
                    .replaceAll('{{data}}', data ?? '');
                // .replaceAll('{{username}}', data.username ?? '')
                // .replaceAll('{{role}}', data.role ?? '');
            }

            this.content = html;
        },

        close() {
            this.open = false;
            this.title = '';
            this.content = '';
            this.data = null;
            this.size = 'max-w-xl';
        }
    });

    Alpine.data('passwordField', () => ({
        show: false
    }));

    Alpine.store("alert", {
        confirmDelete(form) {

            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Data yang dihapus tidak dapat dikembalikan.",
                icon: "warning",

                showCancelButton: true,

                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",

                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#6b7280",

                reverseButtons: true

            }).then((result) => {

                if (result.isConfirmed) {
                    form.submit();
                }

            });

        }

    });


});