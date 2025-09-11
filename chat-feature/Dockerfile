FROM node:20-alpine

WORKDIR /usr/src/app

COPY package*.json ./
RUN npm ci || true

COPY . .

EXPOSE 4000
CMD ["npm", "start"]
