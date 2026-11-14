"""Build a clean upload zip of the website for PHP hosting.

- Leaves out local admin data (password, enquiries, rate limits) and uploaded test files.
- Puts a fresh random admin setup key into the zipped config.php and writes it to
  dist/ so it is never shared in chat or committed.

Usage:
  python tools/package.py                                        live site (mirapools.in)
  python tools/package.py --demo https://mirapools.empyef.com    preview copy, hidden from search engines
"""

import argparse
import datetime
import pathlib
import re
import secrets
import zipfile

ROOT = pathlib.Path(__file__).resolve().parent.parent
SITE = ROOT / "site"
DIST = ROOT / "dist"

DEMO_ROBOTS = "# Preview copy: keep search engines out\nUser-agent: *\nDisallow: /\n"


def skip(rel: str) -> bool:
    if rel.startswith("data/") and rel != "data/.htaccess":
        return True  # local admin password, gallery edits, enquiries, locks
    if rel.startswith("uploads/") and not rel.endswith(".htaccess"):
        return True  # images uploaded locally while testing
    if rel.endswith((".lock", ".tmp", ".log")):
        return True
    return "/." in "/" + rel and not rel.endswith(".htaccess")


def add_text(z: zipfile.ZipFile, rel: str, text: str) -> None:
    # writestr() alone stores no permissions, so Linux hosts extract the file as unreadable (0000).
    info = zipfile.ZipInfo(rel, date_time=datetime.datetime.now().timetuple()[:6])
    info.compress_type = zipfile.ZIP_DEFLATED
    info.external_attr = (0o100644 & 0xFFFF) << 16
    z.writestr(info, text)


def add_dir(z: zipfile.ZipFile, rel: str) -> None:
    info = zipfile.ZipInfo(rel.rstrip("/") + "/", date_time=datetime.datetime.now().timetuple()[:6])
    info.external_attr = ((0o040755 & 0xFFFF) << 16) | 0x10
    z.writestr(info, "")


def add_file(z: zipfile.ZipFile, path: pathlib.Path, rel: str) -> None:
    # Files from Windows carry no Unix mode either; set the usual 0644.
    info = zipfile.ZipInfo.from_file(path, rel)
    info.compress_type = zipfile.ZIP_DEFLATED
    info.external_attr = (0o100644 & 0xFFFF) << 16
    with open(path, "rb") as fh:
        z.writestr(info, fh.read())


def sub_once(pattern: str, repl: str, text: str, what: str) -> str:
    text, n = re.subn(pattern, repl, text)
    if n != 1:
        raise SystemExit(f"Could not find {what} in config.php")
    return text


def main() -> None:
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument("--demo", metavar="URL", help="build a preview copy for this address (noindex, robots blocked)")
    ap.add_argument("--cpanel-php", metavar="PKG", help="cPanel hosts only: put the PHP handler for this package (e.g. ea-php81) at the top of .htaccess")
    args = ap.parse_args()
    if args.cpanel_php and not re.match(r"^ea-php[0-9]{2}$", args.cpanel_php):
        raise SystemExit("--cpanel-php needs a cPanel package name like ea-php81 or ea-php83")
    demo_url = args.demo.rstrip("/") if args.demo else ""
    if demo_url and not re.match(r"^https?://[a-z0-9.-]+$", demo_url):
        raise SystemExit("--demo needs a plain address like https://mirapools.empyef.com")

    DIST.mkdir(exist_ok=True)
    key = secrets.token_hex(16)
    stamp = datetime.date.today().strftime("%Y%m%d")
    kind = "demo" if demo_url else "site"
    out = DIST / f"mirapools-{kind}-{stamp}.zip"
    keyfile = DIST / ("SETUP_KEY-demo.txt" if demo_url else "SETUP_KEY.txt")
    target = demo_url or "https://mirapools.in"

    count = 0
    with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as z:
        dirs = {"data", "uploads", "uploads/gallery", "uploads/site"}
        for path in SITE.rglob("*"):
            rel = path.relative_to(SITE).as_posix()
            if path.is_dir() and not skip(rel + "/x") and not rel.startswith(("data/", "uploads/")):
                dirs.add(rel)
        for d in sorted(dirs):
            add_dir(z, d)  # explicit 0755 folders, including the empty data/ and uploads/ ones
        for path in sorted(SITE.rglob("*")):
            if path.is_dir():
                continue
            rel = path.relative_to(SITE).as_posix()
            if skip(rel):
                continue
            if rel == "includes/config.php":
                text = path.read_text(encoding="utf-8")
                text = sub_once(r"define\('ADMIN_SETUP_KEY', '[^']*'\);", f"define('ADMIN_SETUP_KEY', '{key}');", text, "ADMIN_SETUP_KEY")
                if demo_url:
                    text = sub_once(r"\?: 'https://mirapools\.in'\)", f"?: '{demo_url}')", text, "SITE_URL")
                    text = sub_once(r"define\('SITE_NOINDEX', false\);", "define('SITE_NOINDEX', true);", text, "SITE_NOINDEX")
                add_text(z, rel, text)
            elif rel == "robots.txt" and demo_url:
                add_text(z, rel, DEMO_ROBOTS)
            elif rel == ".htaccess" and args.cpanel_php:
                # Same block cPanel's MultiPHP Manager writes; extracting the zip would otherwise remove it.
                block = (
                    "# php -- BEGIN cPanel-generated handler, do not edit\n"
                    f"# Set the “{args.cpanel_php}” package as the default “PHP” programming language.\n"
                    "<IfModule mime_module>\n"
                    f"  AddHandler application/x-httpd-{args.cpanel_php} .php .php8 .phtml\n"
                    "</IfModule>\n"
                    "# php -- END cPanel-generated handler, do not edit\n\n"
                )
                add_text(z, rel, block + path.read_text(encoding="utf-8"))
            else:
                add_file(z, path, rel)
            count += 1

    keyfile.write_text(
        f"Admin setup key for {out.name}\n{key}\n\n"
        f"Enter it once at {target}/admin/ to create the admin password.\n"
        "Keep this file private. Delete it after setup.\n",
        encoding="utf-8",
    )
    print(f"{out.name}: {count} files, {out.stat().st_size / 1024 / 1024:.1f} MB, for {target}" + (" (hidden from search engines)" if demo_url else ""))
    print(f"Setup key written to {keyfile}")


if __name__ == "__main__":
    main()
