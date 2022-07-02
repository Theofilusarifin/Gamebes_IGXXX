@extends('peserta.layouts.app')
@section('style')
<style>
  .btn {
    color: #fff !important;
  }

  .card-header {
    background-color: #1F2937 !important;
    color: #fff !important;
  }
</style>
@endsection
@section('content')
<main class="px-5">

  {{-- Mesin Tambahan --}}
  <div class="row my-5">
    <div class="col-12 col-sm-4 col-xl-4">
      <div class="card border-0 shadow">
        <div class="card-header">
          <div class="row d-flex justify-content-center align-items-center">
            <div class="col-6">
              <h1 class="fs-5 fw-bold text-white mb-0">Mesin Tambahan</h1>
            </div>
            <div class="col-6 d-flex justify-content-end">
              <button class="btn btn-danger me-3" id="edit_ac" tipe="button" onclick="edit('ac')">Edit</button>
              <button disabled class="btn btn-success" id="save_ac" tipe="button" onclick="save('ac')">Save</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col">AC</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="ac_1" tabindex="-1" aria-hidden="true">
                      <option selected disabled value="">-- Lakukan Edit --</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Card Udang Kaleng --}}
  <div class="row my-5">
    <div class="col-12 col-sm-12 col-xl-12">
      <div class="card border-0 shadow">
        <div class="card-header">
          <div class="row d-flex justify-content-center align-items-center">
            {{-- Judul --}}
            <div class="col-6">
              <h1 class="fs-5 fw-bold text-white mb-0">Kombinasi Mesin Udang Kaleng</h1>
            </div>
            <div class="col-6 d-flex justify-content-end">
              {{-- Buton Edit --}}
              <button class="btn btn-danger me-3" id="edit_udang" tipe="button" onclick="edit('udang')">Edit</button>

              {{-- Reset Kombinasi Mesin Udang Kaleng --}}
              <button type="button" class="btn btn-info" id="reset_udang" onclick="reset('udang')">
                <i data-feather='rotate-cw' style="width: 18px; height:18px;"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          {{-- Alert --}}
          <div class="row">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show" id="alert_udang" style="display:none"
                role="alert">
                <span class="fas fa-bullhorn me-1" id="alert-body_udang"></span>
              </div>
            </div>
          </div>

          {{-- Effectivity dan Higenity --}}
          <div class="row mb-4">
            {{-- Effectivity --}}
            <div class="col-6">
              @php($udang_efektivitas = 0)
              @if ($machine_combination_udang != null)
              @php($udang_efektivitas = $machine_combination_udang->effectivity)
              @endif
              <div class="progress-wrapper">
                <div class="progress-info">
                  <div class="progress-label">
                    <span class="text-primary">Efektivitas</span>
                  </div>
                  <div class="progress-percentage">
                    <span id="efektivitas-percentage_udang">{{ $udang_efektivitas }}%</span>
                  </div>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-info" role="progressbar" id="progress-efektivitas-percentage_udang"
                    style="width: {{ $udang_efektivitas }}%;" aria-valuenow="{{ $udang_efektivitas }}" aria-valuemin="0"
                    aria-valuemax="100"></div>
                </div>
              </div>
            </div>
            {{-- Higenity --}}
            <div class="col-6">
              @php($udang_kehigenisan = 0)
              @if ($machine_combination_udang != null)
              @php($udang_kehigenisan = $machine_combination_udang->higenity)
              @endif
              <div class="progress-wrapper">
                <div class="progress-info">
                  <div class="progress-label">
                    <span class="text-primary">Kehigenisan</span>
                  </div>
                  <div class="progress-percentage">
                    <span id="kehigenisan-percentage_udang">{{ $udang_kehigenisan }}%</span>
                  </div>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-info" role="progressbar" id="progress-kehigenisan-percentage_udang"
                    style="width: {{ $udang_kehigenisan }}%;" aria-valuenow="{{ $udang_kehigenisan }}" aria-valuemin="0"
                    aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            {{-- Table mesin 1-5 --}}
            <table class="table align-items-center table-flush mb-4">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 1</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 2</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 3</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 4</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 5</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @for ($i = 0; $i < 5; $i++) <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_{{$i+1}}" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', {{$i+1}})">
                      @if ($machine_udang_tersimpan != null)
                      @if (count($machine_udang_tersimpan) >= $i+1)
                      <option selected disabled value="">{{ $machine_udang_tersimpan[$i]->name }}</option>
                      @else
                      @if ($i == 0)
                      <option selected disabled value="">-- Lakukan Edit --</option>
                      @else
                      <option selected disabled value="">-- Pilih Mesin {{ $i }} --</option>
                      @endif
                      @endif
                      @else
                      @if ($i == 0)
                      <option selected disabled value="">-- Lakukan Edit --</option>
                      @else
                      <option selected disabled value="">-- Pilih Mesin {{ $i }} --</option>
                      @endif
                      @endif
                    </select>
                    </td>
                    @endfor
                </tr>
              </tbody>
            </table>
            {{-- Table mesin 6-10 --}}
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 6</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 7</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 8</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 9</th>
                  <th class="border-bottom" scope="col" style="width: 20%; text-align:center">Mesin 10</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @for ($i = 5; $i < 10; $i++) <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_{{$i+1}}" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', {{$i+1}})">
                      @if ($machine_udang_tersimpan != null)
                      @if (count($machine_udang_tersimpan) >= $i+1)
                      <option selected disabled value="">{{ $machine_udang_tersimpan[$i]->name }}</option>
                      @else
                      <option selected disabled value="">-- Pilih Mesin {{ $i }} --</option>
                      @endif
                      @else
                      <option selected disabled value="">-- Pilih Mesin {{ $i }} --</option>
                      @endif
                    </select>
                    </td>
                    @endfor
                </tr>
              </tbody>
            </table>

            {{-- Buton Save --}}
            <div class="d-flex flex-row-reverse">
              <button disabled class="btn btn-success mt-2" id="save_udang" tipe="button"
                onclick="save('udang')">Save</button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Card Mesin Kitosan dan Saus Tomat --}}
  <div class="row my-5">
    {{-- Card Kitosan --}}
    <div class="col-12 col-sm-6 col-xl-6">
      <div class="card border-0 shadow">
        <div class="card-header">
          <div class="row d-flex justify-content-center align-items-center">
            {{-- Judul --}}
            <div class="col-6">
              <h1 class="fs-5 fw-bold text-white mb-0">Kombinasi Mesin Kitosan</h1>
            </div>
            <div class="col-6 d-flex justify-content-end">
              {{-- Buton Edit --}}
              <button class="btn btn-danger me-3" id="edit_kitosan" tipe="button"
                onclick="edit('kitosan')">Edit</button>

              {{-- Reset Kombinasi Mesin Kitosan --}}
              <button type="button" class="btn btn-info" id="reset_kitosan" onclick="reset('kitosan')">
                <i data-feather='rotate-cw' style="width: 18px; height:18px;"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          {{-- Alert --}}
          <div class="row">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show" id="alert_kitosan" style="display:none"
                role="alert">
                <span class="fas fa-bullhorn me-1" id="alert-body_kitosan"></span>
              </div>
            </div>
          </div>

          {{-- Effectivity dan Higenity --}}
          <div class="row mb-4">
            {{-- Effectivity --}}
            <div class="col-6">
              @php($kitosan_efektivitas = 0)
              @if ($machine_combination_kitosan != null)
              @php($kitosan_efektivitas = $machine_combination_kitosan->effectivity)
              @endif
              <div class="progress-wrapper">
                <div class="progress-info">
                  <div class="progress-label">
                    <span class="text-primary">Efektivitas</span>
                  </div>
                  <div class="progress-percentage">
                    <span id="efektivitas-percentage_kitosan">{{ $kitosan_efektivitas }}%</span>
                  </div>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-info" role="progressbar" id="progress-efektivitas-percentage_kitosan"
                    style="width: {{ $kitosan_efektivitas }}%;" aria-valuenow="{{ $kitosan_efektivitas }}"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
            {{-- Higenity --}}
            <div class="col-6">
              @php($kitosan_kehigenisan = 0)
              @if ($machine_combination_kitosan != null)
              @php($kitosan_kehigenisan = $machine_combination_kitosan->higenity)
              @endif
              <div class="progress-wrapper">
                <div class="progress-info">
                  <div class="progress-label">
                    <span class="text-primary">Kehigenisan</span>
                  </div>
                  <div class="progress-percentage">
                    <span id="kehigenisan-percentage_kitosan">{{ $kitosan_kehigenisan }}%</span>
                  </div>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-info" role="progressbar" id="progress-kehigenisan-percentage_kitosan"
                    style="width: {{ $kitosan_kehigenisan }}%;" aria-valuenow="{{ $kitosan_kehigenisan }}"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col" style="width: 33%; text-align:center">Mesin 1</th>
                  <th class="border-bottom" scope="col" style="width: 33%; text-align:center">Mesin 2</th>
                  <th class="border-bottom" scope="col" style="width: 33%; text-align:center">Mesin 3</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @if ($machine_kitosan_tersimpan != null)
                  @foreach ($machine_kitosan_tersimpan as $kitosan_machine)
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_{{ $loop->index+1 }}" tabindex="-1"
                      aria-hidden="true" onchange="setMachine('kitosan', {{ $loop->index+1 }})">
                      <option selected disabled value="">{{ $kitosan_machine->name }}</option>
                    </select>
                  </td>
                  @endforeach
                  @else
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_1" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('kitosan', 1)">
                      <option selected disabled value="">-- Lakukan Edit --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_2" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('kitosan', 2)">
                      <option selected disabled value="">-- Pilih Mesin 1 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_3" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('kitosan', 3)">
                      <option selected disabled value="">-- Pilih Mesin 2 --</option>
                    </select>
                  </td>
                  @endif
                </tr>
              </tbody>
            </table>

            {{-- Buton Save --}}
            <div class="d-flex flex-row-reverse">
              <button disabled class="btn btn-success mt-2" id="save_kitosan" tipe="button"
                onclick="save('kitosan')">Save</button>
            </div>

          </div>
        </div>
      </div>
    </div>
    {{-- Card Saus Tomat --}}
    <div class="col-12 col-sm-6 col-xl-6">
      <div class="card border-0 shadow">
        <div class="card-header">
          <div class="row d-flex justify-content-center align-items-center">
            {{-- Judul --}}
            <div class="col-6">
              <h1 class="fs-5 fw-bold text-white mb-0">Kombinasi Mesin Saus Tomat</h1>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center">
              {{-- Buton Edit --}}
              <button class="btn btn-danger me-3" id="edit_saus" tipe="button" onclick="edit('saus')">Edit</button>

              {{-- Reset Kombinasi Mesin Saus Tomat --}}
              <button type="button" class="btn btn-info" id="reset_saus" onclick="reset('saus')">
                <i data-feather='rotate-cw' style="width: 18px; height:18px;"></i>
              </button>
            </div>

          </div>
        </div>
        <div class="card-body">
          {{-- Alert --}}
          <div class="row">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show" id="alert_saus" style="display:none"
                role="alert">
                <span class="fas fa-bullhorn me-1" id="alert-body_saus"></span>
              </div>
            </div>
          </div>

          {{-- Effectivity dan Higenity --}}
          <div class="row mb-4">
            {{-- Effectivity --}}
            <div class="col-6">
              @php($saus_efektivitas = 0)
              @if ($machine_combination_saus != null)
              @php($saus_efektivitas = $machine_combination_saus->effectivity)
              @endif
              <div class="progress-wrapper">
                <div class="progress-info">
                  <div class="progress-label">
                    <span class="text-primary">Efektivitas</span>
                  </div>
                  <div class="progress-percentage">
                    <span id="efektivitas-percentage_saus">{{ $saus_efektivitas }}%</span>
                  </div>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-info" role="progressbar" id="progress-efektivitas-percentage_saus"
                    style="width: {{ $saus_efektivitas }}%;" aria-valuenow="{{ $saus_efektivitas }}" aria-valuemin="0"
                    aria-valuemax="100"></div>
                </div>
              </div>
            </div>
            {{-- Higenity --}}
            <div class="col-6">
              @php($saus_kehigenisan = 0)
              @if ($machine_combination_saus != null)
              @php($saus_kehigenisan = $machine_combination_saus->higenity)
              @endif
              <div class="progress-wrapper">
                <div class="progress-info">
                  <div class="progress-label">
                    <span class="text-primary">Kehigenisan</span>
                  </div>
                  <div class="progress-percentage">
                    <span id="kehigenisan-percentage_saus">{{ $saus_kehigenisan }}%</span>
                  </div>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-info" role="progressbar" id="progress-kehigenisan-percentage_saus"
                    style="width: {{ $saus_kehigenisan }}%;" aria-valuenow="{{ $saus_kehigenisan }}" aria-valuemin="0"
                    aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col" style="width: 50%; text-align:center">Mesin 1</th>
                  <th class="border-bottom" scope="col" style="width: 50%; text-align:center">Mesin 2</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @if ($machine_saus_tersimpan != null)
                  @foreach ($machine_saus_tersimpan as $saus_machine)
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_{{ $loop->index+1 }}" tabindex="-1"
                      aria-hidden="true" onchange="setMachine('saus', {{ $loop->index+1 }})">
                      <option selected disabled value="">{{ $saus_machine->name }}</option>
                    </select>
                  </td>
                  @endforeach
                  @else
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_1" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('saus', 1)">
                      <option selected disabled value="">-- Lakukan Edit --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_2" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('saus', 2)">
                      <option selected disabled value="">-- Pilih Mesin 1 --</option>
                    </select>
                  </td>
                  @endif
                </tr>
              </tbody>
            </table>

            {{-- Buton Save --}}
            <div class="d-flex flex-row-reverse">
              <button disabled class="btn btn-success mt-2" id="save_saus" tipe="button"
                onclick="save('saus')">Save</button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Inventory Mesin --}}
  <div class="row my-5">
    <div class="col-12 col-sm-12 col-xl-12">
      <div class="card border-0 shadow">
        <div class="card-header">
          <h2 class="fs-5 fw-bold mb-0" style="color:white">Inventory Mesin</h2>
        </div>
        <div class="card-body">
          {{-- Alert --}}
          <div class="row">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show" id="alert_inventory" style="display:none"
                role="alert">
                <span class="fas fa-bullhorn me-1" id="alert-body_inventory"></span>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col" style="width:20%; text-align:center">ID Mesin</th>
                  <th class="border-bottom" scope="col" style="width:20%; text-align:center">Nama Mesin</th>
                  <th class="border-bottom" scope="col" style="width:20%; text-align:center">Peforma Mesin</th>
                  <th class="border-bottom" scope="col" style="width:20%; text-align:center">Season Beli</th>
                  <th class="border-bottom" scope="col" style="width:20%; text-align:center">Action</th>
                </tr>
              </thead>
              <tbody id="inventory_mesin">
                @foreach ($team_machines as $team_machine)
                <tr>
                  <td class="text-gray-900" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->id}}
                  </td>
                  <td class="text-gray-900" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->machine->name}}
                  </td>
                  <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->performance}}
                  </td>
                  <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->season_buy}}
                  </td>
                  <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">
                    <button class="btn btn-danger me-3" tipe="button" onclick="jual('{{$team_machine->id}}')">
                      Jual
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</main>
@endsection

@section('script')
<script>
  function edit(tipe) {
    $.ajax({
      type: 'POST',
      url: "{{ route('peserta.mesin.get') }}",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
      },
      success: function(data) {
        var option_variable = "<option selected disabled>-- Pilih Mesin --</option>";
        $.each(data.available_machines, (key, available_machine) => {
          option_variable += `<option value=${available_machine.id}>${available_machine.id}-${available_machine.machine.name} (${available_machine.performance})</option>`;
        });
        $('#' + tipe + '_1').html(option_variable);
      }
    });

    // Kitosan
    if (tipe == "kitosan") {
      $('#edit_kitosan').attr('disabled', true);
      $('#save_kitosan').attr('disabled', false);
      $('#kitosan_1').attr('disabled', false);
    }
    // Udang
    else if (tipe == "udang") {
      $('#edit_udang').attr('disabled', true);
      $('#save_udang').attr('disabled', false);
      $('#udang_1').attr('disabled', false);
    }
    // Saus Tomat
    else if (tipe == "saus") {
      $('#edit_saus').attr('disabled', true);
      $('#save_saus').attr('disabled', false);
      $('#saus_1').attr('disabled', false);
    }
    // AC
    else if (tipe == "ac") {
      $('#edit_ac').attr('disabled', true);
      $('#save_ac').attr('disabled', false);
      $('#ac_1').attr('disabled', false);
    }
  }

  function setMachine(tipe, index) {
    // Define index berikutnya
    index_berikutnya = index + 1;

    $.ajax({
      type: 'POST',
      url: "{{ route('peserta.mesin.set') }}",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'team_machine_id': $('#' + tipe + '_' + index).val(),
      },
      success: function(data) {
        var option_variable = "<option selected disabled>-- Pilih Mesin --</option>";
        $.each(data.available_machines, (key, available_machine) => {
          option_variable += `<option value=${available_machine.id}>${available_machine.id}-${available_machine.machine.name}
    (${available_machine.performance})</option>`;
        });
        $('#' + tipe + '_' + index_berikutnya).html(option_variable);
      }
    });
    // Kitosan
    if (tipe == "kitosan") {
      $('#kitosan_' + index).attr('disabled', true);
      $('#kitosan_' + index_berikutnya).attr('disabled', false);
    }
    // Udang
    else if (tipe == "udang") {
      $('#udang_' + index).attr('disabled', true);
      $('#udang_' + index_berikutnya).attr('disabled', false);
    }
    // Saus Tomat
    else if (tipe == "saus") {
      $('#saus_' + index).attr('disabled', true);
      $('#saus_' + index_berikutnya).attr('disabled', false);
    }
  }

  function jual(id) {
    $.ajax({
      type: 'POST',
      url: "{{ route('peserta.mesin.jual') }}",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'team_machine_id': id,
      },
      success: function(data) {
        var table_data = "";
        $.each(data.team_mesins, (key, team_mesin) => {
          table_data +=
          ` <tr>
              <td class="text-gray-900" scope="row" style="width:20%;  text-align:center">${team_mesin.id}</td>
              <td class="text-gray-900" scope="row" style="width:20%; text-align:center">${team_mesin.name}</td>
              <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">${team_mesin.performance}</td>
              <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">${team_mesin.season_buy}</td>
              <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">
                <button class="btn btn-danger me-3" tipe="button" onclick="jual(${team_mesin.id})">Jual</button>
              </td>
            </tr>`;
        });
        
        $('#inventory_mesin').html(table_data);

        $('#alert_inventory').hide();
        $('#alert_inventory').show();
        $('#alert-body_inventory').html(data.msg);
        $("#alert_inventory").fadeTo(5000, 500).hide(1000, function() {
            $("#alert_inventory").hide(1000);
          });
        if (data.status == "success") {
          $('#alert_inventory').removeClass("alert-danger");
          $('#alert_inventory').addClass("alert-success");
        } else if (data.status == "error") {
          $('#alert_inventory').removeClass("alert-success");
          $('#alert_inventory').addClass("alert-danger");
        }
      }
    });
  }

  function reset(tipe) {
    $.ajax({
      type: 'POST',
      url: "{{ route('peserta.mesin.reset') }}",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'jenis_kombinasi': tipe,
      },
      success: function(data) {
        // Ubah percentage
        $("#efektivitas-percentage_"+tipe).html(0 + "%");
        $("#kehigenisan-percentage_"+tipe).html(0 + "%");
        // Ubah progress bar
        $("#progress-kehigenisan-percentage_"+tipe).css("width",0 +"%");
        $("#progress-efektivitas-percentage_"+tipe).css("width",0 +"%");
        $("#progress-kehigenisan-percentage_"+tipe).attr("aria-valuenow",0);
        $("#progress-efektivitas-percentage_"+tipe).attr("aria-valuenow",0);

        //Message
        $('#alert_' + tipe).hide();
        $('#alert_' + tipe).show();
        $('#alert-body_' + tipe).html(data.msg);
        $("#alert_" + tipe).fadeTo(5000, 500).hide(1000,
        function() {
          $("#alert_" + tipe).hide(1000);
        });
        if (data.status == "success") {
          $('#alert_' + tipe).removeClass("alert-danger");
          $('#alert_' + tipe).addClass("alert-success");
        } else if (data.status == "error") {
          $('#alert_' + tipe).removeClass("alert-success");
          $('#alert_' + tipe).addClass("alert-danger");
        }
      }
    });

  }

  function save(tipe) {
    $susunan_mesin = [];
    if (tipe == "kitosan") {
      for (let i = 1; i <= 3; i++) {
        $('#kitosan_' + i).attr('disabled', true);
        $susunan_mesin[i - 1] = $('#kitosan_' + i).val();
      }
    } else if (tipe == "udang") {
      for (let i = 1; i <= 10; i++) {
        $('#udang_' + i).attr('disabled', true);
        $susunan_mesin[i - 1] = $('#udang_' + i).val();
      }
    } else if (tipe == "saus") {
      for (let i = 1; i <= 2; i++) {
        $('#saus_' + i).attr('disabled', true);
        $susunan_mesin[i - 1] = $('#saus_' + i).val();
      }
    }

    $.ajax({
      type: 'POST',
      url: "{{ route('peserta.mesin.save') }}",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'susunan_mesin': $susunan_mesin,
        'tipe': tipe,
      },
      success: function(data) {
        var efektivitas = 0;
        var kehigenisan = 0;
        // Logic disini
        // Untuk Udang
        if (tipe == "udang"){
          if (data.machine_combination_udang != null){
            efektivitas = data.machine_combination_udang.effectivity;
            kehigenisan = data.machine_combination_udang.higenity;
          }
        }

        // Untuk Kitosan
        if (tipe == "kitosan"){
          if (data.machine_combination_kitosan != null){
            efektivitas = data.machine_combination_kitosan.effectivity;
            kehigenisan = data.machine_combination_kitosan.higenity;
          }
        }

        // Untuk Saus
        if (tipe == "saus"){
          if (data.machine_combination_saus != null){
            efektivitas = data.machine_combination_saus.effectivity;
            kehigenisan = data.machine_combination_saus.higenity;
          }
        }
        // Ubah percentage
        $("#efektivitas-percentage_"+tipe).html(efektivitas + "%");
        $("#kehigenisan-percentage_"+tipe).html(kehigenisan + "%");
        // Ubah progress bar
        $("#progress-kehigenisan-percentage_"+tipe).css("width",kehigenisan +"%");
        $("#progress-efektivitas-percentage_"+tipe).css("width",efektivitas +"%");
        $("#progress-kehigenisan-percentage_"+tipe).attr("aria-valuenow",kehigenisan);
        $("#progress-efektivitas-percentage_"+tipe).attr("aria-valuenow",efektivitas);


        //Message
        $('#edit_' + tipe).attr('disabled', false);
        $('#save_' + tipe).attr('disabled', true);
        $('#alert_' + tipe).hide();
        $('#alert_' + tipe).show();
        $('#alert-body_' + tipe).html(data.msg);
        $("#alert_" + tipe).fadeTo(5000, 500).hide(1000,
          function() {
            $("#alert_" + tipe).hide(1000);
          });
        if (data.status == "success") {
          $('#alert_' + tipe).removeClass("alert-danger");
          $('#alert_' + tipe).addClass("alert-success");
        } else if (data.status == "error") {
          $('#alert_' + tipe).removeClass("alert-success");
          $('#alert_' + tipe).addClass("alert-danger");
        }
      }
    });
  }
</script>
@endsection