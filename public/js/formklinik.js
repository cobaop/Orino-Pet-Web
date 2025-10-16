    const inputTanggal = document.getElementById('tanggal_reservasi');
    const hariText = document.getElementById('hariText');

    inputTanggal.addEventListener('change', function () {
        const date = new Date(this.value);
        const hari = date.toLocaleDateString('id-ID', { weekday: 'long' });
        if (!isNaN(date.getTime())) {
            hariText.textContent = 'Hari: ' + hari;
        } else {
            hariText.textContent = '';
        }
    });
