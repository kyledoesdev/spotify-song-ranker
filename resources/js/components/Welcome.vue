<template>
    <section class="mb-4">
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="row">
                    <div class="col d-flex justify-content-center">
                        <h5 class="display-3 mt-4">Spotify Discography Ranker</h5>
                    </div>
                </div>
                <hr />
            </div>
        </div>
        <div class="card login-card border border-3 border-dark">
            <div class="card-body login-card-body">
                <div class="row" style="height: 60vh;">
                    <div class="col d-flex justify-content-center align-items-center">
                        <a class="btn btn-lg border border-dark border-2 text-white explore-btn gradient-background" href="/explore">
                            <span class="k-line">Explore Rankings</span>
                        </a>
                    </div>
                    <div class="col d-flex justify-content-center align-items-center">
                        <a class="btn btn-lg border border-dark border-2 text-white login-btn gradient-background" :href="buttonhref">
                            <span v-if="this.authid" class="k-line">View Dashboard</span>
                            <span v-else class="k-line">Login & Start</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <h5>webring:</h5>
            <hr class="mt-0" />
            <div class="row d-flex">
                <div class="col-auto">
                    <a href="https://insect.christmas" target="_blank">
                        <img class="mx-1" :src="asset('insect.gif')" />
                    </a>
                </div>

                <div class="col-auto">
                    <a href="https://kyleevangelisto.com" target="_blank">
                        <div class="portfolio-webring">
                            <span class="text-white" style="font-size: 12px;">my portfolio</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
    </section>
</template>

<script>
    export default {
        computed: {
            buttonhref() {
                return this.authid ? '/home' : '/login/spotify';
            }
        }
    }
</script>
<style>
    .login-card {
        background: url('/public/cover.png') center/cover no-repeat;
        min-height: 40vh;
    }

    .login-btn {
        padding: 2vh;
    }

    .explore-btn {
        padding: 2vh;
    }

    p {
        font-size: 2vh;
    }

    @media (max-width: 768px) {
        .welcome-card {
            overflow-y: scroll; 
            max-height: 40vh;
        }

        p {
            font-size: 1vh;
        }
    }

    .portfolio-webring {
        background: linear-gradient(45deg, #00E5FF 25%, #1200FF);
        background-size: 400% 400%;
        animation: webring 5s linear infinite;
        min-width: 88px; 
        max-width: 88px; 
        min-height: 31px; 
        max-height: 31px; 
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid black;
    }
    
    @keyframes webring {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }
</style>