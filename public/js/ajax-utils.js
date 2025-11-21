// Utility untuk menangani AJAX requests
const AjaxUtils = {
    // Fungsi untuk melakukan request AJAX
    request: async function (url, method = 'GET', data = null) {
        try {
            const options = {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            };

            if (data) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(url, options);

            // Cek status response
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Terjadi kesalahan pada server');
            }

            // Parse response sebagai JSON
            const result = await response.json();

            // Validasi format response
            if (typeof result !== 'object') {
                throw new Error('Format response tidak valid');
            }

            return result;
        } catch (error) {
            console.error('AJAX Error:', error);
            throw error;
        }
    },

    // Fungsi untuk refresh data tabel
    refreshTable: async function (tableId, url) {
        try {
            const response = await this.request(url);
            const table = document.getElementById(tableId);
            if (table) {
                table.innerHTML = response.html;
            }
        } catch (error) {
            console.error('Error refreshing table:', error);
            this.showNotification(error.message, 'danger');
        }
    },

    // Fungsi untuk menampilkan notifikasi
    showNotification: function (message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Tambahkan ke container notifikasi
        const container = document.querySelector('.alert-container') || document.body;
        container.insertBefore(alertDiv, container.firstChild);

        // Auto dismiss setelah 5 detik
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}; 