# Laravel JWT IDP
## What is it?
This project is a basic identity provider developed using Laravel Framework. The user 
authentication is based on JWT standard. It is possible use a single sign-on point to log-in
users of several application.

## How can I use it?
It is possible to integrate the single sign-on in an existing in few steps. Protect your route
with a middleware that checks if the user exists in the session. If the user is not in the session
redirect the user to the IDP login. After the login success the IDP will redirect the user to the
application passing a token. The application must use that token to retrieve the user data.

### Routes
GET Requests

- **/loginForm** shows the IDP login form
- **/registerForm** shows the IDP register form
- **/v1/loginWithToken** retrieve the user data by token. Parameters: "token"
- **/v1/logout** logout

POST Requests
- **/v1/login** login the user into the application. Parameters: "username" and "password".
- **/v1/register** register the user into the IDP. Parameters: "username", "email", "password", "password_confirmation", "name", "surname"

### Views
There are 2 default views: login form and register form. 
