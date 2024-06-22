# Contributing Guide

Thank you for considering contributing to our song-rank.com! We welcome all kinds of contributions, including bug fixes, feature requests, and documentation improvements. Please follow the guidelines below to get started.

## Table of Contents
- [How to Contribute](#how-to-contribute)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Cloning the Repository](#cloning-the-repository)
  - [Setting Up the Application](#setting-up-the-application)
- [Coding Standards](#coding-standards)
- [Submitting Pull Requests](#submitting-pull-requests)
- [Contact](#contact)


## How to Contribute
1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature-name`).
3. Make your changes.
4. Commit your changes (`git commit -m 'Add some feature'`).
5. Push to the branch (`git push origin feature/your-feature-name`).
6. Open a pull request.

## Getting Started

### Prerequisites
- PHP 8.2 or later
- Composer
- Vue.js & Node.js & NPM
- MySQL

### Cloning the Repository
1. Fork the repository on GitHub.
2. Clone your forked repository to your local machine:
    ```sh
    https://github.com/kylenotfound/spotify-song-ranker.git
    ```
3. Navigate to the project directory:
    ```sh
    cd your-repository
    ```
    - If you are using Laravel Herd, clone the App in your Laravel Herd specified directory. 
    - If you are using Laravel Valet, be sure to "secure" the application.

### Setting Up the Application
1. Install PHP dependencies using Composer:
    ```sh
    composer install --ignore-platform-reqs
    ```
2. Install JavaScript dependencies using npm:
    ```sh
    npm i && npm run watch
    ```
3. Copy the `.env.example` file to `.env`:
    ```sh
    cp .env.example .env
    ```
4. You will need Spotify Client & Secret Keys for logging in. You can create a Spotify developer account here: 
    ```
    https://developer.spotify.com/
    ```
    Once you have made an account, do the following:
    - Create a new "application" - call it song-rank-fork.
    - Go to settings and set your callback URL, this can be something like:
        - http://localhost:8000/login/spotify/callback
        - http://127.0.0.1:8000/login/spotify/callback
        - https://spotify-song-ranker.test/login/spotify/callback (If you are using Laravel Herd / Valet)

4. Generate an application key:
    ```sh
    php artisan key:generate
    ```
5. Set up the database:
    - Update the `.env` file with your database credentials.
    - Run the database migrations:
      ```sh
      php artisan migrate
      ```
7. Start the local development server:
    ```sh
    php artisan serve 
    ```
    Your Laravel application should now be up and running at `http://localhost:8000`.
    Or if you are using Laravel Herd / Valet it should be available at 
    ``` https://repo_name.test ```


## Submitting Pull Requests
- Ensure your code isn't ugly, is working, you removed all unnecessary comments & debug logs, etc.
- Provide a clear description of your changes.
    - Why
    - What Changed
    - Discussions / Considerations
    - Testing Done
- Reference any related issues in your pull request.
- Be prepared to update your pull request based on feedback from code reviewers.

## Contact
If you have any questions, feel free to open an issue or reach out to me.

Thank you for contributing!
