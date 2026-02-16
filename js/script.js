/**
 * STUDENT ACADEMIC PLANNER - JavaScript
 * Fitur: Digital Clock, Reminder Notification, Real-time Checking
 */

// ===== UPDATE DIGITAL CLOCK & DATE =====
function updateClock() {
  const now = new Date();

  // Format jam HH:MM:SS
  const hours = String(now.getHours()).padStart(2, "0");
  const minutes = String(now.getMinutes()).padStart(2, "0");
  const seconds = String(now.getSeconds()).padStart(2, "0");
  const timeString = `${hours}:${minutes}:${seconds}`;

  // Format tanggal
  const options = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  };
  const dateString = now.toLocaleDateString("id-ID", options);

  // Update elemen
  const clockElement = document.getElementById("clock");
  const dateElement = document.getElementById("date");

  if (clockElement) {
    clockElement.textContent = timeString;
  }
  if (dateElement) {
    dateElement.textContent = dateString;
  }
}

// Update clock setiap 1 detik
setInterval(updateClock, 1000);
updateClock(); // Panggil sekali saat halaman load

// ===== REMINDER NOTIFICATION (CHECK SETIAP MENIT) =====
function checkReminders() {
  const now = new Date();
  const currentHour = String(now.getHours()).padStart(2, "0");
  const currentMinute = String(now.getMinutes()).padStart(2, "0");
  const currentTime = `${currentHour}:${currentMinute}`;

  // Hari ini dalam bahasa Indonesia
  const hari_english = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
  const hari_indonesia = [
    "Minggu",
    "Senin",
    "Selasa",
    "Rabu",
    "Kamis",
    "Jumat",
    "Sabtu",
  ];
  const hariIni = hari_indonesia[now.getDay()];

  // Ambil semua jadwal yang ditampilkan di halaman
  const scheduleCards = document.querySelectorAll(".schedule-card");

  scheduleCards.forEach((card) => {
    // Ambil jam mulai dari card
    const timeBadge = card.querySelector(".time-badge");
    if (!timeBadge) return;

    const jamMulai = timeBadge.textContent.trim();
    const namaMK = card.querySelector(".schedule-info h4");

    if (!namaMK) return;

    const namaMKText = namaMK.textContent.trim();

    // Hitung 15 menit sebelum jam kuliah
    const [jamMulaiH, jamMulaiM] = jamMulai.split(":");
    const jamMulaiDate = new Date();
    jamMulaiDate.setHours(parseInt(jamMulaiH), parseInt(jamMulaiM), 0);

    const jam15MenitSblm = new Date(jamMulaiDate.getTime() - 15 * 60 * 1000);
    const jam15MenitSblmStr =
      String(jam15MenitSblm.getHours()).padStart(2, "0") +
      ":" +
      String(jam15MenitSblm.getMinutes()).padStart(2, "0");

    // Jika waktu sekarang = 15 menit sebelum jam mulai
    if (currentTime === jam15MenitSblmStr) {
      showReminder(namaMKText);
    }
  });
}

function showReminder(namaMK) {
  const notificationDiv = document.getElementById("reminder-notification");
  if (!notificationDiv) return;

  // Set pesan dan tampilkan
  notificationDiv.innerHTML = `
        <strong>🔔 PENGINGAT!</strong><br>
        Mata Kuliah <strong>${escapeHtml(namaMK)}</strong> akan dimulai 15 menit lagi!<br>
        Segera persiapkan dirimu! 📚
    `;
  notificationDiv.style.display = "block";

  // Buat suara notifikasi (jika browser mendukung)
  try {
    const audioContext = new (
      window.AudioContext || window.webkitAudioContext
    )();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();

    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);

    oscillator.frequency.value = 800;
    oscillator.type = "sine";

    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(
      0.01,
      audioContext.currentTime + 0.5,
    );

    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.5);
  } catch (e) {
    console.log("Audio notification tidak didukung");
  }

  // Browser Notification API (jika user memberikan permission)
  if ("Notification" in window && Notification.permission === "granted") {
    new Notification("Pengingat Akademik", {
      body: `Mata Kuliah ${namaMK} akan dimulai 15 menit lagi!`,
      icon: "📚",
    });
  }

  // Sembunyikan notifikasi setelah 10 detik
  setTimeout(() => {
    notificationDiv.style.display = "none";
  }, 10000);
}

// Minta permission untuk Browser Notifications
if ("Notification" in window && Notification.permission === "default") {
  Notification.requestPermission();
}

// Check reminder setiap menit
setInterval(checkReminders, 60000);

// Juga check saat halaman dimuat
checkReminders();

// ===== HELPER FUNCTION =====
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

// ===== ANIMASI SMOOTH SCROLL =====
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});

// ===== FORM VALIDATION =====
document.querySelectorAll("form").forEach((form) => {
  form.addEventListener("submit", function (e) {
    const requiredFields = this.querySelectorAll("[required]");
    let isValid = true;

    requiredFields.forEach((field) => {
      if (!field.value.trim()) {
        field.style.borderColor = "var(--danger)";
        isValid = false;
      } else {
        field.style.borderColor = "";
      }
    });

    if (!isValid) {
      e.preventDefault();
      alert("⚠️ Mohon isi semua field yang wajib diisi!");
    }
  });
});

// ===== FILE SIZE VALIDATION =====
document.querySelectorAll('input[type="file"]').forEach((input) => {
  input.addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      const maxSize = 20 * 1024 * 1024; // 20MB
      if (file.size > maxSize) {
        alert("❌ Ukuran file terlalu besar! Maximum 20MB");
        this.value = "";
      }
    }
  });
});

// ===== CONFIRM DELETE ACTION =====
document.querySelectorAll(".btn-delete").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    if (
      !confirm("🗑️ Yakin ingin menghapus? Tindakan ini tidak bisa dibatalkan!")
    ) {
      e.preventDefault();
    }
  });
});

console.log("✅ Student Academic Planner - Script loaded successfully!");
