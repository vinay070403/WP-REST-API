# RT Celebs REST API Plugin

## Purpose

This plugin demonstrates how to create a **custom REST API route** in WordPress for a custom post type (`rt-celebs`) with pagination support.  

Using the WordPress REST API, you can build:
- Entirely new admin experiences.
- Brand new interactive front-end applications.
- Integrations that bring WordPress content into separate apps.

This plugin is a practical example of extending WordPress with custom REST API endpoints to serve specific needs.

---

## Key REST API Concepts

Before working with the WordPress REST API, it’s important to understand these five key concepts:

1. **Routes & Endpoints**  
   A **route** is a URI that maps to one or more HTTP methods (GET, POST, etc.).  
   Each HTTP method mapped to a route is called an **endpoint**.  
   *Example:* `/wp-json/rt/v1/celebs` is a route, with GET method endpoint to fetch celebs.

2. **Requests**  
   Requests are instances of the `WP_REST_Request` class, encapsulating all info about the incoming API call (parameters, headers, method, etc).

3. **Responses**  
   Responses are the data returned by the API endpoints, usually in JSON format. The `WP_REST_Response` class helps build and manage these responses including HTTP headers.

4. **Schema**  
   The schema defines the structure and data types of input/output for each endpoint. It ensures consistent data formatting.

5. **Controller Classes**  
   Controllers manage route registration, request handling, schema utilization, and generating proper API responses.  

For more details, see the [WordPress REST API Handbook](https://developer.wordpress.org/rest-api/).

---

## What This Plugin Does

- Registers a custom post type called `rt-celebs` (if not already registered).
- Adds a custom REST API route `/wp-json/rt/v1/celebs` that returns paginated `rt-celebs` posts.
- Supports query parameters:
  - `page` (default 1) — page number for pagination.
  - `per_page` (default 10) — items per page.
- Returns response data including total posts and total pages via headers.
- Demonstrates adding CORS headers and restricting allowed origins to prevent cross-origin request issues.

---

## How to Use

1. Install and activate this plugin in your WordPress environment (LocalWP recommended for local dev).
2. Ensure `rt-celebs` CPT exists or let the plugin register it.
3. Make a GET request to:  http://localhost:10022/wp-json/rt/v1/celebs?page=1&per_page=5
4. The API will return JSON with paginated celebs data.

---

## Common Problems & Solutions

### Problem: Cross-Origin Requests Not Allowed / CORS errors  
**Symptoms:**  
- Publishing or API requests fail with errors like  
`Publishing failed. Cross-origin requests not allowed`  
- Browsers block requests made from a different origin (domain, protocol, or port).

**Cause:**  
- Web security feature called **CORS (Cross-Origin Resource Sharing)** blocks requests from unauthorized origins.

**Solution:**  
1. use WP-CLI inside the site's site shell:
wp post create --post_type=rt-celebs --post_title="Famous Star" --post_status=publish --post_content="bio"

2 . - Add CORS headers in your plugin to allow your frontend or Postman origin:  
```php
header('Access-Control-Allow-Origin: https://localhost:10022');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');
```
Restrict origins server-side to only allow trusted URLs.

Problem: Unable to authenticate or post data
Symptoms:

API requests needing authentication fail or return permission errors.

Cause:

Authentication required but missing or invalid.

WordPress REST API uses cookie authentication or token-based methods.

Solution:

Use nonce tokens for authenticated requests from WordPress front-end.

For external clients, use Basic Auth, OAuth, or Application Passwords.

Ensure permission_callback in custom routes properly handles auth.

Why Use Custom REST Routes?
To expose custom data structures (like rt-celebs) via API.

To control exactly what data and endpoints are available.

To build decoupled front-end apps (React, Vue, etc.) consuming WP content.

To integrate WordPress content with other systems or mobile apps.

References & Further Reading
WordPress REST API Handbook

REST API Routes and Endpoints

Using the REST API

CORS Explained

How to Authenticate REST API Requests

License
GPL v2 or later

Created by Vinay
<img width="1116" height="1005" alt="image" src="https://github.com/user-attachments/assets/a1e375de-26ef-45a8-b3dc-543b987561af" />
<img width="1847" height="609" alt="image" src="https://github.com/user-attachments/assets/70d73bbd-702c-46f1-8383-773f7f39e366" />



---

If you want, I can also help you create a **simple guide for making authenticated POST requests** and how to test this with Postman or frontend apps.

Would you like me to do that?
