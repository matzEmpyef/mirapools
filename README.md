# Mira Pools website (mirapools.in)

A multi-page marketing site with a small admin panel. It runs on plain PHP 8.1 or newer, with no database: everything is saved as JSON files in `site/data/`. It works on ordinary cPanel or Hostinger hosting.

## Pages
- `/` Home
- `/about`
- `/services`, plus 10 service pages at `/services/<name>`
- `/gallery`, with category filters and a full-screen photo viewer
- `/contact`, with an enquiry form; enquiries are saved to the admin panel and e-mailed if `mail()` works
- `/sitemap.xml`, `/robots.txt`, and a custom 404 page

## Going live (regular PHP hosting)

**Hosting to buy:** any Linux shared hosting with cPanel or hPanel (for example Hostinger, GoDaddy, BigRock), with:
- PHP **8.1 or newer**, with the GD extension (standard)
- Apache with `.htaccess` support (standard on cPanel; not plain Nginx)
- a free SSL certificate (Let's Encrypt, usually one click)
- e-mail hosting, if info@mirapools.in should receive the enquiry e-mails

No database is needed.

**Steps**
1. Build the upload package: `python tools/package.py`. This creates `dist/mirapools-site-<date>.zip`, and `dist/SETUP_KEY.txt` with a fresh private admin setup key. The zip leaves out local admin data and test uploads.
2. Point the domain mirapools.in at the hosting (nameservers or an A record, from the domain registrar).
3. In the hosting's File Manager, open `public_html/`, upload the zip and extract it there. The zip's top level (`index.php`, `.htaccess` and so on) must sit directly in `public_html/`.
4. Make sure PHP can write to `data/` and `uploads/` (folder permission 755, or 775 if uploads fail).
5. Turn on SSL for mirapools.in, and set the PHP version to 8.1+ in the hosting panel. The site forces https:// and drops "www." by itself.
6. Open https://mirapools.in/admin/, enter the key from `SETUP_KEY.txt` and choose the admin password. This only happens once; then delete `SETUP_KEY.txt`.
7. Send a test enquiry from /contact and check that it reaches the admin panel and info@mirapools.in. If the e-mail doesn't arrive, the host may block PHP `mail()`. Enquiries still show in the admin panel.
8. Add the site to Google Search Console, submit `https://mirapools.in/sitemap.xml`, and create a Google Business Profile for the head office.

**Client preview (mirapools.empyef.com):** run `python tools/package.py --demo https://mirapools.empyef.com`. This builds `dist/mirapools-demo-<date>.zip` with its own key in `dist/SETUP_KEY-demo.txt`. The preview is marked "noindex" and its robots.txt blocks search engines, so it won't compete with mirapools.in on Google. Upload it to the subdomain's document root the same way as above. When the real site goes live, delete the preview or redirect it to mirapools.in.

**Updating later:** run `python tools/package.py` again and upload the files that changed. Never overwrite `data/` or `uploads/` on the server: they hold the live gallery, password and enquiries. Take a backup of those two folders now and then.

## Admin panel (`/admin/`)
- **Gallery**: upload several photos at once, pick a category, mark photos for the home page, edit titles, drag to reorder, and delete.
- **Site images**: replace any photo used on the pages, such as the hero, service images or the call-to-action band. "Reset" brings back the default.
- **Enquiries**: messages from the contact form.
- **Settings**: phone numbers, WhatsApp, e-mail, address, branches and social links (updated across the whole site), plus a password change.

## Temporary content to replace
- **Logo**: `assets/img/logo.png`, taken from the old website (small, 228×88). Replace it with a higher-resolution file when available; the old artwork misspells the tagline as "Watermangementsystem".
- **E-mail**: the site uses info@mirapools.in. Make sure that mailbox exists, or change it under Settings.
- **Photos** in `assets/img/temp/`:
  - `site-project-*`: from the old website
  - `project-*`, `feature-*` and others: cropped from the brochure (low resolution)
  - some headers: free Unsplash stock (loaded from images.unsplash.com)

  Replace all of them from the admin panel once the originals arrive.
- **Office hours** are blank, and hidden until they are added in Settings.

## Local preview (developer)
Run `docker build -t mira-pools-dev dev/`, then start the "mira-pools" preview, or run `site/` on port 8090 with `-e MIRA_SITE_URL=http://localhost:8090`.
