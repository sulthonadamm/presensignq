@extends('layouts.presensi')
@section('header')

<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="javascript:;" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
        </div>
    <div class="pageTitle">E-Presensi</div>
    <div class="right"></div>
</div>
    <!-- * App Header -->
<style>
     /*  */
    .webcam-capture, .webcam-capture video{
        display: inline-block;
        width: 100% !important;
        margin: auto;
        height: auto !important;
        border-radius: 20px;
    }

    #map {
        height: 180px; 
    }
</style>
@endsection

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@section ('content')
<!-- App Capsule -->
<div class="row" style="margin-top: 60px">
    <div class="col">
        <input type="hidden" id="lokasi">
        <div class="webcam-capture"></div>
    </div>
</div>
<div class="col mx-auto">
    <div class="btn-group mt-2">
        @if ($cek > 0)
        <button id="takeabsen" type="button" class="btn btn-danger">
            <ion-icon name="camera-outline"></ion-icon> Absen Pulang</button>
        @else
        <button id="takeabsen" type="button" class="btn btn-primary">
            <ion-icon name="camera-outline"></ion-icon> Absen Masuk</button>
         @endif
    </div>
</div>
<div class="row mt-2">
    <div class="col">
        <div id="map"></div>
    </div>
</div>
<!-- * App Capsule -->
<audio id="notifikasi_in">
    <source src=" {{ asset('assets/sound/audio-notif-in.mp3')}}" type="audio/mpeg">
</audio>
<audio id="notifikasi_out">
    <source src=" {{ asset('assets/sound/audio-notif-out.mp3')}}" type="audio/mpeg">
</audio>
<audio id="notifikasi_failed">
    <source src=" {{ asset('assets/sound/failed.mp3')}}" type="audio/mpeg">
</audio>
@endsection

@push('myscript')
<script>
    Webcam.set({
        height:480,
        width:640,
        image_format:'jpeg',
        jpeg_quality: 80
    });

    var notifikasi_in = document.getElementById('notifikasi_in');

    Webcam.attach('.webcam-capture');

    var lokasi = document.getElementById('lokasi');
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(successCallback, errorCallback);
    }

    function successCallback(position){
        lokasi.value = position.coords.latitude + "," + position.coords.longitude;
        var map = L.map('map').setView([0, 0], 20);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
         maxZoom: 18,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        // marker
        var marker = L.marker([0, 0]).addTo(map);
        var circle = L.circle([0, 0], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 15
        }).addTo(map);
    }

    function errorCallback(){

    }

    $("#takeabsen").click(function(e){
        Webcam.snap(function(uri){
            image = uri;
        });
        var lokasi = $("#lokasi").val();
        
        $.ajax({
            type:'POST',
            url:'/presensi/store',
            data:{
                _token:"{{ csrf_token() }}",
                image:image,
                lokasi:lokasi
            },
            cache:false,
            success:function(respond){
                // var status = respond.split("|");
                if(respond.message === 'Data presensi berhasil disimpan.'){
                    notifikasi_in.play();
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Terima Kasih & Selamat Bekerja",
                        icon: "success",
                        confirmButtonText: "kembali"
                        
                    })
                    setTimeout("location.href='/dashboard'", 3000);
                } else if(respond.message === 'Data presensi berhasil diupdate.') {
                    notifikasi_out.play();
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Terima Kasih & Hati - hati di jalan!",
                        icon: "success",
                        confirmButtonText: "kembali"
                    })
                    setTimeout("location.href='/dashboard'", 3000);
                } else if (respond.message === 'Anda diluar jangkauan. Tidak dapat melanjutkan.') {
                    notifikasi_failed.play();
                    Swal.fire({
                        title: "Error",
                        text: "Anda diluar jangkauan. Tidak dapat melanjutkan.",
                        icon: "error",
                        confirmButtonText: "kembali"
                    })
                } else if (respond.message === 'Maaf, sistem mungkin sedang error. Harap hubungi teknisi.') {
                    notifikasi_failed.play();
                    Swal.fire({
                        title: "Error",
                        text: "Maaf, sistem mungkin sedang error. Harap hubungi teknisi.",
                        icon: "error",
                        confirmButtonText: "kembali"
                    })
                    setTimeout("location.href='/dashboard'", 3000);
                }
                alert("Nilai radius: " + $radius);
            },
            error: function(xhr, status, error) {
            console.error(xhr.responseText);
            alert('Error occurred while processing request: ' + xhr.responseText);
            },
        });
        console.log(image);
    });

</script>
@endpush