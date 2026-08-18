function lanjutBuatSoal() {

    const judulTest = document.getElementById("judul-test").value.trim();

    if (judulTest === "") {

        alert("Silakan masukkan judul test terlebih dahulu.");

        return;
    }

    localStorage.setItem("judulTest", judulTest);

    window.location.href = "buat-soal.php";
}

// =========================
// TAMBAH SOAL
// =========================

let nomorSoal = 1;

function tambahSoal() {

    nomorSoal++;

    const container = document.getElementById("soal-container");

    const soalBaru = document.createElement("div");

    soalBaru.classList.add("soal-card");

    soalBaru.innerHTML = `
        <div class="soal-title">
            Soal ${nomorSoal}
        </div>

        <div class="soal-content">

            <div class="pertanyaan-area">

                <textarea
                    class="input-soal"
                    placeholder="Tulis soal di sini..."
                ></textarea>

            </div>


            <div class="pilihan-area">

                <label class="pilihan-item">

                    <input
                        type="radio"
                        name="soal-${nomorSoal}"
                        value="A"
                    >

                    <span>A.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        placeholder="Pilihan A"
                    >

                </label>


                <label class="pilihan-item">

                    <input
                        type="radio"
                        name="soal-${nomorSoal}"
                        value="B"
                    >

                    <span>B.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        placeholder="Pilihan B"
                    >

                </label>


                <label class="pilihan-item">

                    <input
                        type="radio"
                        name="soal-${nomorSoal}"
                        value="C"
                    >

                    <span>C.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        placeholder="Pilihan C"
                    >

                </label>


                <label class="pilihan-item">

                    <input
                        type="radio"
                        name="soal-${nomorSoal}"
                        value="D"
                    >

                    <span>D.</span>

                    <input
                        type="text"
                        class="input-pilihan"
                        placeholder="Pilihan D"
                    >

                </label>

            </div>


            <div class="kunci-area">

                <label>
                    Kunci Jawaban
                </label>

                <select>

                    <option value="">
                        Pilih jawaban
                    </option>

                    <option value="A">
                        A. Pilihan A
                    </option>

                    <option value="B">
                        B. Pilihan B
                    </option>

                    <option value="C">
                        C. Pilihan C
                    </option>

                    <option value="D">
                        D. Pilihan D
                    </option>

                </select>

            </div>

        </div>
    `;

    container.appendChild(soalBaru);

    updateLangkah();
}


// =========================
// UPDATE LANGKAH
// =========================

function updateLangkah() {

    const langkah = document.getElementById("jumlah-langkah");

    langkah.textContent = `Langkah ${nomorSoal} dari 3`;
}


// =========================
// KEMBALI
// =========================

function kembaliKeBuatTest() {

    window.location.href = "buat-test.php";
}


// =========================
// SELANJUTNYA
// =========================

function selesaiBuatSoal() {

    if (nomorSoal < 1) {
        return;
    }

    window.location.href = "test-selesai.php";
}

// =========================
// MASUK TEST
// =========================

function masukTest() {

    const kodeTest = document.getElementById("kode-test").value.trim();

    if (kodeTest === "") {

        alert("Silakan masukkan kode test terlebih dahulu.");

        return;
    }

    localStorage.setItem("kodeTest", kodeTest);

    window.location.href = "kerjakan-test.php";
}

// =========================
// DATA SOAL SEMENTARA
// =========================

const daftarSoal = [

    {
        pertanyaan: "Apa planet terbesar di tata surya?",
        A: "Bumi",
        B: "Mars",
        C: "Jupiter",
        D: "Venus"
    },

    {
        pertanyaan: "Apa ibu kota Indonesia?",
        A: "Bandung",
        B: "Jakarta",
        C: "Surabaya",
        D: "Medan"
    },

    {
        pertanyaan: "Berapa hasil dari 10 + 5?",
        A: "10",
        B: "12",
        C: "15",
        D: "20"
    },

    {
        pertanyaan: "Hewan yang dikenal sebagai raja hutan adalah?",
        A: "Harimau",
        B: "Gajah",
        C: "Singa",
        D: "Serigala"
    },

    {
        pertanyaan: "Planet yang dikenal sebagai Planet Merah adalah?",
        A: "Mars",
        B: "Venus",
        C: "Jupiter",
        D: "Saturnus"
    }

];


// =========================
// POSISI SOAL
// =========================

let soalSekarang = 0;


// Menyimpan jawaban peserta

const jawabanPeserta = {};


// =========================
// TAMPILKAN SOAL
// =========================

function tampilkanSoal() {

    const soal = daftarSoal[soalSekarang];

    document.getElementById("pertanyaan").textContent =
        `${soalSekarang + 1}. ${soal.pertanyaan}`;

    document.getElementById("jawaban-A").textContent =
        `A. ${soal.A}`;

    document.getElementById("jawaban-B").textContent =
        `B. ${soal.B}`;

    document.getElementById("jawaban-C").textContent =
        `C. ${soal.C}`;

    document.getElementById("jawaban-D").textContent =
        `D. ${soal.D}`;

    document.getElementById("nomor-soal").textContent =
        `Soal ${soalSekarang + 1} dari ${daftarSoal.length}`;


    // Menampilkan jawaban yang sebelumnya dipilih

    const jawabanSebelumnya =
        jawabanPeserta[soalSekarang];

    const pilihan =
        document.querySelectorAll('input[name="jawaban"]');

    pilihan.forEach(function(input) {

        input.checked =
            input.value === jawabanSebelumnya;

    });
}


// =========================
// SOAL BERIKUTNYA
// =========================

function soalBerikutnya() {

    const jawaban =
        document.querySelector('input[name="jawaban"]:checked');


    if (!jawaban) {

        alert("Silakan pilih jawaban terlebih dahulu.");

        return;
    }


    // Simpan jawaban

    jawabanPeserta[soalSekarang] =
        jawaban.value;


    if (soalSekarang < daftarSoal.length - 1) {

        soalSekarang++;

        tampilkanSoal();

    } else {

        // Soal terakhir

        window.location.href = "hasil-test.php";

    }
}


// =========================
// SOAL SEBELUMNYA
// =========================

function soalSebelumnya() {

    if (soalSekarang === 0) {

        alert("Ini adalah soal pertama.");

        return;
    }


    // Simpan jawaban jika ada

    const jawaban =
        document.querySelector('input[name="jawaban"]:checked');

    if (jawaban) {

        jawabanPeserta[soalSekarang] =
            jawaban.value;
    }


    soalSekarang--;

    tampilkanSoal();
}


// =========================
// JALANKAN SAAT HALAMAN DIBUKA
// =========================

if (document.getElementById("pertanyaan")) {

    tampilkanSoal();

}

// =========================
// MOBILE MENU
// =========================

function toggleMenu() {

    const menu = document.querySelector(".navbar-menu");

    if (menu) {
        menu.classList.toggle("show");
    }
}