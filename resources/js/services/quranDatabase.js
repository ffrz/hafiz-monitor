import Dexie from 'dexie';

// Initialize the database
const db = new Dexie("QuranDatabase");
db.version(1).stores({
  surahs: 'id, name, englishName, numberOfAyahs', // Surah schema
  ayahs: '++id, surah, ayahNumber, text'         // Ayah schema
});

// Fetch and store Quran data
export async function fetchAndStoreQuranData() {
  try {
    // Fetch all Surahs
    const surahsResponse = await fetch('https://api.alquran.cloud/v1/surah');
    const surahsData = await surahsResponse.json();

    // Store Surahs in IndexedDB
    await db.surahs.bulkPut(surahsData.data);

    // Fetch Ayahs for each Surah
    for (const surah of surahsData.data) {
      const ayahsResponse = await fetch(`https://api.alquran.cloud/v1/surah/${surah.number}`);
      const ayahsData = await ayahsResponse.json();

      // Map Ayahs for database storage
      const ayahs = ayahsData.data.ayahs.map(ayah => ({
        surah: surah.number,
        ayahNumber: ayah.numberInSurah,
        text: ayah.text,
      }));

      // Store Ayahs in IndexedDB
      await db.ayahs.bulkPut(ayahs);
    }

    console.log("Quran data fetched and stored in IndexedDB.");
  } catch (error) {
    console.error("Error fetching or storing Quran data:", error);
  }
}

// Retrieve Surahs
export async function getSurahs() {
  return await db.surahs.toArray();
}

// Retrieve Ayahs by Surah
export async function getAyahs(surahId) {
  return await db.ayahs.where('surah').equals(surahId).toArray();
}
