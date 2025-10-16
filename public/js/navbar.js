function toggleDropdown() {
  const dropdown = document.getElementById('manualDropdown');
  dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Tutup dropdown saat klik di luar
window.addEventListener('click', function (event) {
  if (!event.target.closest('.dropdown-manual')) {
    const dropdown = document.getElementById('manualDropdown');
    if (dropdown) dropdown.style.display = 'none';
  }
});

function confirmLogout() {
  Swal.fire({
    title: 'Yakin ingin log out?',
    text: "Sesi Anda akan berakhir.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Logout',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('logout-form').submit();
    }
  });
}

// ✅ Tutup offcanvas saat menu diklik
document.addEventListener('DOMContentLoaded', function () {
  const offcanvasEl = document.getElementById('mobileMenu');
  const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);

  document.querySelectorAll('#mobileMenu a').forEach(link => {
    link.addEventListener('click', function () {
      bsOffcanvas.hide();
    });
  });
});