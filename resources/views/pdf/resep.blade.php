<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Resep Dokter</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 12px;
      margin: 0;
      padding: 0;
      width: 80mm;
    }

    .container {
      padding: 10px;
    }

    h2,
    h3 {
      text-align: center;
      margin: 5px 0;
    }

    .section {
      margin-bottom: 10px;
    }

    .section-title {
      font-weight: bold;
      margin-bottom: 3px;
    }

    .data-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 3px;
    }

    .resep-list {
      list-style: decimal;
      padding-left: 20px;
    }

    .resep-list li {
      margin-bottom: 5px;
    }

    .total {
      font-weight: bold;
      margin-top: 5px;
    }

    .payment {
      margin-top: 5px;
    }

    hr {
      border: none;
      border-top: 1px dashed #000;
      margin: 10px 0;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Resep Dokter</h2>
    <hr>

    <div class="section">
      <div class="section-title">Data Pasien</div>
      <div class="data-row">
        <span>Nama:</span>
        <span>{{ $data->patient_name }}</span>
      </div>
      <div class="data-row">
        <span>Tanggal Periksa:</span>
        <span>{{ \Carbon\Carbon::parse($data->examined_at)->format('d-m-Y') }}</span>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Catatan Dokter</div>
      <div>{{ $data->notes ?? '-' }}</div>
    </div>

    @if($data->file)
    <div class="section">
      <div class="section-title">File</div>
      <div>{{ $data->file }}</div>
    </div>
    @endif

    <div class="section">
      <div class="section-title">Resep</div>
      <ul class="resep-list">
        @foreach($data->resep_dokter->resep_dokter as $item)
        <li>
          {{ $item['name'] }}
          @if(!empty($item['description']))
          <br><small>ket: {{ $item['description'] }}</small>
          @endif
        </li>
        @endforeach
      </ul>
    </div>

    <div class="section total">
      Total Harga: Rp {{ number_format($data->pembayaran?->total_price ?? 0, 0, ',', '.') }}
    </div>

    <div class="section payment">
      Pembayaran: {{ $data->is_paid ? 'Lunas' : 'Belum lunas' }}
    </div>

    <hr>
    <div style="text-align: center; font-size: 10px;">
      Terima kasih telah berkunjung. Semoga lekas sembuh!
    </div>
  </div>
</body>

</html>