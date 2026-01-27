# RULE FINAL KOPERASI

## 1. ANGGOTA
- Ketua, bendahara, admin = ROLE USER
- Semua role tetap terhubung ke anggota
- Status anggota:
  - aktif
  - cuti
  - tugas_belajar
  - tidak_aktif
- Anggota tidak_aktif tidak boleh transaksi

---

## 2. SIMPANAN
- Tidak ada bunga
- Tidak ada SHU
- Saldo tidak disimpan di kolom
- Saldo = SUM(simpanans.jumlah)
- Simpanan masuk: jumlah positif
- Simpanan keluar: jumlah negatif
- Saldo awal dicatat sebagai transaksi
- Semua pengembalian dicatat (tidak dihapus)

Jenis simpanan:
- pokok
- wajib
- sukarela

---

## 3. PINJAMAN
- Tidak ada bunga
- Tidak ada denda
- Satu pengajuan = satu pinjaman
- Top-up tidak membuat pinjaman baru
- Top-up menambah sisa pinjaman aktif
- Tenor boleh berubah
- Cicilan input manual
- Pelunasan bisa lebih cepat

Batasan:
- Top-up hanya jika sisa pinjaman < 3.000.000
- Total pinjaman aktif + pengajuan ≤ 20.000.000

---

## 4. PENGAJUAN PINJAMAN
Status:
- diajukan
- disetujui
- ditolak
- dibatalkan
- dicairkan

Aturan:
- Ketua boleh menyetujui pengajuan sendiri
- Bendahara wajib mencairkan

---

## 5. PENSIUN / MUTASI
- Anggota tidak boleh keluar jika masih ada pinjaman aktif
- Semua simpanan dikembalikan
- Pengembalian dicatat sebagai transaksi simpanan
- Data tidak dihapus

---

## 6. PRINSIP SISTEM
- Tidak ada update saldo manual
- Tidak ada hapus data uang
- Semua transaksi lewat service
- Service = satu-satunya pintu logika bisnis
