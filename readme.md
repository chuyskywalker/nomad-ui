# Nomad UI

A _very_ basic Nomad UI. Shows info and status for nodes and jobs.

## Run Nomad UI

You really only need to pass in the nomad url for this to work:

```
docker run -ti --rm --name=nui \
 -p 8090:80 \
 -e 'NOMAD_BASEURL=http://nomad-address:4646' \
 chuyskywalker/nomad-ui
```

That will run this in the foreground and make the UI available at `http://<your-ip>:8090`.
