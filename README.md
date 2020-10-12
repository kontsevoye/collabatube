<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->
[![Build][build-shield]][build-url]
[![Apache-2.0 License][license-shield]][license-url]
<!--
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
-->

<!-- PROJECT LOGO -->
<br />
<p align="center">
  <a href="https://github.com/kontsevoye/collabatube">
    <img src="images/logo.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">Collabatube</h3>
  
  <h4 align="center">🚧Work in progress🚧</h3>

  <p align="center">
    Watch YouTube with your friends remotely
    <!--
    <br />
    <a href="https://github.com/kontsevoye/collabatube"><strong>Explore the docs »</strong></a>
    -->
    <br />
    <br />
    <a href="https://collabatube.ru">View Demo</a>
    ·
    <a href="https://github.com/kontsevoye/collabatube/issues">Report Bug</a>
    ·
    <a href="https://github.com/kontsevoye/collabatube/issues">Request Feature</a>
  </p>
</p>

<!-- TABLE OF CONTENTS -->
## Table of Contents

* [About the Project](#about-the-project)
  * [Built With](#built-with)
* [Getting Started](#getting-started)
  * [Prerequisites](#prerequisites)
  * [Running](#running)
  * [Configuration description](#configuration-description)
    * [GitHub OAuth](#github-oauth)
    * [Google OAuth](#google-oauth)
    * [YouTube Data API](#youtube-data-api)
* [Deployment](#deployment)
  * [Prerequisites](#prerequisites)
  * [Running](#running)
* [Roadmap](#roadmap)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)

<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://collabatube.ru)

Tired of watching YouTube videos alone?
Want to giggle over funny moments or cry over sad ones with your friends?
We come to the rescue!

Collabatube is a web application for watching videos with your friends.
You can create a playlist and discuss what you see in a cozy chat.
Play time will be synchronized for all clients.

Do you want no one else to come in?
You can create a private room and protect it with a password, only invited people will be able to enter.

### Built With

* [Swoole](https://github.com/swoole/swoole-src)
* [Hyperf](https://github.com/hyperf/hyperf)

<!-- GETTING STARTED -->
## Getting Started

To get a local copy up and running follow these simple steps.

### Prerequisites

Collabatube has some requirements for the system environment, it can only run under Linux and Mac environment,
but due to the development of Docker virtualization technology, Docker for Windows can also be used as the running
environment under Windows.

When you don't want to use Docker as the basis for your running environment, you need to make sure that your operating
environment meets the following requirements:  

 - MySQL
 - Redis
 - PHP >= 7.4
 - Swoole PHP extension >= 4.5, and Disabled `Short Name`
 - JSON PHP extension
 - PDO PHP extension
 - Redis PHP extension

### Running

1. Clone the repo
```sh
git clone https://github.com/kontsevoye/collabatube.git
```
2. Pull docker image dependencies
```sh
docker-compose pull
```
3. Build docker images
```sh
docker-compose build
```
4. Create the configuration `.env` file, based on `.env.dist`
```sh
cp .env.dist .env
```
5. Start all the containers in background
```sh
docker-compose up -d
```
6. This will start the cli http server on port `9501`, cli ws server on port `9502` and bind it to all network
interfaces. You can then visit the site at `http://localhost:9501/` which will bring up Collabatube default home page.

### Configuration description

- application
    - `APP_NAME` — application name for internal usage
    - `APP_ENV` — application environment
- docker
    - `DOCKER_HTTP_HOST_PORT` — HTTP server port on the host machine
    - `DOCKER_WS_HOST_PORT` — WS server port on the host machine
    - `DOCKER_MYSQL_HOST_PORT` — mysql server port on the host machine
    - `DOCKER_REDIS_HOST_PORT` — redis server port on the host machine
- socketio
    - `SOCKETIO_SERVER_URL` — URL to the socket.io server
- redis
    - `REDIS_HOST` — redis server host
    - `REDIS_AUTH` — redis server auth
    - `REDIS_PORT` — redis server port
    - `REDIS_DB` — redis server database
- db
    - `DB_DRIVER` — DB driver
    - `DB_HOST` — DB server host
    - `DB_PORT` — DB server port
    - `DB_DATABASE` — DB server database
    - `DB_USERNAME` — DB server username
    - `DB_PASSWORD` — DB server password
    - `DB_CHARSET` — DB server charset
    - `DB_COLLATION` — DB server collation
    - `DB_PREFIX` — DB table prefix
- auth
    - `JWT_SECRET` — JWT secret for signing tokens
    - `OAUTH_GITHUB_CLIENT_ID` — GitHub OAuth client ID, please refer to [this](#github-oauth)
    - `OAUTH_GITHUB_CLIENT_SECRET` — GitHub OAuth client secret, please refer to [this](#github-oauth)
    - `OAUTH_GOOGLE_CLIENT_ID` — Google OAuth client ID, please refer to [this](#google-oauth)
    - `OAUTH_GOOGLE_CLIENT_SECRET` — Google OAuth client secret, please refer to [this](#google-oauth)
- youtube
    - `YOUTUBE_DATA_API_KEY` — YouTube Data API key, please refer to [this](#youtube-data-api)

#### GitHub OAuth

Follow [this](https://docs.github.com/en/free-pro-team@latest/developers/apps/creating-an-oauth-app) manual to obtain
client id & secret.

#### Google OAuth

Follow [this](https://developers.google.com/identity/protocols/oauth2/web-server#creatingcred) manual to obtain
client id & secret.

#### YouTube Data API

Follow [this](https://developers.google.com/youtube/registering_an_application#create_project) manual to obtain
data API key.

<!-- DEPLOYMENT -->
## Deployment

To get up and running follow these simple steps.

### Prerequisites

You need to make sure that your environment meets the following requirements:  

 - Kubernetes cluster and its kubeconfig
 - kubectl
 - helm

### Running

The project comes with [Helm chart](https://github.com/kontsevoye/collabatube/tree/master/.helm) for deploying into
Kubernetes. So all you have to do is:

1. Change directory to .helm
```sh
cd .helm
```
2. Fetch helm dependencies
```sh
helm dependency update
```
3. Create your own `values-production.yaml` file, based on `values.yaml` and change the values according to your needs
```sh
cp values.yaml values-production.yaml
```
4. Deploy
```sh
helm upgrade -i --atomic -f values.yaml -f values-production.yaml collabatube .
```

<!-- ROADMAP -->
## Roadmap

See the [open issues](https://github.com/kontsevoye/collabatube/issues) for a list of proposed features (and known issues).

<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any
contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<!-- LICENSE -->
## License

Distributed under the Apache-2.0 License. See `LICENSE` for more information.

<!-- CONTACT -->
## Contact

Evgeny Kontsevoy - [@evkon](https://t.me/evkon) - instane@gmail.com

Project Link: [https://github.com/kontsevoye/collabatube](https://github.com/kontsevoye/collabatube)

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[build-shield]: https://github.com/kontsevoye/collabatube/workflows/Build/badge.svg
[build-url]: https://github.com/kontsevoye/collabatube/actions
[contributors-shield]: https://img.shields.io/github/contributors/kontsevoye/collabatube.svg
[contributors-url]: https://github.com/kontsevoye/collabatube/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/kontsevoye/collabatube.svg
[forks-url]: https://github.com/kontsevoye/collabatube/network/members
[stars-shield]: https://img.shields.io/github/stars/kontsevoye/collabatube.svg
[stars-url]: https://github.com/kontsevoye/collabatube/stargazers
[issues-shield]: https://img.shields.io/github/issues/kontsevoye/collabatube.svg
[issues-url]: https://github.com/kontsevoye/collabatube/issues
[license-shield]: https://img.shields.io/github/license/kontsevoye/collabatube.svg
[license-url]: https://github.com/kontsevoye/collabatube/blob/master/LICENSE
[product-screenshot]: images/screenshot.png
