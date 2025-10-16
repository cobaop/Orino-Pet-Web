function filterServices() {
      const keyword = document.getElementById('searchInput').value.toLowerCase();
      document.querySelectorAll('.service-item').forEach(card => {
        const title = card.querySelector('h2').textContent.toLowerCase();
        card.style.display = title.includes(keyword) ? 'block' : 'none';
      });
    }
