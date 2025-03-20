# Use the official Nginx image
FROM nginx:alpine

# Copy the custom Nginx configuration file to the correct location
COPY nginx.conf /etc/nginx/nginx.conf

# Copy the SSL certificates into the container (make sure you have your certificates ready)
COPY ./ssl_certificate/certificate.crt /etc/nginx/ssl/certificate.crt
COPY ./ssl_certificate/private.key /etc/nginx/ssl/private.key

# Copy the frontend files into the Nginx public directory
COPY ./ /usr/share/nginx/html

# Expose port 80
EXPOSE 443
