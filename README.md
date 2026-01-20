# Nodes API - Backend Technical Test

## Description

This REST API allows managing a **tree of nodes** with the following operations:

- Create nodes (root or child)  
- List root nodes  
- List children of a node (with optional depth)  
- Delete nodes (only leaf nodes)  
- Title translation using `Accept-Language` header  
- Convert `created_at` timestamps to a timezone using `Time-Zone` header  

All nodes are stored in a **database** and preloaded using a **Seeder**.

---

## Requirements

- PHP >= 8.2  
- Composer  
- Docker + Docker Compose (if using Sail)  
- Laravel 12

---

## Installation

1. Clone the repository:

```bash
git clone <git@github.com:Jopsam/node-tree-api.git>
cd <your-repo-folder>
```

2. Copy `.env.example` to `.env` and configure environment variables if needed:

```bash
cp .env.example .env
```
3. Install PHP dependencies:

```bash
composer install
```

4. Start Sail / Docker:

```bash
./vendor/bin/sail up -d
```

5. Run migrations and seeders:

```bash
./vendor/bin/sail artisan migrate --seed
```

## Automated Tests

### PHPUnit

**Run a single test file:**

```bash
./vendor/bin/sail test tests/Feature/NodeApiTest.php
```

**Run a single test method:**

```bash
./vendor/bin/sail test --filter=test_it_creates_a_root_node
```

## Postman Docs

<a href="https://documenter.getpostman.com/view/21246879/2sBXViiWKQ" target="_blank">https://documenter.getpostman.com/view/21246879/2sBXViiWKQ</a>


## Important Notes

- `children` is **always returned** as an array, even for leaf nodes.  
- `depth` limits the recursion of children.  
- `Accept-Language` supports ISO 639-1 (`es`, `en`).  
- `Time-Zone` supports any valid PHP timezone (`America/Bogota`, `UTC`, etc).  
- PHPUnit tests cover node creation, listing, translation, timezone conversion, and deletion.

