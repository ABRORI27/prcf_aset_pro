// === THEME HANDLER ===
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("modeSwitch");
  const body = document.body;

  // Terapkan tema tersimpan langsung saat halaman siap
  applySavedTheme();

  // Event toggle theme (langsung sinkronkan ke semua elemen)
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

  // Jalankan sinkronisasi awal setelah semua elemen dimuat
  syncThemeToDynamicElements();
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

  // Elemen yang harus ikut berubah tema
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

  // Pastikan teks selalu terlihat di kedua mode
  document.querySelectorAll("td, th, p, h1, h2, h3, span, label").forEach(el => {
    el.style.color = isLight ? "var(--text-dark)" : "var(--text-light)";
  });

  // Perbaikan khusus untuk box dashboard agar data langsung muncul
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
  syncThemeToDynamicElements();
}

// === FIELD TAMBAHAN UNTUK KENDARAAN ===
function toggleKendaraanFields() {
  const kategori = document.getElementById("kategori_barang")?.value;
  const el = document.getElementById("kendaraanFields");
  if (el) el.style.display = kategori == 4 ? "block" : "none";
}
