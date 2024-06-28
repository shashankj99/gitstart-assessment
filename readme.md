# PHP Symfony RESTful API Assessment

This repository contains the solution for the PHP Symfony assessment. It includes a RESTful API built using Symfony, Doctrine ORM, JWT Authentication, Docker configuration and static code analysis integration.

## Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Setup Instructions](#setup-instructions)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Static Code Analysis](#static-code-analysis)

## Overview

This project implements a RESTful API using Symfony, secured with JWT authentication, and integrates Docker for containerization. Static code analysis tools PHPStan and PHP_CodeSniffer are used to ensure code quality.

## Requirements

- Php 8.2+
- Composer
- Docker Compose

## Setup Instructions

1. **Clone the repository:**

   ```bash
   $ git clone https://github.com/shashankj99/gitstart-assessment.git
   $ cd gitstart-assessment
   ```

2. **Setup Environment Variable:**

    - Create a new .env file at the project root directory.
    - Copy the contents of .env.example and setup database credentials

    > Please use root user for DATABASE_URL as the migration file has create db command.
    >
    > Because of the above command user other than root won't have enough permission to create database.

3. **Install Composer:**

   ```bash
   $ composer install
   ```

4. **Spin up the container:**

   ```bash
   $ docker compose up -d --build
   ```
   > You need not go inside the container to migrate the tables as migration script runs automatically after images are built.

## API Endpoints

1. **Authentication:**

   You'll need to register a new user first. You can do it by running the following command in your terminal.

   ```bash
      curl --request POST \
      --url http://localhost:8080/api/register \
      --header 'Content-Type: application/json' \
      --data '{
         "email": "test@example.com",
         "password": "test@123"
      }'
   ```

   > The port is set by default for this assessment. Make sure the port 8080 is empty.

   Once the user is registered you'll need to log in to generate a JWT.

   You can achive it by running the login_check API.

   ```bash
      curl --request POST \
      --url http://localhost:8080/api/login_check \
      --header 'Content-Type: application/json' \
      --data '{
         "username": "test@example.com",
         "password": "test@123"
      }'
   ```

   You'll recieve an access token in the response. Use the token for the Product APIs.

2. **Product APIs:**
   
   Below are the API Endpoints for the CRUD operation of product.

   - Create Product

      ```bash
         curl --request POST \
         --url http://localhost:8080/api/product \
         --header 'Accept: application/json' \
         --header 'Authorization: Bearer <generated_access_token>' \
         --header 'Content-Type: application/json' \
         --data '{
            "name": "test product",
            "price": 50,
            "quantity": 100,
            "description": "test description"
         }'
      ```
      > The description is optional field. So, if you don't want it. You can remove it from the request payload.

   - Fetch All Products

      ```bash
         curl --request GET \
         --url 'http://localhost:8080/api/product?page=1&limit=10&order=desc&search=test' \
         --header 'Authorization: Bearer generated_access_token'
      ```
      > Here you can play around with the query parameters. The search query is optional.

   - Find Specific Product

      ```bash
         curl --request GET \
         --url http://localhost:8080/api/product/1 \
         --header 'Accept: application/json' \
         --header 'Authorization: Bearer generated_access_token'
      ```

   - Update Specific Product

      ```bash
         curl --request PUT \
         --url http://localhost:8080/api/product/1 \
         --header 'Accept: application/json' \
         --header 'Authorization: Bearer generated_access_token' \
         --header 'Content-Type: application/json' \
         --data '{
            "name": "new updated product",
            "price": 20,
            "quantity": 100,
            "description": "updated product description"
         }'
      ```

   - Delete Specific Product

      ```bash
         curl --request DELETE \
         --url http://localhost:8080/api/product/1 \
         --header 'Authorization: Bearer generated_access_token'
      ```
## Testing

To run test you can use `composer test-docker` command in your terminal.

> Make sure your docker containers are running for the test command to be executed successfully.

## Static Code Analysis

Here both `PHPStan` and `Php_CodeSniffer` is used for the static code analysis.

To check if the code standards are met you can run these commands in the terminal

```bash
   $ composer phpstan
   $ composer phpcs
```

> If encountered any issues, feel free to mail at <shashankj677@gmail.com> or open an issue in this repository.
