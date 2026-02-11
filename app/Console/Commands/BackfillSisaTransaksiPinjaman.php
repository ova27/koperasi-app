<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pinjaman;
use App\Models\TransaksiPinjaman;
use Illuminate\Support\Facades\DB;

class BackfillSisaTransaksiPinjaman extends Command
{
    protected $signature = 'pinjaman:backfill-sisa';
    protected $description = 'Mengisi kolom sisa_setelah pada transaksi_pinjaman lama';

    public function handle()
    {
        $this->info('Mulai backfill sisa_setelah transaksi pinjaman...');

        DB::transaction(function () {

            $pinjamanList = Pinjaman::with(['transaksi' => function ($q) {
                $q->orderBy('tanggal')
                ->orderBy('id');
            }])->get();

            foreach ($pinjamanList as $pinjaman) {

                $this->line("Pinjaman ID: {$pinjaman->id}");

                $sisa = 0;

                foreach ($pinjaman->transaksi as $t) {

                    if (in_array($t->jenis, ['pencairan', 'topup'])) {
                        $sisa += $t->jumlah;
                    }

                    if ($t->jenis === 'cicilan') {
                        $sisa -= $t->jumlah;
                    }

                    if ($t->jenis === 'pelunasan') {
                        $sisa = 0;
                    }

                    $sisa = max($sisa, 0);

                    $t->update([
                        'sisa_setelah' => $sisa,
                    ]);

                    $this->line(
                        "- {$t->tanggal} | {$t->jenis} | Rp {$t->jumlah} | sisa: Rp {$sisa}"
                    );
                }

                // sinkronkan pinjaman
                $pinjaman->update([
                    'sisa_pinjaman' => $sisa,
                    'status' => $sisa === 0 ? 'lunas' : 'aktif',
                ]);
            }
        });

        $this->info('Backfill selesai ✔');
        return Command::SUCCESS;
    }

}
