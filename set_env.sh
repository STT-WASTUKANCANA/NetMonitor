#!/bin/bash

# Script untuk set environment variables dari .env file
# dan clear environment variables lama

echo "Clearing old environment variables..."
unset DB_HOST
unset DB_PORT
unset DB_NAME
unset DB_USER
unset DB_PASSWORD

echo "Loading .env file..."
if [ -f .env ]; then
    export $(grep -v '^#' .env | grep -v '^$' | xargs)
    echo "✓ Environment variables loaded"
    echo ""
    echo "Database Configuration:"
    echo "  DB_HOST=$DB_HOST"
    echo "  DB_PORT=$DB_PORT"
    echo "  DB_NAME=$DB_NAME"
    echo "  DB_USER=$DB_USER"
else
    echo "✗ .env file not found!"
    exit 1
fi
