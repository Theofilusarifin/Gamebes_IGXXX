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
    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  {{-- Mesin Tambahan --}}
  <div class="row my-5">
    <div class="col-12 col-sm-3 col-xl-3">
      <div class="card border-0 shadow">
        <div class="card-header">
          <div class="row d-flex justify-content-center align-items-center">
            {{-- Judul --}}
            <div class="col-6">
              <h1 class="fs-5 fw-bold text-white mb-0">Mesin Tambahan</h1>
            </div>
            {{-- Buton Save/ Edit --}}
            <div class="col-6 d-flex justify-content-end">
              <button class="btn btn-danger me-3" id="edit_ac" type="button" onclick="edit('ac')">Edit</button>
              <button disabled class="btn btn-success" id="save_ac" type="button" onclick="save('ac')">Save</button>
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
              <button class="btn btn-danger me-3" id="edit_udang" type="button" onclick="edit('udang')">Edit</button>
              <button disabled class="btn btn-success" id="save_udang" type="button"
                onclick="save('udang')">Save</button>
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
          <div class="table-responsive">
            {{-- Table mesin 1-5 --}}
            <table class="table align-items-center table-flush mb-3">
              <thead class="thead-light">
                <tr>
                  <th class="border-bottom" scope="col">Mesin 1</th>
                  <th class="border-bottom" scope="col">Mesin 2</th>
                  <th class="border-bottom" scope="col">Mesin 3</th>
                  <th class="border-bottom" scope="col">Mesin 4</th>
                  <th class="border-bottom" scope="col">Mesin 5</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_1" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 1)">
                      <option selected disabled value="">-- Lakukan Edit --</option>
                    </select>
                    </th>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_2" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 2)">
                      <option disabled selected disabled value="">-- Pilih Mesin 1 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_3" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 3)">
                      <option disabled selected disabled value="">-- Pilih Mesin 2 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_4" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 4)">
                      <option selected disabled value="">-- Pilih Mesin 3 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_5" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 5)">
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
                    <select disabled class="select2 form-select" id="udang_6" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 6)">
                      <option selected disabled value="">-- Pilih Mesin 5 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_7" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 7)">
                      <option selected disable dvalue="">-- Pilih Mesin 6 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_8" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 8)">
                      <option selected disabled value="">-- Pilih Mesin 7 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_9" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 9)">
                      <option selected disabled value="">-- Pilih Mesin 8 --</option>
                    </select>
                  </td>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="udang_10" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('udang', 10)">
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
              <button class="btn btn-danger me-3" id="edit_kitosan" type="button"
                onclick="edit('kitosan')">Edit</button>
              <button disabled class="btn btn-success" id="save_kitosan" type="button"
                onclick="save('kitosan')">Save</button>
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
              <button class="btn btn-danger me-3" id="edit_saus" type="button" onclick="edit('saus')">Edit</button>
              <button disabled class="btn btn-success" id="save_saus" type="button" onclick="save('saus')">Save</button>
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
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_1" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('saus', 1)">
                      <option selected disabled value="">-- Lakukan Edit --</option>
                    </select>
                    </th>
                  <td class="text-gray-900" scope="row">
                    <select disabled class="select2 form-select" id="saus_2" tabindex="-1" aria-hidden="true"
                      onchange="setMachine('saus', 2)">
                      <option selected disabled value="">-- Pilih Mesin 1 --</option>
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
                  <th class="border-bottom" scope="col">ID Mesin</th>
                  <th class="border-bottom" scope="col">Nama Mesin</th>
                  <th class="border-bottom" scope="col">Peforma Mesin</th>
                  <th class="border-bottom" scope="col">Season Beli</th>
                  <th class="border-bottom" scope="col">Action</th>
                </tr>
              </thead>
              <tbody>
                @php
                $counter1 = 0;
                @endphp
                @foreach ($display_team_mesins as $mesin)
                <tr>
                  <th class="text-gray-900" scope="row">{{$mesin->id}}</th>
                  <th class="text-gray-900" scope="row">{{$mesin->machine->name}}</th>
                  <td class="fw-bolder text-gray-500">{{$mesin->performance}}</td>
                  <td class="fw-bolder text-gray-500">{{$mesin->season_buy}}</td>
                  <td class="fw-bolder text-gray-500">
                    <button class="btn btn-danger me-3" id="edit_saus" type="button" onclick="jual('{{$mesin->id}}')">
                      Jual
                    </button>
                  </td>
                </tr>
                @php
                $counter1+=1;
                @endphp
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
  function edit(type) {
    $.ajax({
      type: 'POST',
      url: "{{ route('peserta.mesin.get') }}",
      data:{
        '_token': $('meta[name="csrf-token"]').attr('content'),
        },
      success: function (data) {
        $option_variable = "<option selected disabled>-- Pilih Mesin --</option>";
        $.each(data.avaiable_machines, (key, avaiable_machine) => {
          $option_variable += `<option value=${avaiable_machine.id}>${avaiable_machine.id}-${avaiable_machine.machine.name} (${avaiable_machine.performance})</option>`;
        });
        $('#'+type+'_1').html($option_variable);
      }
    });
    // Kitosan
    if(type == "kitosan"){
      $('#edit_kitosan').attr('disabled', true);
      $('#save_kitosan').attr('disabled', false);
      $('#kitosan_1').attr('disabled', false);
    }
    // Udang
    else if(type == "udang"){
      $('#edit_udang').attr('disabled', true);
      $('#save_udang').attr('disabled', false);
      $('#udang_1').attr('disabled', false);
    }
    // Saus Tomat
    else if(type == "saus"){
      $('#edit_saus').attr('disabled', true);
      $('#save_saus').attr('disabled', false);
      $('#saus_1').attr('disabled', false);
    }
    // AC
    else if(type == "ac"){
      $('#edit_ac').attr('disabled', true);
      $('#save_ac').attr('disabled', false);
      $('#ac_1').attr('disabled', false);
    }
  }
  
    function setMachine(type, index) {
      // Define index berikutnya
      index_berikutnya = index+1;
      $.ajax({
        type: 'POST',
        url: "{{ route('peserta.mesin.set') }}",
        data:{
          '_token': $('meta[name="csrf-token"]').attr('content'),
          'team_machine_id': $('#'+type+'_'+index).val(),
        },
        success: function (data) {
          $option_variable = "<option selected disabled>-- Pilih Mesin --</option>";
          $.each(data.avaiable_machines, (key, avaiable_machine) => {
          $option_variable += `<option value=${avaiable_machine.id}>${avaiable_machine.id}-${avaiable_machine.machine.name}
            (${avaiable_machine.performance})</option>`;
          });
          $('#'+type+'_'+index_berikutnya).html($option_variable);
        }
      });
      // Kitosan
      if(type == "kitosan"){
        $('#kitosan_'+index).attr('disabled', true);
        $('#kitosan_'+index_berikutnya).attr('disabled', false);
      }
      // Udang
      else if(type == "udang"){
        $('#udang_'+index).attr('disabled', true);
        $('#udang_'+index_berikutnya).attr('disabled', false);
      }
      // Saus Tomat
      else if(type == "saus"){
        $('#saus_'+index).attr('disabled', true);
        $('#saus_'+index_berikutnya).attr('disabled', false);
      }
    }


    function save(type) {
      $susunan_mesin = [];
      if (type == "kitosan"){
        for (let i = 1; i<=3; i++) {
          $susunan_mesin[i-1] = $('#kitosan_'+i).val();
        }
      }
      else if (type == "udang"){
        for (let i = 1; i<=10; i++) {
          $susunan_mesin[i-1] = $('#udang_'+i).val();
        }
      }
      else if (type == "saus"){
        for (let i = 1; i<=2; i++) {
          $susunan_mesin[i-1] = $('#saus_'+i).val();
        }
      }
      $.ajax({
        type: 'POST',
        url: "{{ route('peserta.mesin.save') }}",
        data:{
          '_token': $('meta[name="csrf-token"]').attr('content'),
          'susunan_mesin': $susunan_mesin,
        },
        success: function (data) {
          $('#edit_'+type).attr('disabled', false);
          $('#save_'+type).attr('disabled', true);
          $('#alert_'+type).hide();
          $('#alert_'+type).show();
          $('#alert-body_'+type).html(data.msg);

          $("#alert_"+type).fadeTo(5000, 500).hide(1000, function(){
              $("#alert_"+type).hide(1000);
          });
          if (data.status == "success") {
            $('#alert_'+type).removeClass("alert-danger");
            $('#alert_'+type).addClass("alert-success");
          }
          else if (data.status == "error") {
            $('#alert_'+type).removeClass("alert-success");
            $('#alert_'+type).addClass("alert-danger");
          }
        }
      });
    }
</script>
@endsection