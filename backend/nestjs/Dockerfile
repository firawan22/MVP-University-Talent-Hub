# ---- Builder Stage ----
FROM node:22-alpine AS builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production && cp -R node_modules /app/node_modules_prod && \
    npm ci

COPY . .

RUN npm run build

# ---- Runner Stage ----
FROM node:22-alpine AS runner

WORKDIR /app

# Copy production dependencies and built output
COPY --from=builder /app/node_modules_prod ./node_modules
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/package*.json ./

# Create uploads directory for file uploads
RUN mkdir -p /app/uploads

EXPOSE 3001

CMD ["node", "dist/main"]
