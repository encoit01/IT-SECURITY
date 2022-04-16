FROM mcr.microsoft.com/mssql/server:2017-latest

ENV SA_PASSWORD=GQOJfth3nePtDyZg
ENV ACCEPT_EULA=Y

RUN locale-gen en_US.UTF-8 && update-locale
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

ADD ./entrypoint.sh /
ADD ./init.sh /
ADD ./sqlScript.sql /


RUN  chmod +x /entrypoint.sh
RUN  chmod +x /init.sh

EXPOSE 1433

ENTRYPOINT /bin/bash /entrypoint.sh