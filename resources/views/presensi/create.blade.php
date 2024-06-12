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
        <input type="text" id="lokasi">
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

    function successCallback(position) {
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        lokasi.value = latitude + ", " + longitude;
        
        var map = L.map('map');

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        // marker at the current position
        var marker = L.marker([latitude, longitude]).addTo(map);
        
        // circle at the given coordinates
        var circle = L.circle([latitude, longitude], {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.5,
            radius: 20
        }).addTo(map);

        // Create a bounding box that includes both the marker and the circle
        var bounds = L.latLngBounds([
            // [latitude, longitude],          // marker position
            [latitude, longitude],          // marker position
            [latitude, longitude]        // circle position
        ]);

        // Adjust the map view to fit the bounding box
        map.fitBounds(bounds);
    }

    function errorCallback(){

    }

    $("#takeabsen").click(function(e){
        Webcam.snap(function(uri){
            image = uri;
        });
        var lokasi = $("#lokasi").val();
        
        $.ajax({
        type: 'POST',
        url: '/presensi/store',
        data: {
            _token: "{{ csrf_token() }}",
            image: image,
            lokasi: lokasi
        },
        cache: false,
        success: function(respond) {
        var status = respond.split("|");
            if (status[0] == "success") {
                if (status[2] == "in") {
                    notifikasi_in.play();
                    Swal.fire({
                        title: "Berhasil!",
                        text: status[1],
                        icon: "success",
                        confirmButtonText: "kembali"
                    });
                    setTimeout("location.href='/dashboard'", 3000);
                } else if (status[2] == "out") {
                    notifikasi_out.play();
                    Swal.fire({
                        title: "Terupdate!",
                        text: status[1],
                        icon: "success",
                        confirmButtonText: "kembali"
                    });
                    setTimeout("location.href='/dashboard'", 3000);
                }
                } else {
                    Swal.fire({
                        title: "Gagal!",
                        text: status[1],
                        icon: "error",
                        confirmButtonText: "kembali"
                    });
                    // setTimeout("location.href='/dashboard'", 3000);
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