FROM node:12-alpine
LABEL maintainer 'Valdeir Santana <https://valdeir.dev>'

ENV PORT=3000
RUN mkdir /app
WORKDIR /app
COPY . /app

RUN npm install --production

EXPOSE ${PORT}

CMD [ "node", "server.js" ]
