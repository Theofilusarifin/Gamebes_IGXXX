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
  <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
    <span class="fas fa-bullhorn me-1"></span>
    Jangan refresh saat melakukan susun mesin!
    <button tipe="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

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
            {{-- Buton Save/ Edit --}}
            <div class="col-6 d-flex justify-content-end">
              <button class="btn btn-danger me-3" id="edit_udang" tipe="button" onclick="edit('udang')">Edit</button>
              <button disabled class="btn btn-success" id="save_udang" tipe="button" onclick="save('udang')">Save</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          {{-- Alert --}}
          <div class="row">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show" id="alert_udang" style="display:none" role="alert">
                <span class="fas fa-bullhorn me-1" id="alert-body_udang"></span>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            {{-- Table mesin 1-5 --}}
            <table class="table align-items-center table-flush mb-3">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col" style="width: 20%">Mesin 1</th>
                  <th class="border-bottom" scope="col" style="width: 20%">Mesin 2</th>
                  <th class="border-bottom" scope="col" style="width: 20%">Mesin 3</th>
                  <th class="border-bottom" scope="col" style="width: 20%">Mesin 4</th>
                  <th class="border-bottom" scope="col" style="width: 20%">Mesin 5</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_1" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 1)">
                      <option selected disabled value="1">1-Washer</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_2" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 2)">
                      <option disabled selected disabled value="2">2-Steamer</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_3" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 3)">
                      <option disabled selected disabled value="3">3-Shrimp Input</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_4" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 4)">
                      <option selected disabled value="4">4-Sealer</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_5" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 5)">
                      <option selected disabled value="">-- Pilih Mesin 4 --</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
            {{-- Table mesin 6-10 --}}
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col">Mesin 6</th>
                  <th class="border-bottom" scope="col">Mesin 7</th>
                  <th class="border-bottom" scope="col">Mesin 8</th>
                  <th class="border-bottom" scope="col">Mesin 9</th>
                  <th class="border-bottom" scope="col">Mesin 10</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_6" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 6)">
                      <option selected disabled value="">-- Pilih Mesin 5 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_7" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 7)">
                      <option selected disabled value="">-- Pilih Mesin 6 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_8" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 8)">
                      <option selected disabled value="">-- Pilih Mesin 7 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_9" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 9)">
                      <option selected disabled value="">-- Pilih Mesin 8 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_10" tabindex="-1" aria-hidden="true" onchange="setMachine('udang', 10)">
                      <option selected disabled value="">-- Pilih Mesin 9 --</option>
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
            {{-- Buton Save/ Edit --}}
            <div class="col-6 d-flex justify-content-end">
              <button class="btn btn-danger me-3" id="edit_kitosan" tipe="button" onclick="edit('kitosan')">Edit</button>
              <button disabled class="btn btn-success" id="save_kitosan" tipe="button" onclick="save('kitosan')">Save</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          {{-- Alert --}}
          <div class="row">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show" id="alert_kitosan" style="display:none" role="alert">
                <span class="fas fa-bullhorn me-1" id="alert-body_kitosan"></span>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col">Mesin 1</th>
                  <th class="border-bottom" scope="col">Mesin 2</th>
                  <th class="border-bottom" scope="col">Mesin 3</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @if ($machine_kitosan_tersimpan != null)
                  @foreach ($machine_kitosan_tersimpan as $kitosan_machine)
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_{{ $loop->index+1 }}" tabindex="-1" aria-hidden="true" onchange="setMachine('kitosan', {{ $loop->index+1 }})">
                      <option selected disabled value="">{{ $kitosan_machine->name }}</option>
                    </select>
                  </td>
                  @endforeach
                  @else
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_1" tabindex="-1" aria-hidden="true" onchange="setMachine('kitosan', 1)">
                      <option selected disabled value="">-- Lakukan Edit --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_2" tabindex="-1" aria-hidden="true" onchange="setMachine('kitosan', 2)">
                      <option selected disabled value="">-- Pilih Mesin 1 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="kitosan_3" tabindex="-1" aria-hidden="true" onchange="setMachine('kitosan', 3)">
                      <option selected disabled value="">-- Pilih Mesin 2 --</option>
                    </select>
                  </td>
                  @endif
                </tr>
              </tbody>
            </table>
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
            {{-- Buton Save/ Edit --}}
            <div class="col-6 d-flex justify-content-end">
              <button class="btn btn-danger me-3" id="edit_saus" tipe="button" onclick="edit('saus')">Edit</button>
              <button disabled class="btn btn-success" id="save_saus" tipe="button" onclick="save('saus')">Save</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          {{-- Alert --}}
          <div class="row">
            <div class="col-12">
              <div class="alert alert-success alert-dismissible fade show" id="alert_saus" style="display:none" role="alert">
                <span class="fas fa-bullhorn me-1" id="alert-body_saus"></span>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table align-items-center table-flush">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col">Mesin 1</th>
                  <th class="border-bottom" scope="col">Mesin 2</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  @if ($machine_saus_tersimpan != null)
                  @foreach ($machine_saus_tersimpan as $saus_machine)
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_1" tabindex="-1" aria-hidden="true" onchange="setMachine('saus', {{ $loop->index+1 }})">
                      <option selected disabled value="">{{ $saus_machine->name }}</option>
                    </select>
                    </th>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_{{ $loop->index+1 }}" tabindex="-1" aria-hidden="true" onchange="setMachine('saus', {{ $loop->index+1 }})">
                      <option selected disabled value="">{{ $saus_machine->name }}</option>
                    </select>
                  </td>
                  @endforeach
                  @else
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_1" tabindex="-1" aria-hidden="true" onchange="setMachine('saus', 1)">
                      <option selected disabled value="">-- Lakukan Edit --</option>
                    </select>
                    </th>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_2" tabindex="-1" aria-hidden="true" onchange="setMachine('saus', 2)">
                      <option selected disabled value="">-- Pilih Mesin 1 --</option>
                    </select>
                  </td>
                  @endif
                </tr>
              </tbody>
            </table>
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
              <div class="alert alert-success alert-dismissible fade show" id="alert_inventory" style="display:none" role="alert">
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
                  <th class="text-gray-900" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->id}}
                  </th>
                  <th class="text-gray-900" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->machine->name}}
                  </th>
                  <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->performance}}
                  </td>
                  <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">
                    {{$team_machine->season_buy}}
                  </td>
                  <td class="fw-bolder text-gray-500" scope="row" style="width:20%; text-align:center">
                    <button class="btn btn-danger me-3" id="edit_saus" tipe="button" onclick="jual('{{$team_machine->id}}')">
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
        $option_variable = "<option selected disabled>-- Pilih Mesin --</option>";
        $.each(data.available_machines, (key, available_machine) => {
          $option_variable += `<option value=${available_machine.id}>${available_machine.id}-${available_machine.machine.name} (${available_machine.performance})</option>`;
        });
        $('#' + tipe + '_1').html($option_variable);
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
        $option_variable = "<option selected disabled>-- Pilih Mesin --</option>";
        $.each(data.available_machines, (key, available_machine) => {
          $option_variable += `<option value=${available_machine.id}>${available_machine.id}-${available_machine.machine.name}
    (${available_machine.performance})</option>`;
        });
        $('#' + tipe + '_' + index_berikutnya).html($option_variable);
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
      url: "{{ route('peserta.mesin.save') }}",
      data: {
        '_token': $('meta[name="csrf-token"]').attr('content'),
        'team_machine_id': id,
      },
      success: function(data) {
        $table_data = "<tr>";
        $.each(data.team_mesins, (key, team_mesin)) => {
          $table_data += `
    <th class='text-gray-900' scope='row' style='width:20%; text-align:center'>${team_mesin.id}</th>
    <th class='text-gray-900' scope='row' style='width:20%; text-align:center'>${team_mesin.machine.name}</th>
    <td class='fw-bolder text-gray-500' scope='row' style='width:20%; text-align:center'>${team_mesin.performance}</td>
    <td class='fw-bolder text-gray-500' scope='row' style='width:20%; text-align:center'>${team_mesin.season_buy}</td>
    <td class='fw-bolder text-gray-500' scope='row' style='width:20%; text-align:center'>
    <button class="btn btn-danger me-3" id="edit_saus" tipe="button" onclick="jual(${$team_mesin.id})">Jual</button>
    </td>`;
        }
        $table_data += "</tr>";
        $('#inventory_mesin').html($table_data);
      },
      error: function(data) {

      }
    })
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