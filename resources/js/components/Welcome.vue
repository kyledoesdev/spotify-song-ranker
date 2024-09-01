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
                <!-- <div class="welcome-card card mb-4">
                    <div class="card-body">
                        <p>
                            Welcome to Kyle's Spotify Discography Ranker. This application will allow you to log in with your
                            spotify account and rank your favorite artists' entire discography. This is not a bracket style ranking system.
                            We use a merge-sort type algorithm to rank every possible combination of tracks as efficently as possible.
                        </p>
                        <p>
                            This application isn't limited to an artists' entire discography either, you can rank indivdual albums, a set
                            of albums, just singles, etc. You can filter down your favorite artists' songs to just the ones you want to rank.
                        </p>
                        <p>
                            After you've finished a ranking, feel free to share your finalized list with friends, download the list & more.
                        </p>
                        <hr />
                        <p>
                            <small class="text-muted"><i>For best experience, use this website on your computer.</i></small>
                        </p>
                    </div>
                </div> -->
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
</style>