<div class="row mt-3">
    <div class="col-2">

    </div>
    <div class="col d-flex justify-content-center">
        <div class="row">
            <div class="col d-flex justify-content-center mt-4">
                <span class="text-center">Kyle's Song Ranker - {{ now()->format('Y')}}</span>
            </div>
        </div>
    </div>
    <div class="col-2 d-flex justify-content-end mx-4 mt-3">
        <h1 style="cursor: pointer;">
            <a style="text-decoration: none;" href="https://ko-fi.com/spacelampsix" target="_blank">
                <i class="fa-regular fa-credit-card fa-beat-fade fa-lg"></i>
            </a>
        </h1>
    </div>
</div>


<input type="hidden" id="authid" value="{{ auth()->id() }}">