# Field Day Commander Helm Chart

Deploys [Field Day Commander](https://github.com/ckoval7/fielddaycommander), an
amateur radio Field Day logging and scoring application, into Kubernetes.

The chart also ships a `questions.yaml`, so it appears as a form-driven
application in the Rancher Apps catalog.

## Installing

```bash
helm dependency build charts/fd-commander
helm install fd-commander charts/fd-commander \
  --namespace fd-commander --create-namespace \
  --set app.url=https://fd.example.com \
  --set ingress.enabled=true \
  --set ingress.host=fd.example.com
```

The first boot waits for the database, then runs migrations and the production
seeders before Octane starts serving, so allow a few minutes before the app
answers. The startup probe is sized for this.

Sign in as callsign `SYSTEM` with password `ChangeMe123!` and change it through
the setup wizard immediately.

## What gets deployed

A single application pod runs FrankenPHP/Octane under supervisord, alongside the
Reverb WebSocket server, a queue worker and the scheduler. MySQL and Redis are
pulled in as optional subcharts.

## Configuration

| Key | Description | Default |
| --- | --- | --- |
| `app.url` | Public URL, no trailing slash. Asset and storage links derive from it. | `http://localhost` |
| `app.key` | Laravel encryption key. Generated on first install when blank. | `""` |
| `image.repository` | Container image. | `ghcr.io/ckoval7/fielddaycommander` |
| `image.tag` | Image tag. Defaults to the chart's `appVersion`. | `""` |
| `ingress.enabled` | Expose through an ingress controller. | `false` |
| `service.type` | Service type for the web interface. | `ClusterIP` |
| `persistence.size` | Volume for uploads, gallery images and logs. | `10Gi` |
| `mysql.enabled` | Deploy the bundled MySQL subchart. | `true` |
| `redis.enabled` | Deploy the bundled Redis subchart. | `true` |
| `reverb.enabled` | Run the Reverb WebSocket server for live updates. | `true` |
| `externalLoggers.enabled` | Expose the UDP ports for radio logging software. | `true` |

### Persistence

`storage/` holds uploads, gallery images, logs and the marker file recording
that the seeders have run. Its claim is annotated `helm.sh/resource-policy: keep`
so uninstalling the release does not delete logged contacts' attachments. Delete
the claim by hand when you genuinely want the data gone.

### Credentials

With the bundled subcharts, MySQL and Redis own their own generated passwords
and the application reads them straight from the subchart secrets, so nothing
can drift between the two on upgrade. The `APP_KEY` and Reverb credentials are
generated once and preserved across upgrades — changing `APP_KEY` later makes
existing encrypted data and sessions unreadable.

To supply everything yourself, put `APP_KEY`, `DB_PASSWORD`, `REDIS_PASSWORD`,
`MAIL_PASSWORD`, `REVERB_APP_KEY`, `REVERB_APP_SECRET` and `REVERB_APP_ID` into
one secret and set `existingSecret`.

### External loggers

N1MM Logger+, WSJT-X and fldigi send contacts to the application over UDP. Those
listeners run inside the application pod, so the ports are exposed through a
separate `-udp` service. A `ClusterIP` will not work here — the logging software
sits on the LAN, outside the cluster:

- **LoadBalancer** keeps the standard port numbers (12060, 2237, 2238), which is
  what the logging software expects. Prefer this.
- **NodePort** remaps them into the node port range, so the logging software has
  to be pointed at the remapped port instead.

The port numbers configured here must match those set in the admin UI under
*Settings > External Loggers*.

### Real-time updates

Reverb backs the live scoreboard and dashboard. Browsers connect to `/app`, which
the ingress routes to the Reverb port ahead of the catch-all rule. When running
behind TLS, set `app.url` to the `https://` URL so the WebSocket connection is
negotiated as `wss://`.

### Scaling

`replicaCount` above 1 is not supported as shipped. The UDP listeners bind ports
inside a single pod and track their PIDs in the database, the storage volume is
ReadWriteOnce, and the deployment uses the `Recreate` strategy because the
entrypoint runs migrations on start.
