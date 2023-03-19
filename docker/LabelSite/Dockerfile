FROM mcr.microsoft.com/dotnet/sdk:7.0-alpine3.17-amd64
ENV PATH="${PATH}:/root/.dotnet/tools"

RUN apk add --no-cache mono --repository http://dl-cdn.alpinelinux.org/alpine/edge/testing && \
    apk add --no-cache --virtual=.build-dependencies ca-certificates && \
    cert-sync /etc/ssl/certs/ca-certificates.crt && \
    apk del .build-dependencies

LABEL maintainer="${maintainer}"
COPY run.sh /run.sh
RUN chmod +x /run.sh

ENTRYPOINT /run.sh
