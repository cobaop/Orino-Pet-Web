document.addEventListener('DOMContentLoaded', function () {
  const groomingForm = document.querySelector('#groomingForm');
  const antarJemputSelect = document.getElementById('opsi_antar_jemput');
  const alamatContainer = document.getElementById('alamat_container');
  const alamatTextarea = document.getElementById('alamat');
  const useProfileCheckbox = document.getElementById('use_profile_address');
  const defaultAlamat = alamatTextarea ? alamatTextarea.dataset.default : '';

  // Saat form utama disubmit, tampilkan modal persetujuan
  groomingForm.addEventListener('submit', function (e) {
    e.preventDefault();
    new bootstrap.Modal(document.getElementById('persetujuanModal')).show();
  });

  // Logika checklist semua
  document.getElementById('checkAll').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('.persetujuan-item');
    checkboxes.forEach(cb => cb.checked = this.checked);
  });

  document.querySelectorAll('.persetujuan-item').forEach(cb => {
    cb.addEventListener('change', function () {
      const all = document.querySelectorAll('.persetujuan-item');
      const allChecked = Array.from(all).every(cb => cb.checked);
      document.getElementById('checkAll').checked = allChecked;
    });
  });

  // Saat tombol modal diklik
  document.querySelector('#btnModalSubmit').addEventListener('click', function () {
    const allCheckboxes = document.querySelectorAll('.persetujuan-item');
    const allAgreed = Array.from(allCheckboxes).every(cb => cb.checked);

    if (!allAgreed) {
      Swal.fire({
        icon: 'error',
        title: 'Reservasi Ditolak',
        text: 'Silakan centang semua pernyataan sebelum melanjutkan.',
      }).then(() => {
        // tutup modal agar user bisa centang ulang
        const modalEl = document.getElementById('persetujuanModal');
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        bsModal.hide();
        // kembali fokus ke tombol submit utama
        document.querySelector('button[type="submit"]').focus();
      });
    } else {
      // semua sudah diceklis, kirim form
      document.querySelector('#groomingForm').submit();
    }
  });


  // Tampilkan alamat jika antar jemput dipilih
  antarJemputSelect.addEventListener('change', function () {
    if (this.value === 'iya') {
      alamatContainer.style.display = 'block';
      alamatTextarea.setAttribute('required', 'required');
      alamatTextarea.value = defaultAlamat;
      alamatTextarea.readOnly = true;
      useProfileCheckbox.checked = true;
    } else {
      alamatContainer.style.display = 'none';
      alamatTextarea.removeAttribute('required');
      alamatTextarea.value = '';
      alamatTextarea.readOnly = false;
    }
  });

  // Logika checkbox "Gunakan alamat dari profil"
  useProfileCheckbox?.addEventListener('change', function () {
    if (this.checked) {
      alamatTextarea.value = defaultAlamat;
      alamatTextarea.readOnly = true;
    } else {
      alamatTextarea.value = '';
      alamatTextarea.readOnly = false;
    }
  });
});

// Ambil lokasi pengguna menggunakan geolocation
function getLocation() {
  if (!navigator.geolocation) {
    return alert('Browser tidak mendukung geolokasi.');
  }

  navigator.geolocation.getCurrentPosition(function (pos) {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    document.getElementById('lokasiPreview').innerText =
      `Lokasi diambil: (${lat.toFixed(6)}, ${lng.toFixed(6)})`;
  }, function () {
    Swal.fire({
      icon: 'error',
      title: 'Gagal Ambil Lokasi',
      text: 'Pastikan Anda mengizinkan akses lokasi di browser.',
    });
  });
}
