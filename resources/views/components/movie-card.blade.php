<div class="col-lg-6">
    <div class="card mb-3" style="max-width: 540px;">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="/images/{{ $movie['foto_sampul'] }}" class="img-fluid rounded-start" alt="{{ $movie['judul'] }}">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">{{ $movie['judul'] }}</h5>
                    <p class="card-text">{{ $movie['sinopsis'] }}</p>
                    <a href="/movie/{{ $movie['id'] }}" class="btn btn-success">Lihat Selanjutnya</a>
                </div>
            </div>
        </div>
    </div>
</div>
