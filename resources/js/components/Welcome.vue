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
                            This application is still in version ALPHA and its UI is constantly under construction. To submit visual changes
                            or if you would like to view the source code: <a target="_blank" href="https://www.github.com/kylenotfound/spotify-song-ranker">click here</a>
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
                    <a class="btn btn-primary btn-lg border border-dark border-1 text-white login-btn" href="/login/spotify">
                        Login & Start
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        
    }
</script>
<style>
    .login-card {
      background: url('/public/cover.png') center/cover no-repeat; /* Replace 'your-image.jpg' with your actual image */
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
