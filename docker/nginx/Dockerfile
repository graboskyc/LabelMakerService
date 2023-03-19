FROM nginx:latest

RUN mkdir -p /scripts
WORKDIR /scripts
COPY run.sh run.sh
RUN sed 's/\r$//' run.sh > run_lf.sh
RUN chmod +x ./run_lf.sh
ENTRYPOINT ./run_lf.sh
