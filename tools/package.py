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


def sub_once(pattern: str, repl: str, text: str, what: str) -> str:
    text, n = re.subn(pattern, repl, text)
    if n != 1:
        raise SystemExit(f"Could not find {what} in config.php")
    return text


def main() -> None:
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument("--demo", metavar="URL", help="build a preview copy for this address (noindex, robots blocked)")
    args = ap.parse_args()
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
        for d in ("data/", "uploads/", "uploads/gallery/", "uploads/site/"):
            z.writestr(zipfile.ZipInfo(d), "")  # keep the empty folders
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
                z.writestr(rel, text)
            elif rel == "robots.txt" and demo_url:
                z.writestr(rel, DEMO_ROBOTS)
            else:
                z.write(path, rel)
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
