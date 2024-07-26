FROM python:3.6.10-alpine3.11

# copy crontabs for root user
COPY scripts/cronjobs /etc/crontabs/root
COPY scripts/ /home/

RUN apk add bash vim

RUN pip install requests

# start crond with log level 8 in foreground, output to stderr
CMD ["crond", "-f", "-d", "8"]