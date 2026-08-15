# Deploying this PHP portfolio to Vercel

Vercel has no native PHP runtime, but the community-maintained `vercel-php`
runtime (https://github.com/vercel-community/php) lets Vercel execute PHP
files as serverless functions. This project is already restructured for it.

## What changed from the plain XAMPP version

- `index.php` moved into `api/index.php` (Vercel functions must live under `api/`)
- `style.css`, `main.js`, `uploads/` stay at the project root as static files
- `vercel.json` routes `/style.css`, `/main.js`, and `/uploads/*` straight to
  the static files, and routes everything else (including `/`) to the PHP function
- Database credentials now come from environment variables (`DB_HOST`,
  `DB_NAME`, `DB_USER`, `DB_PASS`) instead of being hardcoded — you'll set
  these in the Vercel dashboard

## Step 1 — Get a free external MySQL database

Vercel functions have no persistent storage or database of their own, so you
need an externally hosted MySQL. Recommended free option (no credit card):

**Clever Cloud** — https://www.clever-cloud.com/
1. Sign up, create a new "MySQL Addon" on the free **DEV** plan
2. Once created, open the addon and note: **host**, **port**, **database
   name**, **username**, **password**
3. Use their web console (or download the credentials into a MySQL client
   like TablePlus/DBeaver/HeidiSQL) to run `schema.sql` against it — this
   creates `blogs` and `contact_messages` and seeds sample posts

(Alternative free options if you want to compare: `db4free.net` or
`freesqldatabase.com` — both work but are slower and less reliable than
Clever Cloud.)

## Step 2 — Push this project to GitHub

```bash
git init
git add .
git commit -m "PHP portfolio for Vercel"
git branch -M main
git remote add origin https://github.com/<your-username>/<repo-name>.git
git push -u origin main
```

## Step 3 — Import the project in Vercel

1. Go to https://vercel.com/new
2. Import your GitHub repo
3. Framework preset: choose **Other** (Vercel won't auto-detect PHP)
4. Before deploying, open **Environment Variables** and add:

   | Name      | Value                                    |
   |-----------|-------------------------------------------|
   | `DB_HOST` | the host Clever Cloud gave you             |
   | `DB_NAME` | the database name                          |
   | `DB_USER` | the username                                |
   | `DB_PASS` | the password                                |

5. Click **Deploy**

## Step 4 — Test it

Once deployed, Vercel gives you a `https://<project>.vercel.app` URL. Open
it — the homepage, blog CRUD, and contact form should all work against your
Clever Cloud database.

## Notes / limitations to expect

- **Cold starts**: the first request after inactivity can take a couple of
  seconds while the function spins up — normal for serverless.
- **No file uploads to disk**: `uploads/` is static and read-only in this
  setup. The blog form's "Image URL" field (a link, not a file upload) still
  works fine — just don't try to add real file-upload functionality without
  switching to external storage (e.g. Cloudinary, S3).
- **Community runtime**: `vercel-php` isn't officially maintained by Vercel.
  It's stable for small personal projects like this one, but isn't something
  I'd rely on for production-critical apps.
- **Local testing still works**: since the code falls back to
  `localhost` / `root` / empty password when the environment variables
  aren't set, you can keep testing locally with XAMPP exactly as before —
  just run it from `api/index.php` directly, or copy it back to the root as
  `index.php` for local use.
