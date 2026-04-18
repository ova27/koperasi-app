<?php

return [
    'simpanan_wajib' => 50000,
    'iuran_dharma_wanita' => 0,
    'infaq_pegawai' => 0,
    'tabungan_qurban' => 0,
    'iuran_operasional' => 5000,
    'master_rekening_pengurus' => [
        'default' => [
            'nama' => 'Bank Syariah Indonesia (BSI) Cabang Serang',
            'nomor_rekening' => '7235147593',
            'atas_nama' => 'DANDI ISWANDI',
            'keterangan' => 'Pengurus Koperasi Pegawai BPS Provinsi Banten',
        ],
        'by_bank' => [
            'BPS' => [
                'nama' => 'Bank Syariah Indonesia (BSI) Cabang Serang',
                'nomor_rekening' => '7235147593',
                'atas_nama' => 'DANDI ISWANDI',
                'keterangan' => 'Pengurus Koperasi Pegawai BPS Provinsi Banten',
            ],
            'BRI' => [
                'nama' => 'Bank Rakyat Indonesia (BRI)',
                'nomor_rekening' => '-',
                'atas_nama' => 'OKTAVANYTA ARIANI',
                'keterangan' => 'Bendahara Koperasi Pegawai BPS Provinsi Banten',
            ],
        ],
    ],
    'surat_kuasa_bank' => [
        'instansi' => 'BADAN PUSAT STATISTIK PROVINSI BANTEN',
        'penandatangan_1' => [
            'jabatan_pengantar' => 'Kepala Bagian Umum BPS Provinsi Banten',
            'nama' => 'Ridwan Hidayat',
            'nip' => '19720306 199512 1 001',
            'jabatan' => 'Kepala Bagian Umum',
        ],
        'penandatangan_2' => [
            'jabatan_pengantar' => 'Bendahara Pengeluaran',
            'nama' => 'Intan Putri Firdaus, A.Md',
            'nip' => '19910508 201403 2 003',
            'jabatan' => 'Bendahara Pengeluaran BPS Provinsi Banten',
        ],
        // Fallback lama: jika master_rekening_pengurus tidak ditemukan, sistem pakai nilai ini.
        'bank_tujuan' => [
            'nama' => 'Bank Syariah Indonesia (BSI) Cabang Serang',
            'nomor_rekening' => '7235147593',
            'atas_nama' => 'DANDI ISWANDI',
            'keterangan' => 'Pengurus Koperasi Pegawai BPS Provinsi Banten',
        ],
        'kota' => 'Serang',
        'tanggal' => '2026-03-30',
    ],
];
