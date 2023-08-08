# Project Overview

This project is a Laravel 10 based web application that provides APIs for user registration, login, and management of movies and TV shows.

## Technical Stack

- **Framework:** [Laravel 10](https://laravel.com/docs/10.x)
- **ORM**: [`Eloquent`](https://laravel.com/docs/10.x/eloquent) (Laravel's default Object-Relational Mapping tool)
- **PHP Version:** 8.2.8
- **Server Setup**:
  - [Laravel Sail](https://laravel.com/docs/10.x/sail): A lightweight command-line interface for interacting with Laravel's default Docker environment.
  - **Docker**: The application is containerized using Docker.
  - **Operating System**: Ubuntu 22.04.2 LTS
- **Authentication:** JWT-based authentication using the [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth) package.

## Setup

1. Setup Laravel Sail and Docker on Ubuntu.
2. After pulling the project, run `composer install` to install necessary dependencies.
3. After setting up `tymon/jwt` and creating the API secret key, you need to configure the token expiration time. Add the following line to your `.env` file to set the token's expiration time in minutes: `JWT_TTL=60`
4. Run migrations and seeders as required.

## Endpoints

### User Registration

- **Endpoint:** `/api/register`
- **Method:** POST

**Body (JSON):**
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "securepassword123",
    "password_confirmation": "securepassword123"
}
```

### User Login

- **Endpoint:** `/api/login`
- **Method:** POST

**Body (JSON):**
```json
{
    "email": "john.doe@example.com",
    "password": "securepassword123"
}
```

### Refresh Token

- **Endpoint:** `/api/refresh`
- **Method:** POST

**Headers:**
`Authorization: Bearer <token>`

### Movies

- **Endpoint:** `/api/movies`
- **Method:** POST

**Headers:**
`Authorization: Bearer <token>`

**Body (JSON) for Creating:**
```json
{
    "name": "The-Matrix"  // Required
    // Optional fields below
    "director_name": "Lana Wachowski, Lilly Wachowski",
    "actors_name": ["Keanu Reeves", "Laurence Fishburne"]
}
```

- **Method:** GET

**Parameters:**
- `name=` (full name or part of the movie name, use '-' instead of spaces)
- `director_name=` (full name or part of the director name, use '-' instead of spaces)
- `sort_field=` (field to order by)
- `sort_order=` (asc or desc)

### TV Shows

- **Endpoint:** `/api/tv-shows`
- **Method:** POST

**Headers:**
`Authorization: Bearer <token>`

**Body (JSON) for Creating:**
```json
{
  "name": "Breaking Bad", // Required
  // Optional fields below
  "director_name": "Vince Gilligan",
  "seasons": [
    {
      "season_number": 1,
      "episodes": [
        {
          "episode_number": 1,
          "name": "Pilot"
        },
        {
          "episode_number": 2,
          "name": "Cat's in the Bag..."
        }
      ],
      "actors": ["Bryan Cranston", "Aaron Paul"]
    }
  ]
}
```

- **Endpoint:** `/api/tv-shows/{tvShow}/season/{season}/episode/{episode}`
- **Method:** GET (for retrieving a particular episode)

Replace `{tvShow}`, `{season}`, and `{episode}` with the appropriate values:

- `{tvShow}`: Full name of the show, using '-' in place of spaces (e.g. "breaking-bad" for "Breaking Bad").
- `{season}`: The season number you're interested in (e.g. "1" for the first season). This should be a numeric value.
- `{episode}`: The episode number within the chosen season (e.g. "2" for the second episode). This should also be a numeric value.

**Headers:**
`Authorization: Bearer <token>`

### Database Schema

#### Users Table

| Field              | Type          | Modifiers     |
|--------------------|---------------|---------------|
| id                 | BIGINT        | AUTO_INCREMENT, PRIMARY KEY |
| name               | VARCHAR(255)  |               |
| email              | VARCHAR(255)  | UNIQUE        |
| email_verified_at  | TIMESTAMP     | NULLABLE      |
| password           | VARCHAR(255)  |               |
| remember_token     | VARCHAR(100)  | NULLABLE      |
| created_at         | TIMESTAMP     | NULLABLE      |
| updated_at         | TIMESTAMP     | NULLABLE      |

#### Actors Table

| Field      | Type          | Modifiers                           |
|------------|---------------|-------------------------------------|
| id         | BIGINT        | AUTO_INCREMENT, PRIMARY KEY          |
| name       | VARCHAR(255)  |                                     |
| created_at | TIMESTAMP     | NULLABLE                            |
| updated_at | TIMESTAMP     | NULLABLE                            |

#### Directors Table

| Field      | Type          | Modifiers                           |
|------------|---------------|-------------------------------------|
| id         | BIGINT        | AUTO_INCREMENT, PRIMARY KEY          |
| name       | VARCHAR(255)  |                                     |
| created_at | TIMESTAMP     | NULLABLE                            |
| updated_at | TIMESTAMP     | NULLABLE                            |

#### TV Shows Table

| Field       | Type           | Modifiers                                |
|-------------|----------------|------------------------------------------|
| id          | BIGINT         | AUTO_INCREMENT, PRIMARY KEY               |
| name        | VARCHAR(255)   |                                          |
| director_id | BIGINT         | FOREIGN KEY, NULLABLE, REFERENCES directors(id), ON DELETE SET NULL |
| created_at  | TIMESTAMP      | NULLABLE                                 |
| updated_at  | TIMESTAMP      | NULLABLE                                 |

#### Movies Table

| Field       | Type           | Modifiers                                |
|-------------|----------------|------------------------------------------|
| id          | BIGINT         | AUTO_INCREMENT, PRIMARY KEY               |
| name        | VARCHAR(255)   |                                          |
| director_id | BIGINT         | FOREIGN KEY, NULLABLE, REFERENCES directors(id), ON DELETE SET NULL |
| created_at  | TIMESTAMP      | NULLABLE                                 |
| updated_at  | TIMESTAMP      | NULLABLE                                 |

#### Seasons Table

| Field         | Type          | Modifiers                                             |
|---------------|---------------|-------------------------------------------------------|
| id            | BIGINT        | AUTO_INCREMENT, PRIMARY KEY                            |
| season_number | INTEGER       |                                                       |
| tv_show_id    | BIGINT        | FOREIGN KEY, REFERENCES tv_shows(id), ON DELETE CASCADE|
| created_at    | TIMESTAMP     | NULLABLE                                              |
| updated_at    | TIMESTAMP     | NULLABLE                                              |

#### Actor-Movie Pivot Table

| Field    | Type           | Modifiers                                          |
|----------|----------------|----------------------------------------------------|
| id       | BIGINT         | AUTO_INCREMENT, PRIMARY KEY                         |
| actor_id | BIGINT         | FOREIGN KEY, REFERENCES actors(id), ON DELETE CASCADE|
| movie_id | BIGINT         | FOREIGN KEY, REFERENCES movies(id), ON DELETE CASCADE|

#### Actor-Show Pivot Table

| Field      | Type           | Modifiers                                             |
|------------|----------------|-------------------------------------------------------|
| id         | BIGINT         | AUTO_INCREMENT, PRIMARY KEY                            |
| actor_id   | BIGINT         | FOREIGN KEY, REFERENCES actors(id), ON DELETE CASCADE  |
| tv_show_id | BIGINT         | FOREIGN KEY, REFERENCES tv_shows(id), ON DELETE CASCADE|
| season_id  | BIGINT         | FOREIGN KEY, REFERENCES seasons(id), ON DELETE CASCADE |

#### Episodes Table

| Field           | Type          | Modifiers                                             |
|-----------------|---------------|-------------------------------------------------------|
| id              | BIGINT        | AUTO_INCREMENT, PRIMARY KEY                            |
| name            | VARCHAR(255)  |                                                       |
| episode_number  | VARCHAR(255)  |                                                       |
| season_id       | BIGINT        | FOREIGN KEY, REFERENCES seasons(id), ON DELETE CASCADE |
| created_at      | TIMESTAMP     | NULLABLE                                              |
| updated_at      | TIMESTAMP     | NULLABLE                                              |

---

**Note:** Ensure all interactions with the API are authenticated by including the Bearer token in the header for every request after login.

Happy coding!
