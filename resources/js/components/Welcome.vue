<template>
    <section class="mb-4">
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="row">
                    <div class="col d-flex justify-content-center">
                        <h1 class="display-3 mt-4">Spotify Discography Ranker</h1>
                    </div>
                </div>
                <hr />
                <div class="welcome-card card mb-4">
                    <div class="card-body">
                        <p>
                            Welcome to Kyle's Spotify Discography Ranker. This application will allow you to log in with your
                            spotify account and rank your favorite artists' entire discography. This is not a bracket style ranking system.
                            We use an elo-based ranking system to rank every possible combination of tracks.
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
                </div>
            </div>
        </div>
        <div class="card login-card border border-3 border-dark">
            <div class="card-body login-card-body">
                <div class="d-flex align-items-center justify-content-center" style="height: 350px;">
                    <a class="btn btn-lg border border-dark border-1 text-white login-btn gradient-background" :href="buttonhref">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-spotify" viewBox="0 0 16 16">
                            <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.669 11.538a.498.498 0 0 1-.686.165c-1.879-1.147-4.243-1.407-7.028-.77a.499.499 0 0 1-.222-.973c3.048-.696 5.662-.397 7.77.892a.5.5 0 0 1 .166.686zm.979-2.178a.624.624 0 0 1-.858.205c-2.15-1.321-5.428-1.704-7.972-.932a.625.625 0 0 1-.362-1.194c2.905-.881 6.517-.454 8.986 1.063a.624.624 0 0 1 .206.858m.084-2.268C10.154 5.56 5.9 5.419 3.438 6.166a.748.748 0 1 1-.434-1.432c2.825-.857 7.523-.692 10.492 1.07a.747.747 0 1 1-.764 1.288z"/>
                        </svg>&nbsp;
                        <span v-if="this.authid">View Dashboard</span>
                        <span v-else>Login & Start</span>
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
        padding: 30px;
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
