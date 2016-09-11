# Nomad UI

A basic, linkable, fast Nomad UI.

Feature List:

- [x] Overview index: Nodes, Jobs, Allocations, and Evaluations
- [x] Node details
- [ ] Job details
- [ ] Evaluation details
- [ ] Allocation details
- [ ] Allocation File System Explorer
- [x] Allocation File System Streamer

## Run Nomad UI

You really only need to pass in the nomad url for this to work:

```
docker run -ti --rm --name=nui \
 -p 8090:80 \
 -e 'NOMAD_BASEURL=http://nomad-address:4646' \
 chuyskywalker/nomad-ui
```

That will run this in the foreground and make the UI available at `http://<your-ip>:8090`.

## Development

Pretty easy:

```
docker build -t nui .
docker run -ti --rm --name=nui \
 -e 'NOMAD_BASEURL=http://nomad-address:4646' \
 -v `pwd`/html:/var/www/html \
 nui
```

Magically easy, thanks PHP!
