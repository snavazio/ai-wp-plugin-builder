#!/usr/bin/env python3
"""
Take WordPress.org plugin-directory screenshots of a plugin running in the local wp-env sandbox.

Usage: wporg-screens.py <baseUrl> <outDir> <shotsJson>
  shotsJson: [{"path": "/wp-admin/options-general.php?page=x", "caption": "Settings page", "admin": true}, ...]

Writes screenshot-1.png, screenshot-2.png … in order, and prints one line per shot. Logs in once with the
wp-env sandbox's default administrator (admin / password) — that sandbox is the throwaway local test site
the gates use, never a real site. Only http://localhost / 127.0.0.1 base URLs are accepted.
"""
import json
import os
import sys
from urllib.parse import urlparse

from playwright.sync_api import sync_playwright


def main():
    if len(sys.argv) < 4:
        print("Usage: wporg-screens.py <baseUrl> <outDir> <shotsJson>", file=sys.stderr)
        sys.exit(2)
    base, out, shots = sys.argv[1].rstrip("/"), sys.argv[2], json.loads(sys.argv[3])
    if urlparse(base).hostname not in ("localhost", "127.0.0.1"):
        print("Refusing: screenshots are only taken from the local wp-env sandbox.", file=sys.stderr)
        sys.exit(2)
    os.makedirs(out, exist_ok=True)
    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page(viewport={"width": 1280, "height": 800}, device_scale_factor=1)
        if any(s.get("admin") for s in shots):
            page.goto(base + "/wp-login.php", wait_until="domcontentloaded")
            page.fill("#user_login", "admin")
            page.fill("#user_pass", "password")
            page.click("#wp-submit")
            page.wait_for_load_state("networkidle")
        n = 0
        for shot in shots:
            page.goto(base + shot["path"], wait_until="networkidle")
            # Hide WordPress update nags so the plugin itself is what shows.
            # Hide update nags and the sandbox-only Plugin Check menu so the plugin itself is what shows.
            page.add_style_tag(content=".update-nag,.notice-warning.notice-alt,#wp-admin-bar-updates,#adminmenu a[href*='plugin-check']{display:none!important}")
            n += 1
            f = os.path.join(out, f"screenshot-{n}.png")
            page.screenshot(path=f)
            print(f"screenshot-{n}.png\t{shot.get('caption', '')}")
        browser.close()


if __name__ == "__main__":
    main()
