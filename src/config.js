// Semua data contoh undangan dapat diganti di sini.
export const wedding = {
  bride: { name: 'Levi', fullName: 'Levi Anindya Putri', parents: 'Bapak Aditya Pratama & Ibu Ratna Sari', order: 'Putri pertama dari', instagram: '' },
  groom: { name: 'Dio', fullName: 'Dio Ahmad Pratama', parents: 'Bapak Ahmad Wijaya & Ibu Dewi Lestari', order: 'Putra pertama dari', instagram: '' },
  date: '2026-12-12T08:00:00+07:00',
  dateLabel: 'Sabtu, 12 Desember 2026',
  venue: 'The Grand Ballroom',
  address: 'Hotel Majapahit, Jl. Tunjungan No. 65, Surabaya',
  city: 'Surabaya, Indonesia',
  mapUrl: 'https://www.google.com/maps/search/?api=1&query=Hotel+Majapahit+Surabaya',
  events: [
    { name: 'Akad Nikah', time: '08.00 – 10.00 WIB', start: '20261212T010000Z', end: '20261212T030000Z', icon: 'gem' },
    { name: 'Resepsi', time: '11.00 – 14.00 WIB', start: '20261212T040000Z', end: '20261212T070000Z', icon: 'wine' },
  ],
  streamingUrl: '', // Isi tautan siaran sebelum undangan dibagikan.
  accounts: [
    { bank: 'BCA', number: '0000000000', holder: 'Levi Anindya Putri' },
    { bank: 'mandiri', number: '0000000000000', holder: 'Dio Ahmad Pratama' },
  ],
  giftAddress: 'Levi & Dio — Jl. Melati No. 12, Surabaya, Jawa Timur 60261',
  demo: true,
  // Isi URL endpoint POST untuk menyimpan RSVP bersama. Kosong = tersimpan di browser tamu saja.
  rsvpEndpoint: '',
  families: ['Keluarga Bapak Aditya Pratama & Ibu Ratna Sari', 'Keluarga Bapak Ahmad Wijaya & Ibu Dewi Lestari'],
  vendors: ['MAJAPAHIT · VENUE', 'ARUNA · PHOTOGRAPHY', 'KALA · WEDDING ORGANIZER'],
};
