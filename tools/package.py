"""Build a clean upload zip of the website for PHP hosting.

- Leaves out local admin data (password, enquiries, rate limits) and uploaded test files.
- Puts a fresh random admin setup key into the zipped config.php and writes it to
  dist/ so it is never shared in chat or committed.

Usage:
  python tools/package.py                                        live site (mirapools.in)
  python tools/package.py --demo https://mirapools.empyef.com    preview copy, hidden from search engines
  python tools/package.py --update-since <commit>                 only files changed since that commit;
                                                                  keeps the server's .htaccess, config.php, robots.txt
"""

import argparse
import datetime
import pathlib
import re
import secrets
import subprocess
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
    ap.add_argument("--update-since", metavar="COMMIT", help="update zip: only site files changed since this git commit")
    args = ap.parse_args()
    demo_url = args.demo.rstrip("/") if args.demo else ""
    if demo_url and not re.match(r"^https?://[a-z0-9.-]+$", demo_url):
        raise SystemExit("--demo needs a plain address like https://mirapools.empyef.com")

    DIST.mkdir(exist_ok=True)
    if args.update_since:
        build_update(args.update_since, demo_url)
        return
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


# Files the server keeps as they are on an update: cPanel writes its PHP setting into .htaccess,
# config.php holds the live setup key, and robots.txt differs between demo and live.
KEEP_ON_SERVER = {".htaccess", "includes/config.php", "robots.txt"}


def git(*args: str) -> list[str]:
    out = subprocess.run(["git", *args], cwd=ROOT, check=True, capture_output=True, text=True).stdout
    return [line for line in out.splitlines() if line.strip()]


def build_update(since: str, demo_url: str) -> None:
    changed = git("diff", "--name-only", "--diff-filter=ACMR", since, "--", "site")
    changed += git("ls-files", "--others", "--exclude-standard", "site")
    removed = git("diff", "--name-only", "--diff-filter=D", since, "--", "site")
    rels = sorted({c[len("site/"):] for c in changed} - KEEP_ON_SERVER)
    rels = [r for r in rels if not skip(r) and (SITE / r).is_file()]
    if not rels:
        raise SystemExit(f"No site files changed since {since}.")
    stamp = datetime.date.today().strftime("%Y%m%d")
    out = DIST / f"mirapools-{'demo' if demo_url else 'site'}-update-{stamp}.zip"
    with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as z:
        for d in sorted({str(pathlib.PurePosixPath(r).parent) for r in rels} - {"."}):
            add_dir(z, d)
        for r in rels:
            add_file(z, SITE / r, r)
    print(f"{out.name}: {len(rels)} changed files since {since}")
    for r in rels:
        print("  " + r)
    gone = [g[len("site/"):] for g in removed if g[len("site/"):] not in KEEP_ON_SERVER]
    if gone:
        print("No longer used (can be deleted on the server):")
        for g in gone:
            print("  " + g)


if __name__ == "__main__":
    main()
