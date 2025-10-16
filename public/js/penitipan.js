 document.addEventListener('DOMContentLoaded', function () {
    const tanggalMasuk = document.getElementById('tanggal_masuk');
    const tanggalKeluar = document.getElementById('tanggal_keluar');
    const cekButton = document.getElementById('cekButton');
    const form = cekButton.closest('form');

    function validateTanggal() {
      const masuk = tanggalMasuk.value;
      const keluar = tanggalKeluar.value;
      const now = new Date();
      // Bentuk 'YYYY-MM-DD' dari hari ini
      const today = now.toISOString().split('T')[0];

      // 1. Kalau tanggal masuk hari ini tapi sudah lewat jam 19:00
      if (masuk === today && now.getHours() >= 19) {
        cekButton.disabled = true;
        cekButton.innerText = 'Minimal Tanggal Besok';
        cekButton.classList.add('btn-secondary');
        cekButton.classList.remove('btn-warning-custom');
        return;
      }

      // 2. Kalau tanggal belum lengkap
      if (!masuk || !keluar) {
        cekButton.disabled = true;
        cekButton.innerText = 'Isi tanggal';
        cekButton.classList.add('btn-secondary');
        cekButton.classList.remove('btn-warning-custom');
        return;
      }

      // 3. Kalau tanggal sama
      if (masuk === keluar) {
        cekButton.disabled = true;
        cekButton.innerText = 'Tanggal sama';
        cekButton.classList.add('btn-secondary');
        cekButton.classList.remove('btn-warning-custom');
        return;
      }

      // 4. Kalau tanggal keluar sebelum masuk
      if (masuk > keluar) {
        cekButton.disabled = true;
        cekButton.innerText = 'Tanggal tidak valid';
        cekButton.classList.add('btn-secondary');
        cekButton.classList.remove('btn-warning-custom');
        return;
      }

      // 5. Valid: enable tombol
      cekButton.disabled = false;
      cekButton.innerHTML = '<i class="fas fa-search me-1"></i> Cek';
      cekButton.classList.remove('btn-secondary');
      cekButton.classList.add('btn-warning-custom');
    }

    function handleFormSubmit(event) {
      const masuk = tanggalMasuk.value;
      const keluar = tanggalKeluar.value;
      const now = new Date();
      const today = now.toISOString().split('T')[0];

      // Cegah submit kalau masih invalid
      if (
        !masuk ||
        !keluar ||
        masuk === keluar ||
        masuk > keluar ||
        (masuk === today && now.getHours() >= 19)
      ) {
        event.preventDefault();
      }
    }

    tanggalMasuk.addEventListener('change', validateTanggal);
    tanggalKeluar.addEventListener('change', validateTanggal);
    form.addEventListener('submit', handleFormSubmit);
    document.addEventListener('DOMContentLoaded', validateTanggal);
});

function toggleKandangImage() {
    const container = document.getElementById('kandangImageContainer');
    const button = event.currentTarget;

    if (container.style.display === 'none') {
      container.style.display = 'block';
      button.innerHTML = '<i class="fas fa-image me-1"></i> Sembunyikan Gambar Kandang';
    } else {
      container.style.display = 'none';
      button.innerHTML = '<i class="fas fa-image me-1"></i> Lihat Gambar Kandang';
    }
  }