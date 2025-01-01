<script setup>
import BtnLink from "@/components/BtnLink.vue";
</script>
<template>
  <i-head title="Welcome" />
  <guest-layout>
    <q-page>
      <div class="row">
        <q-card class="col q-ma-sm q-mt-md" square flat bordered>
          <q-card-section>
            <div class="text-h4 text-center text-bold">Hafiz Monitor</div>
            <p class="text-subtitle1 text-center q-my-md">
              <b>Hafiz Monitor</b> adalah aplikasi inovatif yang dirancang untuk
              memantau dan mendukung perkembangan hafalan Al-Qur'an secara
              efektif. Dengan fitur unggulan seperti penilaian setiap ayat,
              histori perkembangan dalam bentuk grafik interaktif,
              <b>Hafiz Monitor</b> menjadi solusi modern untuk para penghafal
              dan pembimbing. Dikembangkan menggunakan teknologi mutakhir,
              aplikasi ini tidak hanya mempermudah proses pencatatan hafalan,
              tetapi juga memberikan analisis mendalam untuk meningkatkan
              kualitas hafalan. Cocok untuk individu, lembaga tahfiz, dan
              komunitas. <b>Hafiz Monitor</b> hadir untuk membantu Anda
              menggapai hafalan yang lebih terstruktur dan terukur.
            </p>
          </q-card-section>
          <q-card-section>
            <div class="text-center q-gutter-md">
              <btn-link
                class=""
                size="lg"
                color="pink"
                icon="app_registration"
                label="Buat Akun Gratis"
                :href="route('register')"
              />
              <btn-link
                class=""
                size="lg"
                icon="login"
                color="primary"
                label="Login Aplikasi"
                :href="route('login')"
              />
            </div>
          </q-card-section>
          <q-card-section>
            <div class="text-subtitle1 text-bold text-grey-9">
              Fitur-fitur Kunci <b>Hafiz Monitor</b>:
            </div>
            <div>
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
                  <b>Manajemen Profil Hafiz</b>: Mendukung pengelolaan data
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
                <li>
                  <b>Pengelolaan Grup atau Kelas (Coming Soon)</b>: Memungkinkan
                  pembimbing untuk mengelola kelompok hafiz dengan mudah,
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
                </li>
              </ul>
            </div>
          </q-card-section>
          <q-card-section>
            <div class="text-subtitle1 text-bold text-grey-9">Hubungi Kami</div>
            <p>Email: support.hafiz.monitor@shiftech.my.id</p>
          </q-card-section>
        </q-card>
      </div>
    </q-page>
  </guest-layout>
</template>
