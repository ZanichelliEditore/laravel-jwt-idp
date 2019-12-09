[![Build Status](https://travis-ci.org/ZanichelliEditore/laravel-jwt-idp.svg?branch=master)](https://travis-ci.org/ZanichelliEditore/laravel-jwt-idp)
[![codecov](https://codecov.io/gh/ZanichelliEditore/laravel-jwt-idp/branch/master/graph/badge.svg)](https://codecov.io/gh/ZanichelliEditore/laravel-jwt-idp)

# Laravel JWT IDP

## What is it?

This project is a basic identity provider developed using Laravel Framework. The user
authentication is based on JWT standard. It is possible use a single sign-on point to log-in
users of several application.

## How can I use it?

It is possible to integrate the single sign-on in an existing project in few steps. Protect your route
with a middleware that checks if the user exists in the session. If the user is not in the session
redirect the user to the IDP login. After the login success the IDP will redirect the user to the
application passing a token. The application must use that token to retrieve the user data.

### Setup project

- Clone the project
- Duplicate the file .env.example and change the name in .env
- In the root the project execute `composer install`
- In the project root execute the command `php artisan key:generate`
- Set a secret key (for jwt authentication) executing from command line `php artisan jwt:secret`
- Create passport keys (for api authentication) executing from command line `php artisan passport:install`

### Routes

GET Requests

- **/loginForm** shows the IDP login form. Parameter: "redirect".
- **/v1/user** retrieve the user data by token. Parameters: "token"
- **/v1/logout** logout

POST Requests

- **/v2/login** login the user into the application. Parameters: "username" and "password".

### Views

There is 1 default views: login form.
There is also an admin section (**/admin**) through which you can manage idp system; the view is available for "ADMIN_IDP" user-role.

### Database

The IDP manages users using 3 table: users, users_roles, roles.
In the users tables are stored basic users data like email, password,
name, surname, is_verified. Each user can have a role or many roles associated;
it can be usefull in a context with RBAC (Role-based access control).

### User structure

```json
{
  "user": {
    "id": 1,
    "email": "mario.rossi@example.com",
    "is_verified": 1,
    "name": "Mario",
    "surname": "Rossi",
    "created_at": "2018-09-14 12:30:20",
    "updated_at": null,
    "roles": [
      {
        "roleId": 1,
        "roleName": "USER"
      },
      {
        "roleId": 2,
        "roleName": "ADMIN"
      }
    ]
  }
}
```
