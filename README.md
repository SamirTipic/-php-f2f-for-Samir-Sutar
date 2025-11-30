The following two APIs are required for development (no UI implementation needed; please demonstrate functionality using Postman):

1. Implement JWT authentication for user access.
2. Create an API endpoint to add a product to the cart.

Develop at: 
```
https://github.com/Phantasm-Solutions-Ltd-Pvt/php-f2f-for-candidate

```
Please ensure these implementations are set up prior to joining, in order to streamline the face-to-face interview process.

Best regards,  
Phantasm

## JWT Cart Feature Setup

1. Install dependencies:
   `composer require php-open-source-saver/jwt-auth`

2. Publish config:
   `php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"`

3. Generate secret:
   `php artisan jwt:secret`

4. Migrate:
   `php artisan migrate`

5. Serve:
   `php artisan serve`

### Endpoints
- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/logout
- GET /api/products (Requires Bearer Token)
- POST /api/products (Requires Bearer Token)
- GET /api/cart (Requires Bearer Token)
- POST /api/cart/add (Requires Bearer Token)

### Troubleshooting

**If you get "Invalid refresh token" error:**
1. Make sure you've run `php artisan jwt:secret`
2. Check that `JWT_SECRET` exists in your `.env` file
3. The token used for refresh must be valid (not expired)
4. Try adding a grace period in `.env`: `JWT_BLACKLIST_GRACE_PERIOD=60`

**JWT Token Configuration:**
- Token expires in **1 minute** (60 seconds) - configured via `JWT_TTL=1` in `.env`
- Refresh grace period is **60 seconds** - configured via `JWT_BLACKLIST_GRACE_PERIOD=60`
- After token expires, use the refresh endpoint to get a new token