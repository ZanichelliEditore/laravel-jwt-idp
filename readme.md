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

### Installation
- Clone the project
- Duplicate the file .env.example and change the name in .env
- In the root the project execute ``composer install``
- In the project root execute the command ``php artisan key:generate``
- Set a secret key (for jwt authentication) executing from command line ``php artisan jwt:secret``

### Routes
GET Requests

- **/loginForm** shows the IDP login form. Parameter: "redirect".
- **/registerForm** shows the IDP register form
- **/v1/loginWithToken** retrieve the user data by token. Parameters: "token"
- **/v1/logout** logout

POST Requests
- **/v1/login** login the user into the application. Parameters: "username" and "password".
- **/v1/register** register the user into the IDP. Parameters: "username", "email", "password", "password_confirmation", "name", "surname"

### Views
There are 2 default views: login form and register form.

### Database
The IDP manages users using 3 table: users, users_roles, roles.
In the users tables are stored basic users data like email, username, password,
name, surname, is_verified. Each user can have a role or many roles associated;
it can be usefull in a context with RBAC (Role-based access control).

### User structure

```json
{
    "user": {
        "id": 1,
        "username": "mario.rossi",
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

### Example Middleware in Laravel
The following code is a basic implementation of a middleware that checks if the user is in the session. It can be implemented in different ways, for example using guards and the Authenticable user of Laravel. If you don't know what guards is, read https://laravel.com/docs/5.7/authentication. This implementation uses GuzzleHttp library for http requests.
When the middleware is implemented you have to use it to protect your routes: https://laravel.com/docs/5.7/middleware#registering-middleware
```php
class IdpMiddleware {

    public function handle(Request $request, Closure $next){

        // Check if the request has the token field
        if($request->input('token')){
            $token = $request->input('token');

            $client = new Client();

            try {
                // Retrieves the user information by token
                $response = $client->get('http://example.idp.com/v1/loginWithToken?token=' . $token);
            } catch (\Exception $e){
                Log::error($e->getMessage());
                // Redirect to login form
                return redirect('http://example.idp.com/loginForm' . '?redirect=' . $request->url());
            }

            if($response->getStatusCode() == 200){

                $userJson = \GuzzleHttp\json_decode($response->getBody());
                
                $request->session()->put('user', $userJson);
            }
        }

        // Check if the user is logged in
        if(!$request->session()->has('user')){
            // Redirect to login form
            return redirect('http://example.idp.com/loginForm' . '?redirect=' . $request->url());
        }

        return $next($request);
    }
```
