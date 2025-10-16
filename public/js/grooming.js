document.getElementById("filter-button").addEventListener("click", () => {
      const d = document.getElementById("date-filter");
      d.style.display = d.style.display === "none" ? "block" : "none";
    });

    document.getElementById("reset-button").addEventListener("click", () => {
      document.getElementById("date-filter").value = "";
      document.querySelectorAll(".schedule-item").forEach(e => e.style.display = "flex");
      if (document.getElementById("no-match")) document.getElementById("no-match").remove();
    });

    function filterByDate() {
      const sel = document.getElementById("date-filter").value;
      const items = document.querySelectorAll(".schedule-item");
      let hasMatch = false;
      items.forEach(item => {
        const text = item.querySelector("h6").textContent;
        if (text.includes(sel)) {
          item.style.display = "flex";
          hasMatch = true;
        } else {
          item.style.display = "none";
        }
      });
      if (!hasMatch && !document.getElementById("no-match")) {
        const noMatch = document.createElement("div");
        noMatch.className = "p-4 bg-light text-center text-danger rounded shadow mt-3";
        noMatch.id = "no-match";
        noMatch.innerHTML = "<h5>Tidak ada jadwal pada tanggal tersebut</h5>";
        document.getElementById("schedule-list").appendChild(noMatch);
      } else {
        const noMatch = document.getElementById("no-match");
        if (noMatch) noMatch.remove();
      }
    }