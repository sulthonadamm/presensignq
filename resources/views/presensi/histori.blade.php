@extends('layouts.presensi')
@section('header')
{{-- app header --}}
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="javascript:;" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
            </div>
        <div class="pageTitle">History</div>
        <div class="right"></div>
    </div>
{{-- end app header --}}
@endsection
@section('content')
<div class="row" style="margin-top: 4rem">
    <div class="col">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="bulan" id="bulan" class="form-control">
                        @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>{{ $namabulan[$i] }}</option>    
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="tahun" id="tahun" class="form-control">
                        <option value="">tahun</option>
                        @php
                            $tahunmulai = 2024;
                            $tahunskrng = date("Y");
                        @endphp
                        @for ($tahun=$tahunmulai; $tahun<= $tahunskrng; $tahun++)
                        <option value="{{ $tahun }}"{{ date("Y") == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-primary btn-block" id="getdata">
                        <ion-icon name="search-outline"></ion-icon>
                        search
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col" id="showhistori"></div>
</div>
@endsection

@push('myscript')
    <script>
        $(function(){
            $("#getdata"). click(function(e){
                var bulan = $("#bulan").val();
                var tahun = $("#tahun").val();
                $.ajax({
                    type : 'POST',
                    url : '/gethistori',
                    data : {
                        _token : "{{ csrf_token() }}",
                        bulan : bulan,
                        tahun : tahun
                    },
                    chace : false,
                    success : function(respond) {
                        $('#showhistori').html(respond);
                    }
                });
            });
        });
    </script>
@endpush