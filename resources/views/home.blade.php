<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Hafidz Monitor adalah aplikasi pemantau perkembangan hafalan Al-Qur'an yang membantu guru dan santri dalam menilai, mencatat, dan menganalisis kemajuan hafalan setiap ayat. Dilengkapi dengan histori grafik, dukungan offline, dan sinkronisasi ke server untuk penyimpanan data terpusat, Hafidz Monitor memudahkan pengelolaan hafalan dengan lebih efisien." />
  <meta name="author" content="Shiftech Indonesia" />
  <title>Hafidz Monitor</title>
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.gstatic.com" />
  <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,600;1,600&amp;display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300;0,500;0,600;0,700;1,300;1,500;1,600;1,700&amp;display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,400;1,400&amp;display=swap" rel="stylesheet" />
  <link href="/assets/css/styles.css" rel="stylesheet" />
  @vite([])
</head>

<body id="page-top">
  {{-- <!-- Navigation--> --}}
  <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNav">
    <div class="container px-5">
      <a class="navbar-brand fw-bold" href="#page-top">Hafidz Monitor</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
        aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <i class="bi-list"></i>
      </button>
      <div class="navbar-collapse collapse" id="navbarResponsive">
        <ul class="navbar-nav my-lg-0 my-3 me-4 ms-auto">
          <li class="nav-item"><a class="nav-link me-lg-3" href="#about">Tentang</a></li>
          <li class="nav-item"><a class="nav-link me-lg-3" href="#features">Fitur</a></li>
        </ul>
        <a class="btn btn-primary rounded-pill mb-lg-0 mb-2 px-3" href="./login">
          <span class="d-flex align-items-center">
            <i class="bi-box-arrow-in-right me-2"></i>
            <span class="small">Masuk</span>
          </span>
        </a>
      </div>
    </div>
  </nav>
  {{-- <!-- Mashead header--> --}}
  @if (false)
    <header class="masthead">
      <div class="container px-5">
        <div class="row gx-5 align-items-center">
          <div class="col-lg-6">
            <!-- Mashead text and app badges-->
            <div class="mb-lg-0 text-lg-start mb-5 text-center">
              <h1 class="display-1 lh-1 mb-3">Showcase your app beautifully.</h1>
              <p class="lead fw-normal text-muted mb-5">Launch your mobile app landing page faster with this free,
                open source theme from Start Bootstrap!</p>
              <div class="d-flex flex-column flex-lg-row align-items-center">
                <a class="me-lg-3 mb-lg-0 mb-4" href="#!"><img class="app-badge"
                    src="/assets/images/google-play-badge.svg" alt="..." /></a>
                <a href="#!"><img class="app-badge" src="assets/images/app-store-badge.svg" alt="..." /></a>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <!-- Masthead device mockup feature-->
            <div class="masthead-device-mockup">
              <svg class="circle" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <defs>
                  <linearGradient id="circleGradient" gradientTransform="rotate(45)">
                    <stop class="gradient-start-color" offset="0%"></stop>
                    <stop class="gradient-end-color" offset="100%"></stop>
                  </linearGradient>
                </defs>
                <circle cx="50" cy="50" r="50"></circle>
              </svg><svg class="shape-1 d-none d-sm-block" viewBox="0 0 240.83 240.83"
                xmlns="http://www.w3.org/2000/svg">
                <rect x="-32.54" y="78.39" width="305.92" height="84.05" rx="42.03"
                  transform="translate(120.42 -49.88) rotate(45)"></rect>
                <rect x="-32.54" y="78.39" width="305.92" height="84.05" rx="42.03"
                  transform="translate(-49.88 120.42) rotate(-45)"></rect>
              </svg><svg class="shape-2 d-none d-sm-block" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="50"></circle>
              </svg>
              <div class="device-wrapper">
                <div class="device" data-device="iPhoneX" data-orientation="portrait" data-color="black">
                  <div class="screen bg-black">
                    <!-- PUT CONTENTS HERE:-->
                    <!-- * * This can be a video, image, or just about anything else.-->
                    <!-- * * Set the max width of your media to 100% and the height to-->
                    <!-- * * 100% like the demo example below.-->
                    <video muted="muted" autoplay="" loop="" style="max-width: 100%; height: 100%">
                      <source src="/assets/images/demo-screen.mp4" type="video/mp4" />
                    </video>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  @endif
  {{-- <!-- Quote/testimonial aside--> --}}
  <aside class="bg-gradient-primary-to-secondary text-center" id="about">
    <div class="container px-5">
      <div class="row gx-5 justify-content-center">
        <div class="col-xl-8">
          <div class="h2 fs-1 mb-4 mt-5 text-white">
            <b>Hafidz Monitor</b> adalah aplikasi inovatif yang dirancang untuk
            memantau dan mendukung perkembangan hafalan Al-Qur'an secara
            efektif.
          </div>
          <p class="mb-0 mt-0 text-white">Dengan fitur unggulan seperti penilaian setiap ayat,
            histori perkembangan dalam bentuk grafik interaktif,
            <b>Hafidz Monitor</b> menjadi solusi modern untuk para penghafal
            dan pembimbing. Dikembangkan menggunakan teknologi mutakhir,
            aplikasi ini tidak hanya mempermudah proses pencatatan hafalan,
            tetapi juga memberikan analisis mendalam untuk meningkatkan
            kualitas hafalan. Cocok untuk individu, lembaga tahfiz, dan
            komunitas. <b>Hafidz Monitor</b> hadir untuk membantu Anda
            menggapai hafalan yang lebih terstruktur dan terukur.
          </p>
          {{-- <img src="/assets/images/tnw-logo.svg" alt="..." style="height: 3rem" /> --}}
        </div>
      </div>
    </div>
  </aside>
  {{-- <!-- App features section--> --}}
  <section id="features">
    <div class="container">
      <div class="row gx-5 gy-5 align-items-center">
        <div class="col-lg-8 order-lg-1 mb-lg-0">
          <div class="container-fluid">
            <h2 class="my-3 py-0">Fitur-fitur Hafidz Monitor</h2>
            <ul style="padding: 0 0 0 20px; margin: 0">
              <li>
                <b>Penilaian Hafalan Per Ayat</b>: Memungkinkan pengguna untuk
                memberikan penilaian detail pada setiap ayat, membantu
                pemantauan kemajuan secara spesifik.
              </li>
              <li>
                <b>Grafik Histori Perkembangan</b>: Menyajikan visualisasi
                data dalam bentuk grafik untuk melacak peningkatan hafalan
                dari waktu ke waktu.
              </li>
              <li>
                <b>Manajemen Profil Hafidz</b>: Mendukung pengelolaan data
                personal para penghafal, seperti nama, target hafalan, dan
                catatan kemajuan.
              </li>
              <li>
                <b>Pencatatan Riwayat Kesalahan</b>: Mencatat kesalahan yang
                sering terjadi pada ayat tertentu untuk membantu fokus dalam
                perbaikan.
              </li>
              <li>
                <b>Analisis Kualitas Hafalan</b>: Memberikan insight berbasis
                data, seperti ayat yang sering salah atau butuh pengulangan,
                untuk meningkatkan kualitas hafalan.
              </li>
              <li>
                <b>Dukungan Multi-Platform</b>: Tersedia untuk perangkat
                desktop, web, dan mobile, memberikan fleksibilitas dalam
                penggunaan.
              </li>
              {{-- <li>
                    <b>Pengelolaan Grup atau Kelas (Coming Soon)</b>: Memungkinkan
                    pembimbing untuk mengelola kelompok hafidz dengan mudah,
                    termasuk laporan perkembangan setiap anggota.
                  </li>
                  <li>
                    <b>Notifikasi dan Pengingat (Coming Soon)</b>: Fitur untuk
                    mengingatkan jadwal mengulang hafalan atau target harian.
                  </li>
                  <li>
                    <b>Mode Offline (Coming Soon)</b>: Mendukung penggunaan tanpa
                    koneksi internet, sehingga tetap dapat digunakan kapan saja
                    dan di mana saja.
                  </li>
                  <li>
                    <b>Sinkronisasi Data ke Server (Coming Soon)</b>: Memungkinkan
                    pencadangan dan pemulihan data secara terpusat untuk
                    menghindari kehilangan data.
                  </li>
                  <li>
                    <b>Target Hafalan (Coming Soon)</b>: Memungkinkan pengguna
                    menetapkan target hafalan harian, mingguan, atau bulanan.
                  </li> --}}
            </ul>
          </div>
        </div>
        <div class="col-lg-4 order-lg-0">
          <!-- Features section device mockup-->
          <div class="features-device-mockup">
            <svg class="circle" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="circleGradient" gradientTransform="rotate(45)">
                  <stop class="gradient-start-color" offset="0%"></stop>
                  <stop class="gradient-end-color" offset="100%"></stop>
                </linearGradient>
              </defs>
              <circle cx="50" cy="50" r="50"></circle>
            </svg><svg class="shape-1 d-none d-sm-block" viewBox="0 0 240.83 240.83"
              xmlns="http://www.w3.org/2000/svg">
              <rect x="-32.54" y="78.39" width="305.92" height="84.05" rx="42.03"
                transform="translate(120.42 -49.88) rotate(45)"></rect>
              <rect x="-32.54" y="78.39" width="305.92" height="84.05" rx="42.03"
                transform="translate(-49.88 120.42) rotate(-45)"></rect>
            </svg><svg class="shape-2 d-none d-sm-block" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
              <circle cx="50" cy="50" r="50"></circle>
            </svg>
            <div class="device-wrapper">
              <div class="device" data-device="iPhoneX" data-orientation="portrait" data-color="black">
                <div class="screen bg-black">
                  {{-- <!-- PUT CONTENTS HERE:-->
                  <!-- * * This can be a video, image, or just about anything else.-->
                  <!-- * * Set the max width of your media to 100% and the height to-->
                  <!-- * * 100% like the demo example below.--> --}}
                  <video muted="muted" autoplay="" loop="" style="max-width: 100%; height: 100%">
                    <source src="/assets/images/demo-screen.mp4" type="video/mp4" />
                  </video>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  {{-- <!-- Basic features section--> --}}
  @if (false)
    <section class="bg-light">
      <div class="container px-5">
        <div class="row gx-5 align-items-center justify-content-center justify-content-lg-between">
          <div class="col-12 col-lg-5">
            <h2 class="display-4 lh-1 mb-4">Enter a new age of web design</h2>
            <p class="lead fw-normal text-muted mb-lg-0 mb-5">This section is perfect for featuring some
              information
              about your application, why it was built, the problem it solves, or anything else! There's plenty of
              space for text here, so don't worry about writing too much.</p>
          </div>
          <div class="col-sm-8 col-md-6">
            <div class="px-sm-0 px-5"><img class="img-fluid rounded-circle"
                src="https://source.unsplash.com/u8Jn2rzYIps/900x900" alt="..." /></div>
          </div>
        </div>
      </div>
    </section>
    {{-- <!-- Call to action section--> --}}
    <section class="cta">
      <div class="cta-content">
        <div class="container px-5">
          <h2 class="display-1 lh-1 mb-4 text-white">
            Stop waiting.
            <br />
            Start building.
          </h2>
          <a class="btn btn-outline-light rounded-pill px-4 py-3" href="https://startbootstrap.com/theme/new-age"
            target="_blank">Download for free</a>
        </div>
      </div>
    </section>
    {{-- <!-- App badge section--> --}}
    <section class="bg-gradient-primary-to-secondary" id="download">
      <div class="container px-5">
        <h2 class="font-alt mb-4 text-center text-white">Get the app now!</h2>
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center">
          <a class="me-lg-3 mb-lg-0 mb-4" href="#!"><img class="app-badge"
              src="/assets/images/google-play-badge.svg" alt="..." /></a>
          <a href="#!"><img class="app-badge" src="/assets/images/app-store-badge.svg" alt="..." /></a>
        </div>
      </div>
    </section>
  @endif
  {{-- <!-- Footer--> --}}
  <footer class="bg-gradient-primary-to-secondary py-5 text-center">
    <div class="container px-5">
      <div class="text-white-50 small">
        <div class="mb-2">&copy; 2025 Hafidz Monitor</div>
        <div class="mb-2">Developed by <a href="https://www.shiftech.my.id">Shiftech</a></div>
        {{-- <a href="#">Privacy</a>
            <span class="mx-1">&middot;</span>
            <a href="#">Terms</a>
            <span class="mx-1">&middot;</span>
            <a href="#">FAQ</a> --}}
      </div>
    </div>
  </footer>
  {{-- <!-- Feedback Modal--> --}}
  {{-- <!-- Bootstrap core JS--> --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  {{-- <!-- Core theme JS--> --}}
  <script src="/assets/js/scripts.js"></script>
</body>
</html>
