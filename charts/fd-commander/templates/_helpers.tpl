{{/*
Expand the name of the chart.
*/}}
{{- define "fd-commander.name" -}}
{{- default .Chart.Name .Values.nameOverride | trunc 63 | trimSuffix "-" }}
{{- end }}

{{/*
Fully qualified app name.
*/}}
{{- define "fd-commander.fullname" -}}
{{- if .Values.fullnameOverride }}
{{- .Values.fullnameOverride | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- $name := default .Chart.Name .Values.nameOverride }}
{{- if contains $name .Release.Name }}
{{- .Release.Name | trunc 63 | trimSuffix "-" }}
{{- else }}
{{- printf "%s-%s" .Release.Name $name | trunc 63 | trimSuffix "-" }}
{{- end }}
{{- end }}
{{- end }}

{{- define "fd-commander.chart" -}}
{{- printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end }}

{{- define "fd-commander.labels" -}}
helm.sh/chart: {{ include "fd-commander.chart" . }}
{{ include "fd-commander.selectorLabels" . }}
{{- if .Chart.AppVersion }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
{{- end }}
app.kubernetes.io/managed-by: {{ .Release.Service }}
{{- end }}

{{- define "fd-commander.selectorLabels" -}}
app.kubernetes.io/name: {{ include "fd-commander.name" . }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end }}

{{- define "fd-commander.serviceAccountName" -}}
{{- if .Values.serviceAccount.create }}
{{- default (include "fd-commander.fullname" .) .Values.serviceAccount.name }}
{{- else }}
{{- default "default" .Values.serviceAccount.name }}
{{- end }}
{{- end }}

{{/*
Name of the secret holding sensitive values.
*/}}
{{- define "fd-commander.secretName" -}}
{{- if .Values.existingSecret }}
{{- .Values.existingSecret }}
{{- else }}
{{- include "fd-commander.fullname" . }}
{{- end }}
{{- end }}

{{/*
Database host: the bundled subchart's primary service, or the external host.
*/}}
{{- define "fd-commander.databaseHost" -}}
{{- if .Values.mysql.enabled }}
{{- printf "%s-mysql" .Release.Name }}
{{- else }}
{{- required "externalDatabase.host is required when mysql.enabled is false" .Values.externalDatabase.host }}
{{- end }}
{{- end }}

{{- define "fd-commander.databasePort" -}}
{{- if .Values.mysql.enabled }}3306{{- else }}{{ .Values.externalDatabase.port }}{{- end }}
{{- end }}

{{- define "fd-commander.databaseName" -}}
{{- if .Values.mysql.enabled }}{{ .Values.mysql.auth.database }}{{- else }}{{ .Values.externalDatabase.database }}{{- end }}
{{- end }}

{{- define "fd-commander.databaseUser" -}}
{{- if .Values.mysql.enabled }}{{ .Values.mysql.auth.username }}{{- else }}{{ .Values.externalDatabase.username }}{{- end }}
{{- end }}

{{/*
Redis host: the bundled subchart's master service, or the external host.
*/}}
{{- define "fd-commander.redisHost" -}}
{{- if .Values.redis.enabled }}
{{- printf "%s-redis-master" .Release.Name }}
{{- else }}
{{- .Values.externalRedis.host }}
{{- end }}
{{- end }}

{{- define "fd-commander.redisPort" -}}
{{- if .Values.redis.enabled }}6379{{- else }}{{ .Values.externalRedis.port }}{{- end }}
{{- end }}

{{/*
True when Redis is available for cache/session/queue drivers. Without it the
app falls back to the database, which works but is slower.
*/}}
{{- define "fd-commander.redisAvailable" -}}
{{- if or .Values.redis.enabled .Values.externalRedis.host }}true{{- end }}
{{- end }}

{{/*
Hostname browsers use to reach Reverb, derived from app.url when not set.
*/}}
{{- define "fd-commander.reverbHost" -}}
{{- if .Values.reverb.host }}
{{- .Values.reverb.host }}
{{- else if .Values.ingress.enabled }}
{{- .Values.ingress.host }}
{{- else }}
{{- .Values.app.url | trimPrefix "https://" | trimPrefix "http://" | splitList ":" | first }}
{{- end }}
{{- end }}

{{/*
Public scheme/port browsers use for the WebSocket connection.
*/}}
{{- define "fd-commander.reverbScheme" -}}
{{- if .Values.reverb.scheme }}
{{- .Values.reverb.scheme }}
{{- else if or .Values.ingress.tls.enabled (hasPrefix "https://" .Values.app.url) }}
{{- "https" }}
{{- else }}
{{- "http" }}
{{- end }}
{{- end }}

{{- define "fd-commander.reverbPublicPort" -}}
{{- if .Values.reverb.publicPort }}
{{- .Values.reverb.publicPort }}
{{- else if eq (include "fd-commander.reverbScheme" .) "https" }}443{{- else }}80{{- end }}
{{- end }}

{{/*
Image reference.
*/}}
{{- define "fd-commander.image" -}}
{{- printf "%s:%s" .Values.image.repository (default .Chart.AppVersion .Values.image.tag) }}
{{- end }}

{{/*
Secret and key holding the database password. With the bundled MySQL subchart
the subchart owns the credential, so read it from the subchart's own secret
rather than duplicating (and re-randomising) it here.
*/}}
{{- define "fd-commander.databaseSecretName" -}}
{{- if .Values.existingSecret }}
{{- .Values.existingSecret }}
{{- else if .Values.mysql.enabled }}
{{- printf "%s-mysql" .Release.Name }}
{{- else }}
{{- include "fd-commander.fullname" . }}
{{- end }}
{{- end }}

{{- define "fd-commander.databaseSecretKey" -}}
{{- if .Values.existingSecret }}
{{- .Values.externalDatabase.existingSecretPasswordKey }}
{{- else if .Values.mysql.enabled }}
{{- "mysql-password" }}
{{- else }}
{{- "DB_PASSWORD" }}
{{- end }}
{{- end }}

{{/*
Secret and key holding the Redis password, following the same rule.
*/}}
{{- define "fd-commander.redisSecretName" -}}
{{- if .Values.existingSecret }}
{{- .Values.existingSecret }}
{{- else if .Values.redis.enabled }}
{{- printf "%s-redis" .Release.Name }}
{{- else }}
{{- include "fd-commander.fullname" . }}
{{- end }}
{{- end }}

{{- define "fd-commander.redisSecretKey" -}}
{{- if and (not .Values.existingSecret) .Values.redis.enabled }}
{{- "redis-password" }}
{{- else }}
{{- "REDIS_PASSWORD" }}
{{- end }}
{{- end }}

{{/*
Whether a Redis password is in play at all.
*/}}
{{- define "fd-commander.redisAuthEnabled" -}}
{{- if .Values.redis.enabled }}
{{- if .Values.redis.auth.enabled }}true{{- end }}
{{- else if and .Values.externalRedis.host .Values.externalRedis.password }}
{{- "true" }}
{{- end }}
{{- end }}
