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