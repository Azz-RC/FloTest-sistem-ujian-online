<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FloTest - Ujian Online</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100">

    <div class="mx-auto max-w-5xl px-4 py-8">

        {{-- HEADER --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800">
                FloTest
            </h1>

            <p class="mt-2 text-gray-600">
                Sistem Ujian Online
            </p>
        </div>


        {{-- ALERT --}}
        <div
            id="alert"
            class="mb-6 hidden rounded-lg p-4 text-sm"
        ></div>


        {{-- LOGIN / MASUK ROOM --}}
        <section
            id="room-section"
            class="rounded-xl bg-white p-6 shadow"
        >

            <h2 class="mb-4 text-xl font-semibold text-gray-800">
                Masuk Room Test
            </h2>

            <div class="grid gap-4 md:grid-cols-2">

                <div>
                    <label
                        for="id_user"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        ID User
                    </label>

                    <input
                        type="number"
                        id="id_user"
                        placeholder="Contoh: 1"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none"
                    >
                </div>


                <div>
                    <label
                        for="kode_test"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Kode Test
                    </label>

                    <input
                        type="text"
                        id="kode_test"
                        placeholder="Contoh: YIPN0N"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 uppercase focus:border-blue-500 focus:outline-none"
                    >
                </div>

            </div>


            <button
                id="btn-masuk-room"
                type="button"
                class="mt-5 rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white hover:bg-blue-700"
            >
                Masuk Room
            </button>

        </section>


        {{-- INFORMASI TEST --}}
        <section
            id="test-section"
            class="mt-6 hidden"
        >

            <div class="rounded-xl bg-white p-6 shadow">

                <div class="mb-6">
                    <h2
                        id="judul-test"
                        class="text-2xl font-bold text-gray-800"
                    ></h2>

                    <div class="mt-2 text-sm text-gray-600">
                        Kode Test:
                        <span
                            id="kode-test-display"
                            class="font-semibold"
                        ></span>
                    </div>

                    <div class="text-sm text-gray-600">
                        Status:
                        <span
                            id="status-test-display"
                            class="font-semibold"
                        ></span>
                    </div>
                </div>


                {{-- SOAL --}}
                <div id="soal-container"></div>


                <button
                    id="btn-kirim-jawaban"
                    type="button"
                    class="mt-6 w-full rounded-lg bg-green-600 px-5 py-3 font-semibold text-white hover:bg-green-700"
                >
                    Kirim Jawaban
                </button>

            </div>

        </section>


        {{-- HASIL TEST --}}
        <section
            id="hasil-section"
            class="mt-6 hidden"
        >

            <div class="rounded-xl bg-white p-6 text-center shadow">

                <h2 class="text-2xl font-bold text-gray-800">
                    Hasil Test
                </h2>

                <div class="mt-6 grid gap-4 md:grid-cols-3">

                    <div class="rounded-lg bg-gray-100 p-4">
                        <div class="text-sm text-gray-500">
                            Nilai
                        </div>

                        <div
                            id="hasil-nilai"
                            class="mt-1 text-3xl font-bold"
                        >
                            -
                        </div>
                    </div>


                    <div class="rounded-lg bg-gray-100 p-4">
                        <div class="text-sm text-gray-500">
                            Benar
                        </div>

                        <div
                            id="hasil-benar"
                            class="mt-1 text-3xl font-bold"
                        >
                            -
                        </div>
                    </div>


                    <div class="rounded-lg bg-gray-100 p-4">
                        <div class="text-sm text-gray-500">
                            Salah
                        </div>

                        <div
                            id="hasil-salah"
                            class="mt-1 text-3xl font-bold"
                        >
                            -
                        </div>
                    </div>

                </div>

            </div>

        </section>


        {{-- RIWAYAT TEST --}}
        <section
            id="riwayat-section"
            class="mt-6"
        >

            <div class="rounded-xl bg-white p-6 shadow">

                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">

                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            Riwayat Test
                        </h2>

                        <p class="text-sm text-gray-500">
                            Masukkan ID User untuk melihat riwayat.
                        </p>
                    </div>


                    <button
                        id="btn-riwayat"
                        type="button"
                        class="rounded-lg bg-gray-800 px-5 py-2.5 font-medium text-white hover:bg-gray-900"
                    >
                        Lihat Riwayat
                    </button>

                </div>


                <div
                    id="riwayat-container"
                    class="mt-5"
                ></div>

            </div>

        </section>


        {{-- DETAIL RIWAYAT --}}
        <section
            id="detail-riwayat-section"
            class="mt-6 hidden"
        >

            <div class="rounded-xl bg-white p-6 shadow">

                <h2 class="text-xl font-semibold text-gray-800">
                    Detail Riwayat
                </h2>

                <div
                    id="detail-riwayat-container"
                    class="mt-4"
                ></div>

            </div>

        </section>

    </div>

</body>
</html>