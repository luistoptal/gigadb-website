# Vue client for project image location

## Tasks

- [ ] install node_modules in volume

## Usage

### Dev

Run `./up.sh` script from root. This should build and run the docker container with the vite dev server and hot reloading

Alternatively, run and build isolated from command line:

Useful docker commands:

```bash
# (re)build and run container (e.g. if Dockerfile changed)
NODE_VERSION=20.11.0 APPLICATION=../.. docker-compose -f ops/deployment/docker-compose.yml up --build vite-project-image-location-dev -d
# run container
NODE_VERSION=20.11.0 APPLICATION=../.. docker-compose -f ops/deployment/docker-compose.yml up vite-project-image-location-dev -d
# stop container
docker-compose down vite-project-image-location-dev
# remove dangling images after building
docker image prune -f
# remove dangling volumes
docker volume prune -f
# Full cleanup
docker system prune -a -f --volumes
```
