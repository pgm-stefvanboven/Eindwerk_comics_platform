#!/bin/bash

# Install dependencies
npm install

# Build and version the assets for production
npm run build

# Ensure the build files are in the correct location
if [ ! -f ./public/build/manifest.json ]; then
  echo "Error: manifest.json not found!"
  exit 1
fi

echo "Deployment successful!"