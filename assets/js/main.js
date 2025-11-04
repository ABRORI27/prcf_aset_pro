// === THEME HANDLER ===
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("modeSwitch");
  const body = document.body;

  // Terapkan tema tersimpan langsung saat halaman siap
  applySavedTheme();

  // Event toggle theme
  if (toggle) {
    toggle.addEventListener("change", () => {
      const isLight = toggle.checked;
      body.classList.toggle("light-mode", isLight);
      localStorage.setItem("theme", isLight ? "light" : "dark");
      syncThemeToDynamicElements();
    });
  }

  // === REFRESH REMINDER TIAP 60 DETIK ===
  setInterval(() => {
    const reminder = document.querySelector(".card.reminder-card .reminder-content");
    if (reminder) {
      fetch("reminder.php")
        .then(res => res.text())
        .then(html => {
          reminder.innerHTML = html;
          syncThemeToDynamicElements();
        });
    }
  }, 60000);

  // Jalankan sinkronisasi awal
  syncThemeToDynamicElements();

  // === HANDLE EXPORT SESUAI FILTER SEARCH & KATEGORI ===
  document.querySelectorAll("a[href*='export.php']").forEach(exportBtn => {
    exportBtn.addEventListener("click", (e) => {
      e.preventDefault();

      // Ambil search & kategori dari localStorage / URL
      const query = localStorage.getItem("searchQuery") || "";
      const kategoriSelect = document.getElementById("filterKategori");
      let kategori = 0;

      if (kategoriSelect) {
        kategori = kategoriSelect.value || 0;
      } else {
        // Jika tidak ada select kategori, coba ambil dari URL
        const urlParams = new URLSearchParams(window.location.search);
        kategori = urlParams.get("kategori") || 0;
      }

      // Susun URL export.php
      let exportUrl = "export.php?";
      const params = [];

      if (query.trim() !== "") params.push("search=" + encodeURIComponent(query.trim()));
      if (kategori > 0) params.push("kategori=" + encodeURIComponent(kategori));

      // Jika tidak ada filter, export semua
      if (params.length > 0) {
        exportUrl += params.join("&");
      }

      window.location.href = exportUrl;
    });
  });
});

// === Terapkan tema tersimpan saat load ===
function applySavedTheme() {
  const theme = localStorage.getItem("theme");
  const body = document.body;
  const toggle = document.getElementById("modeSwitch");
  const isLight = theme === "light";

  body.classList.toggle("light-mode", isLight);
  if (toggle) toggle.checked = isLight;

  syncThemeToDynamicElements();
}

// === Sinkronkan warna untuk semua elemen dinamis ===
function syncThemeToDynamicElements() {
  const body = document.body;
  const isLight = body.classList.contains("light-mode");

  const elements = document.querySelectorAll(`
    .dropdown-container, 
    .dropdown-content, 
    .card, 
    .table, 
    table, 
    .dashboard-box, 
    .summary-box, 
    .info-card, 
    .reminder-card
  `);

  elements.forEach(el => {
    el.classList.toggle("light-mode", isLight);
  });

  document.querySelectorAll("td, th, p, h1, h2, h3, span, label").forEach(el => {
    el.style.color = isLight ? "var(--text-dark)" : "var(--text-light)";
  });

  document.querySelectorAll(".dashboard-box, .card").forEach(el => {
    el.style.backgroundColor = isLight ? "var(--panel-light)" : "var(--panel-dark)";
    el.style.transition = "background-color 0.3s ease";
  });
}

// === FILTER TABLE ===
function filterTable(id, q) {
  q = q.toLowerCase();
  const t = document.getElementById(id);
  if (!t) return;

  for (let r of t.tBodies[0].rows) {
    const text = r.innerText.toLowerCase();
    r.style.display = text.includes(q) ? "" : "none";
  }


  // Simpan query pencarian agar bisa digunakan export.php
  localStorage.setItem("searchQuery", q);
  syncThemeToDynamicElements();
}
document.addEventListener('DOMContentLoaded', function() {
  const exportBtn = document.getElementById('exportBtn');
  const searchInput = document.getElementById('searchInput');

  if (exportBtn && searchInput) {
    exportBtn.addEventListener('click', function(e) {
      e.preventDefault(); // cegah link default
      const keyword = searchInput.value.trim();
      let url = 'export.php';

      // jika sedang ada pencarian, tambahkan ke URL
      if (keyword !== '') {
        url += '?search=' + encodeURIComponent(keyword);
      }

      window.location.href = url;
    });
  }
});

// === FIELD TAMBAHAN UNTUK KENDARAAN ===
function toggleKendaraanFields() {
  const kategori = document.getElementById("kategori_barang")?.value;
  const el = document.getElementById("kendaraanFields");
  if (el) el.style.display = kategori == 4 ? "block" : "none";
}
