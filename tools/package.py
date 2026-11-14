"""Build a clean upload zip of the website for PHP hosting.

- Leaves out local admin data (password, enquiries, rate limits) and uploaded test files.
- Puts a fresh random admin setup key into the zipped config.php and writes it to
  dist/SETUP_KEY.txt so it is never shared in chat or committed.

Usage:  python tools/package.py
"""

import datetime
import pathlib
import re
import secrets
import zipfile

ROOT = pathlib.Path(__file__).resolve().parent.parent
SITE = ROOT / "site"
DIST = ROOT / "dist"


def skip(rel: str) -> bool:
    if rel.startswith("data/") and rel != "data/.htaccess":
        return True  # local admin password, gallery edits, enquiries, locks
    if rel.startswith("uploads/") and not rel.endswith(".htaccess"):
        return True  # images uploaded locally while testing
    return rel.endswith((".lock", ".tmp", ".log")) or "/." in "/" + rel and not rel.endswith(".htaccess")


def main() -> None:
    DIST.mkdir(exist_ok=True)
    key = secrets.token_hex(16)
    stamp = datetime.date.today().strftime("%Y%m%d")
    out = DIST / f"mirapools-site-{stamp}.zip"
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
                text, n = re.subn(r"define\('ADMIN_SETUP_KEY', '[^']*'\);", f"define('ADMIN_SETUP_KEY', '{key}');", text)
                if n != 1:
                    raise SystemExit("Could not find ADMIN_SETUP_KEY in config.php")
                z.writestr(rel, text)
            else:
                z.write(path, rel)
            count += 1
    (DIST / "SETUP_KEY.txt").write_text(
        f"Admin setup key for {out.name}\n{key}\n\n"
        "Enter it once at https://mirapools.in/admin/ to create the admin password.\n"
        "Keep this file private. Delete it after setup.\n",
        encoding="utf-8",
    )
    print(f"{out.name}: {count} files, {out.stat().st_size / 1024 / 1024:.1f} MB")
    print(f"Setup key written to {DIST / 'SETUP_KEY.txt'}")


if __name__ == "__main__":
    main()
