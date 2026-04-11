<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RekeningKoperasi;
use App\Models\ArusKas;
use App\Models\Pinjaman;
use App\Models\Simpanan;

class SaldoController extends Controller
{
    public function index()
    {
        $this->authorize('view saldo');
        $penyertaanModalKantin = 4500000;

        $rekenings = RekeningKoperasi::where('aktif', true)->get();

        $rekeningData = $rekenings->map(function ($rekening) {
            $saldoTotal = ArusKas::where('rekening_koperasi_id', $rekening->id)
                ->selectRaw("
                    SUM(
                        CASE 
                            WHEN tipe = 'masuk' THEN jumlah
                            ELSE -jumlah
                        END
                    ) as saldo
                ")
                ->value('saldo') ?? 0;

            $saldoKoperasi = ArusKas::where('rekening_koperasi_id', $rekening->id)
                ->where('jenis_arus', 'koperasi')
                ->selectRaw("
                    SUM(
                        CASE 
                            WHEN tipe = 'masuk' THEN jumlah
                            ELSE -jumlah
                        END
                    ) as saldo
                ")
                ->value('saldo') ?? 0;

            $saldoOperasional = ArusKas::where('rekening_koperasi_id', $rekening->id)
                ->where('jenis_arus', 'operasional')
                ->selectRaw("
                    SUM(
                        CASE 
                            WHEN tipe = 'masuk' THEN jumlah
                            ELSE -jumlah
                        END
                    ) as saldo
                ")
                ->value('saldo') ?? 0;

            return [
                'nama' => $rekening->nama,
                'jenis' => $rekening->jenis,
                'saldo' => $saldoTotal,
                'saldo_koperasi' => $saldoKoperasi,
                'saldo_operasional' => $saldoOperasional,
            ];
        });

        $totalSaldoKoperasi = $rekeningData->sum('saldo_koperasi');
        $totalSaldoOperasional = $rekeningData->sum('saldo_operasional');

        $simpananPokok = Simpanan::where('jenis_simpanan', 'pokok')->sum('jumlah');
        $simpananWajib = Simpanan::where('jenis_simpanan', 'wajib')->sum('jumlah');
        $simpananSukarela = Simpanan::where('jenis_simpanan', 'sukarela')->sum('jumlah');
        $simpananWajibPokok = $simpananPokok + $simpananWajib;
        $totalSimpananDanModal = $simpananWajibPokok + $simpananSukarela + $penyertaanModalKantin;

        $piutangPinjaman = Pinjaman::where('status', 'aktif')->sum('sisa_pinjaman');
        $totalPiutangKoperasi = $piutangPinjaman + $penyertaanModalKantin;
        $totalSaldoKoperasi = $totalSimpananDanModal - $totalPiutangKoperasi;
        $saldoBisaDipinjam = $totalSaldoKoperasi - $simpananSukarela;
        $selisihNeraca = $totalSimpananDanModal - ($totalPiutangKoperasi + $totalSaldoKoperasi);

        $ringkasanSaldo = [
            ['label' => 'Saldo Kas Koperasi', 'nilai' => $totalSaldoKoperasi],
            ['label' => 'Saldo Kas Operasional', 'nilai' => $totalSaldoOperasional],
        ];

        $rincianKoperasiKiri = [
            ['label' => 'Simpanan Wajib + Pokok', 'nilai' => $simpananWajibPokok],
            ['label' => 'Simpanan Sukarela', 'nilai' => $simpananSukarela],
            ['label' => 'Penyertaan Modal Kantin', 'nilai' => $penyertaanModalKantin],
        ];

        $rincianKoperasiKanan = [
            ['label' => 'Piutang ke Anggota', 'nilai' => $piutangPinjaman],
            ['label' => 'Piutang Penyertaan Modal Kantin', 'nilai' => $penyertaanModalKantin],
            ['label' => 'Saldo Kas Koperasi', 'nilai' => $totalSaldoKoperasi],
        ];

        $alokasiSaldoKoperasi = [
            ['label' => 'Simpanan Sukarela', 'nilai' => $simpananSukarela],
            ['label' => 'Saldo Riil untuk Dipinjam Anggota', 'nilai' => $saldoBisaDipinjam],
        ];

        return view('keuangan.saldo', compact(
            'totalSaldoKoperasi',
            'totalSaldoOperasional',
            'simpananSukarela',
            'saldoBisaDipinjam',
            'ringkasanSaldo',
            'rincianKoperasiKiri',
            'rincianKoperasiKanan',
            'alokasiSaldoKoperasi',
            'selisihNeraca'
        ));
    }
}
