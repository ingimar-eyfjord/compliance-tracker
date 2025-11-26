FROM nginx:1.25-alpine

WORKDIR /var/www/html

COPY --chmod=644 default.conf /etc/nginx/conf.d/default.conf

# Non-root user not needed; keep default nginx user
