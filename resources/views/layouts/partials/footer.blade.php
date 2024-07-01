<section>
    <div class="container text-center text-md-start mt-5">
        <div class="row mt-3">
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <h6 class="text-uppercase fw-bold mb-4">
                <i class="fas fa-music me-3"></i>Song Rank - by Kyle - {{ now()->format('Y') }}
            </h6>
            <p>
                Please consider supporting song rank to keep it free for everyone and free of ads.
            </p>
        </div>
        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
            <h6 class="text-uppercase fw-bold mb-4">
                Song Rank
            </h6>
            <p>
                <a href="https://github.com/kylenotfound/spotify-song-ranker" class="text-reset" target="_blank">Source</a>
            </p>
            <p>
                <a href="https://github.com/kylenotfound/spotify-song-ranker/issues" class="text-reset" target="_blank">Issues</a>
            </p>
            <p>
                <a href="https://github.com/kylenotfound/spotify-song-ranker/blob/master/contributing.md" class="text-reset" target="_blank">Contributing</a>
            </p>
        </div>
        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
            <h6 class="text-uppercase fw-bold mb-4">
                About
            </h6>
            <p>
                <a href="{{ route('about') }}" class="text-reset">About</a>
            </p>
            <p>
                <a href="https://ko-fi.com/spacelampsix" class="text-reset" target="_blank">Donate</a>
            </p>
        </div>
        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
            <!-- Links -->
            <h6 class="text-uppercase fw-bold mb-4">Me</h6>
            <p>
                <a href="https://kyleevangelisto.com/" class="text-reset" target="_blank">Portfolio</a>
            </p>
            <p>
                <a href="https://github.com/kylenotfound" class="text-reset" target="_blank">Github</a>
            </p>
            <p>
                <a href="https://discord.gg/zXe9kqyFEJ" class="text-reset" target="_blank">Discord</a>
            </p>
        </div>
      </div>
    </div>
</section>

<input type="hidden" id="authid" value="{{ auth()->id() }}">