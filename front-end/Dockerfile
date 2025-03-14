# Use the official Nginx image
FROM nginx:alpine

# Copy the custom Nginx configuration file to the correct location
COPY nginx.conf /etc/nginx/nginx.conf

# Copy the frontend files into the Nginx public directory
COPY ./ /usr/share/nginx/html

# Expose port 80
EXPOSE 80
