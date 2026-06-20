<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Marketplace Platform API</title>
    <style>
        :root { color-scheme: light dark; font-family: system-ui, sans-serif; line-height: 1.5; }
        body { max-width: 42rem; margin: 2rem auto; padding: 0 1rem; }
        h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
        p.lead { color: #666; margin-top: 0; }
        ul { padding-left: 1.25rem; }
        a { color: #2563eb; }
        code { font-size: 0.9em; }
        .badge { display: inline-block; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: #e5e7eb; color: #374151; }
    </style>
</head>
<body>
    <p class="badge">Portfolio sample · local development only</p>
    <h1>Laravel Marketplace Platform API</h1>
    <p class="lead">REST backend for catalog, auth, favorites, and seller workflows. Pair with the iOS marketplace client.</p>

    <h2>Quick links</h2>
    <ul>
        <li><a href="/api/documentation">Swagger UI</a> — interactive API docs</li>
        <li><a href="/up">Health check</a> — <code>/up</code></li>
        <li><a href="https://github.com/sameh-bakleh/laravel-marketplace-platform/blob/main/docs/MOBILE_CLIENT.md">Mobile client contract</a></li>
        <li><a href="https://github.com/sameh-bakleh/laravel-marketplace-platform">GitHub repository</a></li>
    </ul>

    <h2>Demo login (after <code>db:seed</code>)</h2>
    <p><code>demo@example.com</code> / <code>password</code> — buyer account with pre-seeded favorites for the iOS app.</p>

    <h2>Sample endpoints</h2>
    <ul>
        <li><code>POST /api/login</code> — JWT access token</li>
        <li><code>GET /api/products</code> — paginated catalog</li>
        <li><code>GET /api/favorites</code> — authenticated favorites</li>
    </ul>
</body>
</html>
