<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40"
      lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Kuasa Pendebetan Rekening</title>
    <!--[if gte mso 9]><xml>
     <w:WordDocument>
      <w:View>Print</w:View>
      <w:Zoom>100</w:Zoom>
      <w:DoNotOptimizeForBrowser/>
     </w:WordDocument>
    </xml><![endif]-->
    <style>
        @page {
            size: 21.0cm 29.7cm;
            mso-page-orientation: portrait;
            margin: 0.8cm 2.2cm 2.6cm 2.2cm;
            mso-footer: f1;
            mso-footer-margin: 0.8cm;
        }

        body {
            font-family: Cambria, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
        }

        .header {
            margin: 0 0 4px;
            text-align: left;
        }

        .logo {
            width: 56px;
            height: 13px;
            object-fit: contain;
            display: block;
            margin: 0;
        }

        .title {
            margin: 32px 0 22px;
            text-align: center;
            font-size: 12pt;
            font-weight: 700;
            text-decoration: underline;
        }

        .spacer {
            height: 18px;
        }

        .signer-block {
            margin: 10px 0 20px;
        }

        .meta-table {
            border-collapse: collapse;
            margin-left: 24px;
        }

        .meta-table td {
            padding: 0;
            vertical-align: top;
        }

        .meta-table td:nth-child(1) {
            width: 160px;
        }

        .meta-table td:nth-child(2) {
            width: 15px;
            text-align: center;
        }

        .paragraph {
            text-align: justify;
            margin: 12px 0;
            line-height: 1.6;
            mso-line-height-rule: exactly;
        }

        .bold {
            font-weight: 700;
        }

        .underline {
            text-decoration: underline;
        }

        .signatures {
            margin-top: 48px;
            width: 100%;
            border-collapse: collapse;
        }

        .signatures td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .signatures .right {
            text-align: center;
        }

        .signatures .left {
            text-align: center;
        }

        .ttd-space {
            height: 42px;
        }

        .signature-name {
            font-weight: 700;
            margin-bottom: 0;
            line-height: 1;
        }

        .signature-nip-text {
            font-weight: 400;
            line-height: 1;
        }

        .page-footer {
            text-align: center;
            font-size: 8pt;
            line-height: 1.4;
        }

        @media print {
            .page-footer-fixed {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 8pt;
                line-height: 1.4;
            }
        }

    </style>
</head>
<body>
    <div class="header">
        @if($logoDataUri)
            <img src="{{ $logoDataUri }}" alt="Logo BPS" class="logo">
        @endif
    </div>

    <div class="title">SURAT KUASA PENDEBETAN REKENING</div>

    <p>Yang bertanda tangan dibawah ini, Saya :</p>

    <div class="signer-block">
        <p>I.&nbsp;&nbsp;&nbsp;{{ $suratKuasa['penandatangan_1']['jabatan_pengantar'] }}</p>
        <table class="meta-table">
            <tr>
                <td>Nama</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td class="bold">&nbsp;{{ $suratKuasa['penandatangan_1']['nama'] }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td>&nbsp;{{ $suratKuasa['penandatangan_1']['nip'] }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td>&nbsp;{{ $suratKuasa['penandatangan_1']['jabatan'] }}</td>
            </tr>
        </table>
    </div>

    <div class="signer-block">
        <p>II.&nbsp;&nbsp;{{ $suratKuasa['penandatangan_2']['jabatan_pengantar'] }}</p>
        <table class="meta-table">
            <tr>
                <td>Nama</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td class="bold">&nbsp;{{ $suratKuasa['penandatangan_2']['nama'] }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td>&nbsp;{{ $suratKuasa['penandatangan_2']['nip'] }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</td>
                <td>&nbsp;{{ $suratKuasa['penandatangan_2']['jabatan'] }}</td>
            </tr>
        </table>
    </div>

    <p class="paragraph">
        Dengan ini memberikan kuasa kepada {{ $suratKuasa['bank_tujuan']['nama'] }} untuk melakukan pendebetan rekening
        pegawai {{ $suratKuasa['instansi'] }} untuk
        <span class="bold underline">Potongan Koperasi {{ ucfirst($bulanLabel) }}</span>
        sebesar <span class="bold">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</span>
        - ({{ $totalSetoranTerbilang }} rupiah) dari rekening pegawai {{ $suratKuasa['instansi'] }}
        yang selanjutnya ditransfer ke nomor rekening
        {{ $suratKuasa['bank_tujuan']['nama'] }} : <span class="bold">{{ $suratKuasa['bank_tujuan']['nomor_rekening'] }}</span>
        atas nama <span class="bold">{{ $suratKuasa['bank_tujuan']['atas_nama'] }}</span>,
        {{ $suratKuasa['bank_tujuan']['keterangan'] }} (Daftar Nama dan Nomor Rekening Pegawai terlampir). 
    </p>

    <p>Demikian surat kuasa ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <table class="signatures">
        <tr>
            <td class="left">{{ $suratKuasa['penandatangan_2']['jabatan_pengantar'] }},</td>
            <td class="right">{{ $suratKuasa['kota'] }}, {{ $suratKuasa['tanggal'] }}<br>{{ $suratKuasa['penandatangan_1']['jabatan_pengantar'] }},</td>
        </tr>
        <tr>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td class="left signature-name"><b>{{ $suratKuasa['penandatangan_2']['nama'] }}</b><br><span class="signature-nip-text">NIP : {{ $suratKuasa['penandatangan_2']['nip'] }}</span></td>
            <td class="right signature-name"><b>{{ $suratKuasa['penandatangan_1']['nama'] }}</b><br><span class="signature-nip-text">NIP : {{ $suratKuasa['penandatangan_1']['nip'] }}</span></td>
        </tr>
    </table>

    <!--[if gte mso 9]-->
    <div style="mso-element:footer" id="f1">
        <p class="MsoFooter" style="text-align:center;font-size:8pt;line-height:1.4;margin:0;font-family:Cambria,serif;">
            Jl. Palima Raya, Kawasan Pusat Pemerintahan Provinsi Banten (KP3B)<br>
            Kav. H1-2 Telp.(0254)267027, Fax. (0254)267026, Curug, Serang
        </p>
    </div>
    <!--[endif]-->

    <!--[if !mso]><!-->
    <div class="page-footer-fixed">
        Jl. Palima Raya, Kawasan Pusat Pemerintahan Provinsi Banten (KP3B)<br>
        Kav. H1-2 Telp.(0254)267027, Fax. (0254)267026, Curug, Serang
    </div>
    <!--<![endif]-->
</body>
</html>
