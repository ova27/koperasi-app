<?php

namespace Tests\Feature\Exports;

use App\Exports\LaporanPotonganBulananExport;
use App\Exports\PotonganBankBulanDepanExport;
use App\Models\PotonganBulananDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PotonganBulananExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_rincian_export_includes_all_savings_components_in_combined_column(): void
    {
        PotonganBulananDetail::create($this->detail([
            'simpanan_wajib' => 50_000,
            'simpanan_sukarela' => 25_000,
            'iuran_operasional' => 5_000,
            'total' => 180_000,
        ]));

        $rows = (new LaporanPotonganBulananExport('2026-09'))->array();

        $this->assertSame('BPS PROVINSI BANTEN TAHUN 2026', $rows[1][0]);
        $this->assertSame(80_000, $rows[6][8]);
        $this->assertSame(180_000, $rows[6][12]);
        $this->assertSame(80_000, $rows[7][8]);
        $this->assertSame(180_000, $rows[7][12]);
    }

    public function test_bank_export_filters_selected_bank_and_preserves_account_number(): void
    {
        PotonganBulananDetail::create($this->detail([
            'nama' => 'Anggota BRI',
            'bank' => 'BRI',
            'nomor_rekening' => '0012345678',
            'total' => 100_000,
        ]));
        PotonganBulananDetail::create($this->detail([
            'nama' => 'Anggota BSI',
            'bank' => 'BSI',
            'nomor_rekening' => '0098765432',
            'total' => 200_000,
        ]));

        $rows = (new PotonganBankBulanDepanExport('2026-09', 'BRI'))->array();

        $this->assertSame('DI BRI BULAN SEPTEMBER 2026', $rows[1][0]);
        $this->assertSame('0012345678', $rows[5][3]);
        $this->assertSame(100_000, $rows[5][4]);
        $this->assertSame(100_000, $rows[6][4]);
    }

    private function detail(array $overrides = []): array
    {
        return array_merge([
            'bulan_potongan' => '2026-09',
            'anggota_id' => null,
            'nama' => 'Anggota Uji',
            'bank' => 'BRI',
            'nomor_rekening' => '0011223344',
            'simpanan_wajib' => 0,
            'simpanan_sukarela' => 0,
            'cicilan' => 0,
            'iuran_dharma_wanita' => 0,
            'infaq_pegawai' => 0,
            'tabungan_qurban' => 0,
            'total_titipan' => 0,
            'iuran_operasional' => 0,
            'total' => 0,
            'sisa_pinjaman_lalu' => 0,
            'sisa_pinjaman_sekarang' => 0,
            'tenor' => '-',
            'cicilan_ke' => '-',
        ], $overrides);
    }
}
