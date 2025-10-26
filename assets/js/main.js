// === DARK / LIGHT MODE SWITCH GLOBAL ===
document.addEventListener("DOMContentLoaded", () => {
  const modeSwitch = document.getElementById("modeSwitch");
  const current = localStorage.getItem("theme");

  if (current === "light") {
    document.body.classList.add("light-mode");
    if (modeSwitch) modeSwitch.checked = true;
  }

  if (modeSwitch) {
    modeSwitch.addEventListener("change", () => {
      document.body.classList.toggle("light-mode");
      const mode = document.body.classList.contains("light-mode") ? "light" : "dark";
      localStorage.setItem("theme", mode);
    });
  }

  // === REFRESH REMINDER TIAP 60 DETIK ===
  setInterval(() => {
    const reminder = document.querySelector(".card.reminder-card .reminder-content");
    if (reminder) {
      fetch("reminder.php")
        .then(res => res.text())
        .then(html => (reminder.innerHTML = html));
    }
  }, 60000);
});

// === FILTER TABLE ===
function filterTable(id, q) {
  q = q.toLowerCase();
  const t = document.getElementById(id);
  if (!t) return;
  for (let r of t.tBodies[0].rows) {
    const text = r.innerText.toLowerCase();
    r.style.display = text.includes(q) ? "" : "none";
  }
}

// === FIELD TAMBAHAN UNTUK KENDARAAN ===
function toggleKendaraanFields() {
  const el = document.getElementById("kendaraanFields");
  const val = document.getElementById("kategori_barang")?.value?.toLowerCase() || "";
  if (!el) return;
  el.style.display =
    val.includes("kendaraan") || val.includes("mobil") || val.includes("motor")
      ? "block"
      : "none";
}
