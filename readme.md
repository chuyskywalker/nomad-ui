# Nomad UI

A basic, linkable, fast Nomad UI.

Feature List:

- [x] Overview index: Nodes, Jobs, Allocations, and Evaluations
- [x] Node details
- [x] Job details
- [ ] Evaluation details
- [x] Allocation details
- [x] Allocation File System Explorer
- [x] Allocation File Streamer

## Screenshots

Homepage:
![Homepage](./about/home.png)

Node:
![Homepage](./about/node.png)

Job:
![Homepage](./about/job.png)

Allocation (and file streamer):
![Homepage](./about/allocation.png)

## Run Nomad UI

You can run the dockerhub hosted image with just the Nomad URL and an ecryption key:

```
docker run -ti --rm --name=nui \
 -p 8090:80 \
 -e 'NOMAD_BASEURL=http://nomad.service.consul:4646' \
 chuyskywalker/nomad-ui
```

That will run this in the foreground and make the UI available at `http://<your-ip>:8090`.

## Development

Pretty easy:

```
docker build -t nui .
docker run -ti --rm -v $(pwd)/html:/app composer/composer install
docker run -d --name=nui \
 -e 'NOMAD_BASEURL=http://nomad.service.consul:4646' \
 -v `pwd`/html:/var/www/html \
 nui
```

Magically easy, thanks PHP!
