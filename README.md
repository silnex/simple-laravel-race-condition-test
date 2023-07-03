# Laravel Race Condition Test

## How to install?

```bash 
cp .env.example .env
docker compose up -d
```

## How to Test?

```bash
curl http://localhost:8000/user/reset # Reset money

wrk http://localhost:8000/user/1/add-money/${method} # Run test
```

## Test Result

### Method 1
```php
// file: routes/web.php:25
$user->update(['money' => $user->money + 1]);
/* Result
| 328 requests in 10.02s, 401.20KB read
| Requests/sec:     32.73
| user money: 252 
| Have Race Condition issue!
*/
```

### Method 2

```php
// file: routes/web.php:30
$user = $user->lockForUpdate()->find(1);
$user->update(['money' => $user->money + 1]);
/* Result
| 305 requests in 10.02s, 373.10KB read
| Requests/sec:     30.45
| User money: 331 (requests+10 because wrk run 10s+0.02s)
| Prevent Race Condition but slow down
*/
```

### Method 3

```php
// file: routes/web.php:35
$user->update(['money' => DB::raw('money + 1')]); // same this `$user->increment('money', 1);`
/* Result
| 333 requests in 10.02s, 407.36KB read
| Requests/sec:     33.22
| User money: 343 (requests+10 because wrk run 10s+0.02s)
| Prevent Race Condition and Fast!
*/
```
