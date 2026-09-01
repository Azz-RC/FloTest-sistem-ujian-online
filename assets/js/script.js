/* =========================================================
   FLOTEST - SCRIPT
========================================================= */

/* =========================================================
   MENU MOBILE
========================================================= */

function toggleMenu() {
  const menu = document.querySelector(".navbar-menu");

  if (!menu) {
    return;
  }

  menu.classList.toggle("show");
}

/* =========================================================
   BUAT SOAL
========================================================= */

let nomorSoal = 1;

/* =========================================================
   TAMBAH SOAL
========================================================= */

function tambahSoal() {
  const container = document.getElementById("soal-container");

  if (!container) {
    return;
  }

  const jumlahSoal = container.querySelectorAll(".soal-card").length;

  /* -------------------------------------------------------
     BATAS MAKSIMAL SOAL
  ------------------------------------------------------- */

  if (jumlahSoal >= 100) {
    alert("Maksimal 100 soal.");

    return;
  }

  nomorSoal = jumlahSoal + 1;

  /* -------------------------------------------------------
     BUAT CARD SOAL BARU
  ------------------------------------------------------- */

  const card = document.createElement("div");

  card.className = "soal-card";

  card.setAttribute("data-nomor", nomorSoal);

  card.innerHTML = `

    <div class="soal-title">

      Soal ${nomorSoal}

    </div>


    <div class="soal-content">


      <!-- PERTANYAAN -->

      <div class="pertanyaan-area">

        <textarea
          class="input-soal"
          placeholder="Tulis soal di sini..."
          maxlength="65535"
        ></textarea>

      </div>


      <!-- PILIHAN -->

      <div class="pilihan-area">


        <!-- PILIHAN A -->

        <label class="pilihan-item">

          <span>A</span>

          <input
            type="text"
            class="input-pilihan pilihan-a"
            placeholder="Pilihan A"
            maxlength="255"
          >

        </label>


        <!-- PILIHAN B -->

        <label class="pilihan-item">

          <span>B</span>

          <input
            type="text"
            class="input-pilihan pilihan-b"
            placeholder="Pilihan B"
            maxlength="255"
          >

        </label>


        <!-- PILIHAN C -->

        <label class="pilihan-item">

          <span>C</span>

          <input
            type="text"
            class="input-pilihan pilihan-c"
            placeholder="Pilihan C"
            maxlength="255"
          >

        </label>


        <!-- PILIHAN D -->

        <label class="pilihan-item">

          <span>D</span>

          <input
            type="text"
            class="input-pilihan pilihan-d"
            placeholder="Pilihan D"
            maxlength="255"
          >

        </label>


      </div>


      <!-- KUNCI JAWABAN -->

      <div class="kunci-area">

        <label for="kunci-${nomorSoal}">
          Kunci Jawaban
        </label>


        <select
          id="kunci-${nomorSoal}"
          class="kunci-jawaban"
        >

          <option value="">
            Pilih jawaban
          </option>

          <option value="A">A</option>

          <option value="B">B</option>

          <option value="C">C</option>

          <option value="D">D</option>

        </select>

      </div>


    </div>

  `;

  container.appendChild(card);

  updateJumlahSoal();

  /* -------------------------------------------------------
     SCROLL KE SOAL BARU
  ------------------------------------------------------- */

  card.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
}

/* =========================================================
   UPDATE JUMLAH SOAL
========================================================= */

function updateJumlahSoal() {
  const container = document.getElementById("soal-container");

  const indikator = document.getElementById("jumlah-langkah");

  if (!container || !indikator) {
    return;
  }

  const jumlah = container.querySelectorAll(".soal-card").length;

  indikator.textContent = jumlah + " soal";
}

/* =========================================================
   AMBIL DATA SOAL
========================================================= */

function siapkanDataSoal() {
  const container = document.getElementById("soal-container");

  if (!container) {
    return [];
  }

  const cards = container.querySelectorAll(".soal-card");

  const semuaSoal = [];

  cards.forEach(function (card) {
    const inputPertanyaan = card.querySelector(".input-soal");

    const inputA = card.querySelector(".pilihan-a");

    const inputB = card.querySelector(".pilihan-b");

    const inputC = card.querySelector(".pilihan-c");

    const inputD = card.querySelector(".pilihan-d");

    const inputKunci = card.querySelector(".kunci-jawaban");

    semuaSoal.push({
      pertanyaan: inputPertanyaan ? inputPertanyaan.value.trim() : "",

      pilihan_a: inputA ? inputA.value.trim() : "",

      pilihan_b: inputB ? inputB.value.trim() : "",

      pilihan_c: inputC ? inputC.value.trim() : "",

      pilihan_d: inputD ? inputD.value.trim() : "",

      kunci_jawaban: inputKunci ? inputKunci.value.trim().toUpperCase() : "",
    });
  });

  return semuaSoal;
}

/* =========================================================
   VALIDASI DATA SOAL
========================================================= */

function validasiDataSoal(semuaSoal) {
  /* -------------------------------------------------------
     CEK JUMLAH SOAL
  ------------------------------------------------------- */

  if (semuaSoal.length === 0) {
    alert("Minimal harus ada 1 soal.");

    return false;
  }

  if (semuaSoal.length > 100) {
    alert("Maksimal 100 soal.");

    return false;
  }

  /* -------------------------------------------------------
     CEK SATU PER SATU
  ------------------------------------------------------- */

  for (let i = 0; i < semuaSoal.length; i++) {
    const soal = semuaSoal[i];

    const nomor = i + 1;

    /* -----------------------------------------------------
       PERTANYAAN
    ----------------------------------------------------- */

    if (!soal.pertanyaan || soal.pertanyaan.trim() === "") {
      alert("Pertanyaan soal nomor " + nomor + " belum diisi.");

      return false;
    }

    /* -----------------------------------------------------
       PILIHAN A
    ----------------------------------------------------- */

    if (!soal.pilihan_a || soal.pilihan_a.trim() === "") {
      alert("Pilihan A soal nomor " + nomor + " belum diisi.");

      return false;
    }

    /* -----------------------------------------------------
       PILIHAN B
    ----------------------------------------------------- */

    if (!soal.pilihan_b || soal.pilihan_b.trim() === "") {
      alert("Pilihan B soal nomor " + nomor + " belum diisi.");

      return false;
    }

    /* -----------------------------------------------------
       PILIHAN C
    ----------------------------------------------------- */

    if (!soal.pilihan_c || soal.pilihan_c.trim() === "") {
      alert("Pilihan C soal nomor " + nomor + " belum diisi.");

      return false;
    }

    /* -----------------------------------------------------
       PILIHAN D
    ----------------------------------------------------- */

    if (!soal.pilihan_d || soal.pilihan_d.trim() === "") {
      alert("Pilihan D soal nomor " + nomor + " belum diisi.");

      return false;
    }

    /* -----------------------------------------------------
       KUNCI JAWABAN
    ----------------------------------------------------- */

    if (!["A", "B", "C", "D"].includes(soal.kunci_jawaban)) {
      alert("Kunci jawaban soal nomor " + nomor + " belum dipilih.");

      return false;
    }
  }

  return true;
}

/* =========================================================
   SIMPAN DATA KE HIDDEN INPUT
========================================================= */

function simpanDataSoalKeForm() {
  const hidden = document.getElementById("soal_data");

  if (!hidden) {
    alert("Input data soal tidak ditemukan.");

    return false;
  }

  const semuaSoal = siapkanDataSoal();

  /* -------------------------------------------------------
     VALIDASI
  ------------------------------------------------------- */

  if (!validasiDataSoal(semuaSoal)) {
    return false;
  }

  /* -------------------------------------------------------
     UBAH ARRAY MENJADI JSON
  ------------------------------------------------------- */

  try {
    hidden.value = JSON.stringify(semuaSoal);
  } catch (error) {
    console.error("Gagal membuat JSON soal:", error);

    alert("Gagal menyiapkan data soal.");

    return false;
  }

  return true;
}

/* =========================================================
   PROSES SUBMIT BUAT SOAL
========================================================= */

function initBuatSoal() {
  const form = document.getElementById("form-soal");

  if (!form) {
    return;
  }

  form.addEventListener("submit", function (event) {
    /*
     * HENTIKAN SUBMIT SEMENTARA
     * supaya kita bisa memproses data soal
     */

    event.preventDefault();

    /* ---------------------------------------------------
         SIAPKAN DATA
      --------------------------------------------------- */

    const berhasil = simpanDataSoalKeForm();

    if (!berhasil) {
      return;
    }

    /* ---------------------------------------------------
         KONFIRMASI
      --------------------------------------------------- */

    const yakin = confirm("Apakah kamu yakin ingin menyelesaikan test?");

    if (!yakin) {
      return;
    }

    /* ---------------------------------------------------
         SUBMIT FORM
      --------------------------------------------------- */

    form.submit();
  });
}

/* =========================================================
   KEMBALI KE BUAT TEST
========================================================= */

function kembaliKeBuatTest() {
  window.location.href = "buat-test.php";
}

/* =========================================================
   KERJAKAN TEST
========================================================= */

function initKerjakanTest() {
  const cards = document.querySelectorAll(".soal-jawab-card");

  if (!cards.length) {
    return;
  }

  const indikator = document.getElementById("nomor-indikator");

  const btnKembali = document.getElementById("btn-kembali");

  const btnSelanjutnya = document.getElementById("btn-selanjutnya");

  const btnSelesai = document.getElementById("btn-selesai");

  const form = document.getElementById("form-jawaban");

  let soalSekarang = 0;

  /* =======================================================
     TAMPILKAN SOAL
  ======================================================= */

  function tampilkanSoal(index) {
    cards.forEach(function (card, i) {
      if (i === index) {
        card.classList.add("soal-active");
      } else {
        card.classList.remove("soal-active");
      }
    });

    /* -----------------------------------------------------
       INDIKATOR
    ----------------------------------------------------- */

    if (indikator) {
      indikator.textContent = "Soal " + (index + 1) + " dari " + cards.length;
    }

    /* -----------------------------------------------------
       TOMBOL KEMBALI
    ----------------------------------------------------- */

    if (btnKembali) {
      if (index === 0) {
        btnKembali.disabled = true;
      } else {
        btnKembali.disabled = false;
      }
    }

    /* -----------------------------------------------------
       TOMBOL SELANJUTNYA / SELESAI
    ----------------------------------------------------- */

    if (index === cards.length - 1) {
      if (btnSelanjutnya) {
        btnSelanjutnya.hidden = true;
      }

      if (btnSelesai) {
        btnSelesai.hidden = false;
      }
    } else {
      if (btnSelanjutnya) {
        btnSelanjutnya.hidden = false;
      }

      if (btnSelesai) {
        btnSelesai.hidden = true;
      }
    }
  }

  /* =======================================================
     SELANJUTNYA
  ======================================================= */

  if (btnSelanjutnya) {
    btnSelanjutnya.addEventListener("click", function () {
      if (soalSekarang < cards.length - 1) {
        soalSekarang++;

        tampilkanSoal(soalSekarang);

        window.scrollTo({
          top: 0,

          behavior: "smooth",
        });
      }
    });
  }

  /* =======================================================
     KEMBALI
  ======================================================= */

  if (btnKembali) {
    btnKembali.addEventListener("click", function () {
      if (soalSekarang > 0) {
        soalSekarang--;

        tampilkanSoal(soalSekarang);

        window.scrollTo({
          top: 0,

          behavior: "smooth",
        });
      }
    });
  }

  /* =======================================================
     KONFIRMASI SELESAI
  ======================================================= */

  if (form) {
    form.addEventListener("submit", function (event) {
      const yakin = confirm("Apakah kamu yakin ingin menyelesaikan test?");

      if (!yakin) {
        event.preventDefault();
      }
    });
  }

  /* =======================================================
     TAMPILKAN SOAL PERTAMA
  ======================================================= */

  tampilkanSoal(0);
}

/* =========================================================
   DOM READY
========================================================= */

document.addEventListener("DOMContentLoaded", function () {
  updateJumlahSoal();

  initBuatSoal();

  initKerjakanTest();
});

/* =========================================================
   CRUD TEST
========================================================= */


/* =========================================================
   KONFIRMASI HAPUS TEST
========================================================= */

document
  .querySelectorAll(".form-hapus-test")
  .forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        const yakin = window.confirm(
          "Yakin ingin menghapus test ini? " +
            "Test tidak akan ditampilkan lagi."
        );

        if (!yakin) {
          event.preventDefault();
        }
      }
    );
  });


/* =========================================================
   PERBARUI NOMOR SOAL DI HALAMAN EDIT
========================================================= */

function perbaruiNomorSoalEdit() {
  const cards = document.querySelectorAll(
    "#edit-soal-container .edit-soal-card"
  );

  cards.forEach(function (card, index) {
    const judul = card.querySelector(
      ".soal-title span"
    );

    if (judul) {
      judul.textContent =
        "Soal " + (index + 1);
    }
  });
}


/* =========================================================
   TAMBAH SOAL DI HALAMAN EDIT
========================================================= */

const tombolTambahEdit = document.getElementById(
  "btn-tambah-edit"
);

if (tombolTambahEdit) {
  tombolTambahEdit.addEventListener(
    "click",
    function () {
      const container =
        document.getElementById(
          "edit-soal-container"
        );

      if (!container) {
        return;
      }

      const jumlahSoal =
        container.querySelectorAll(
          ".edit-soal-card"
        ).length;

      if (jumlahSoal >= 100) {
        alert("Maksimal 100 soal.");

        return;
      }

      const card =
        document.createElement("div");

      card.className =
        "soal-card edit-soal-card";

      card.setAttribute(
        "data-id-soal",
        "0"
      );

      card.innerHTML = `

        <div class="soal-title">

          <span>Soal</span>

          <button
            type="button"
            class="btn-remove-soal"
          >
            Hapus Soal
          </button>

        </div>


        <div class="soal-content">

          <div class="pertanyaan-area">

            <textarea
              class="input-soal"
              placeholder="Tulis soal di sini..."
              maxlength="65535"
              required
            ></textarea>

          </div>


          <div class="pilihan-area">

            <label class="pilihan-item">

              <span>A</span>

              <input
                type="text"
                class="input-pilihan pilihan-a"
                placeholder="Pilihan A"
                maxlength="255"
                required
              >

            </label>


            <label class="pilihan-item">

              <span>B</span>

              <input
                type="text"
                class="input-pilihan pilihan-b"
                placeholder="Pilihan B"
                maxlength="255"
                required
              >

            </label>


            <label class="pilihan-item">

              <span>C</span>

              <input
                type="text"
                class="input-pilihan pilihan-c"
                placeholder="Pilihan C"
                maxlength="255"
                required
              >

            </label>


            <label class="pilihan-item">

              <span>D</span>

              <input
                type="text"
                class="input-pilihan pilihan-d"
                placeholder="Pilihan D"
                maxlength="255"
                required
              >

            </label>

          </div>


          <div class="kunci-area">

            <label>
              Kunci Jawaban
            </label>

            <select
              class="kunci-jawaban"
              required
            >

              <option value="">
                Pilih jawaban
              </option>

              <option value="A">A</option>

              <option value="B">B</option>

              <option value="C">C</option>

              <option value="D">D</option>

            </select>

          </div>

        </div>

      `;

      container.appendChild(card);

      perbaruiNomorSoalEdit();

      card.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  );
}


/* =========================================================
   HAPUS CARD SOAL DI HALAMAN EDIT
========================================================= */

document.addEventListener(
  "click",
  function (event) {
    if (
      !event.target.classList.contains(
        "btn-remove-soal"
      )
    ) {
      return;
    }

    const container =
      document.getElementById(
        "edit-soal-container"
      );

    if (!container) {
      return;
    }

    const jumlahSoal =
      container.querySelectorAll(
        ".edit-soal-card"
      ).length;

    if (jumlahSoal <= 1) {
      alert(
        "Minimal harus ada 1 soal."
      );

      return;
    }

    const card = event.target.closest(
      ".edit-soal-card"
    );

    if (card) {
      card.remove();

      perbaruiNomorSoalEdit();
    }
  }
);


/* =========================================================
   SIAPKAN DATA SOAL SEBELUM DISIMPAN
========================================================= */

const formEditTest = document.getElementById(
  "form-edit-test"
);

if (formEditTest) {
  formEditTest.addEventListener(
    "submit",
    function (event) {
      const cards =
        document.querySelectorAll(
          "#edit-soal-container " +
            ".edit-soal-card"
        );

      if (cards.length === 0) {
        event.preventDefault();

        alert(
          "Minimal harus ada 1 soal."
        );

        return;
      }

      const semuaSoal = [];

      cards.forEach(function (card) {
        const idSoal =
          card.getAttribute(
            "data-id-soal"
          ) || "0";

        const pertanyaan =
          card.querySelector(
            ".input-soal"
          );

        const pilihanA =
          card.querySelector(
            ".pilihan-a"
          );

        const pilihanB =
          card.querySelector(
            ".pilihan-b"
          );

        const pilihanC =
          card.querySelector(
            ".pilihan-c"
          );

        const pilihanD =
          card.querySelector(
            ".pilihan-d"
          );

        const kunciJawaban =
          card.querySelector(
            ".kunci-jawaban"
          );

        semuaSoal.push({
          id_soal: idSoal,

          pertanyaan: pertanyaan
            ? pertanyaan.value.trim()
            : "",

          pilihan_a: pilihanA
            ? pilihanA.value.trim()
            : "",

          pilihan_b: pilihanB
            ? pilihanB.value.trim()
            : "",

          pilihan_c: pilihanC
            ? pilihanC.value.trim()
            : "",

          pilihan_d: pilihanD
            ? pilihanD.value.trim()
            : "",

          kunci_jawaban: kunciJawaban
            ? kunciJawaban.value
            : "",
        });
      });

      const inputSoalData =
        document.getElementById(
          "edit-soal-data"
        );

      if (!inputSoalData) {
        event.preventDefault();

        alert(
          "Input data soal tidak ditemukan."
        );

        return;
      }

      inputSoalData.value =
        JSON.stringify(semuaSoal);
    }
  );
}