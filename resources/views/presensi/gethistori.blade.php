@if ($histori->isEmpty())
<div class="alert alert-warning">
    <p class="text-center">Data tidak tersedia.</p>
</div>
@endif
@foreach ($histori as $d)
<ul class="listview image-listview">
    <li>
        <div class="item">
            @php
                $path = Storage::url('absensi/'.$d->foto_in);
            @endphp
            <div class="in">
                <img src="{{ url($path) }}" class="image">
                <div>
                    {{ date("d-m-Y", strtotime($d->tgl_presensi)) }}
                </div>
                <span class="badge {{ $d->jam_in < "08:00" ? "bg-success" : "bg-danger" }}">
                {{ $d->jam_in }}
                </span>
            </div>
        </div>
    </li>
</ul>
@endforeach