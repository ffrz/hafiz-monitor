import Dexie from 'dexie';

const db = new Dexie("QuranDatabase");

db.version(1).stores({
  surahs: 'id, name, total_ayahs',
  ayahs: 'id, surah_id, number, juz, text'
});


async function isSurahsStored() {
  return await db.surahs.count() > 0;
}

async function isAyahsStored(surah_id) {
  const count = await db.ayahs.where('surah_id').equals(surah_id).count();
  return count > 0;
}

export async function getSurahs() {
  const stored = await isSurahsStored();
  if (!stored) {
    await fetchSurahs();
  }
  return await db.surahs.toArray();
}

export async function getAyahs(surah_id) {
  let ayahs = await db.ayahs.where('surah_id').equals(surah_id).toArray();
  if (ayahs.length == 0) {
    await fetchAyahsBySurahId(surah_id);
    ayahs = await db.ayahs.where('surah_id').equals(surah_id).toArray();
  }
  return ayahs;
}

async function fetchSurahs() {
  try {
    const surahsResponse = await fetch('/api/v1/surahs');
    const surahsData = await surahsResponse.json();
    await db.surahs.bulkPut(surahsData);
    console.log("Quran data fetched and stored in IndexedDB.");
  } catch (error) {
    console.error("Error fetching or storing Quran data:", error);
    alert('Gagal mengunduh data surat, silahkan coba refresh halaman!');
  }
}

async function fetchAyahsBySurahId(surah_id) {
  try {
    const cached = await db.ayahs.where('surah_id').equals(String(surah_id)).toArray();

    if (cached.length > 0) {
      console.log("Data loaded from IndexedDB.");
      return cached;
    }

    const ayahsResponse = await fetch(`/api/v1/surahs/${surah_id}/ayahs`);
    const ayahsData = await ayahsResponse.json();

    // Normalisasi jika perlu
    const cleaned = ayahsData.map(a => ({
      ...a,
      id: Number(a.id),
      surah_id: String(a.surah_id),
      number: Number(a.number),
      juz: Number(a.juz),
      text: a.text
    }));

    await db.ayahs.bulkPut(cleaned);
    console.log("Ayahs fetched and stored in IndexedDB.");
    return cleaned;
  } catch (error) {
    console.error("Error fetching or storing Quran data:", error);
    alert('Gagal mengunduh data surat, silahkan coba refresh halaman!');
  }
}

