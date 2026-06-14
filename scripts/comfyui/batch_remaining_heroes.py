#!/usr/bin/env python3
"""Generate all remaining Tier B+ heroes (one Comfy run per Translation_Key)."""

from __future__ import annotations

import subprocess
import sys
from pathlib import Path

REPO = Path(__file__).resolve().parents[2]
EXPORT = REPO / "scripts/comfyui/export_cover.py"

BASE = (
    "Wide cinematic editorial illustration for a Spanish tech blog named Praderas, "
    "soft golden meadow light, {hint}, grass-green and warm neutral tones, "
    "professional quiet atmosphere, no readable text, no logos, no watermarks, "
    "high detail, tasteful color grading"
)

# (translation_key, output_stem without -hero, visual hint clause, seed)
JOBS: list[tuple[str, str, str, int]] = [
    # CTD series 6–13
    (
        "praderas-ctd-06",
        "ctd-06-database-schema-time-tracking",
        "abstract entity-relationship diagram and database table motifs as soft modular blocks and gentle schema lines",
        6062026,
    ),
    (
        "praderas-ctd-07",
        "ctd-07-database-roles-permissions",
        "abstract database roles and permission grants as layered access rings and subtle key silhouettes",
        7062026,
    ),
    (
        "praderas-ctd-08",
        "ctd-08-database-extra-tables",
        "abstract stacked table layers and migration lanes suggesting expanding database schema",
        8062026,
    ),
    (
        "praderas-ctd-09",
        "ctd-09-rest-api-client",
        "abstract REST client and HTTP request-response motifs as calm linked nodes and API path shapes",
        9062026,
    ),
    (
        "praderas-ctd-10",
        "ctd-10-react-frontend-roles",
        "abstract React scaffolding and role management UI wireframes as responsive column hints without labels",
        10062026,
    ),
    (
        "praderas-ctd-11",
        "ctd-11-react-role-management",
        "abstract list-and-edit role screens as soft card grids and form silhouettes in a frontend app",
        11062026,
    ),
    (
        "praderas-ctd-12",
        "ctd-12-react-project-management",
        "abstract project CRUD screens as gentle kanban and table motifs in a React interface",
        12062026,
    ),
    (
        "praderas-ctd-13",
        "ctd-13-react-create-user",
        "abstract user creation form and onboarding flow shapes in a time-tracking React app",
        13062026,
    ),
    # Cybersecurity / privacy (B3)
    (
        "praderas-b3-cs-intro",
        "b3-cybersecurity-intro",
        "abstract layered shields and lock motifs suggesting introductory cybersecurity concepts, cool teal accents",
        20062001,
    ),
    (
        "praderas-b3-cs-advanced",
        "b3-cybersecurity-advanced-data-protection",
        "abstract advanced security depth fields and encrypted data vault silhouettes, cool teal on neutrals",
        20062002,
    ),
    (
        "praderas-b3-cs-digital-world",
        "b3-cybersecurity-digital-world",
        "abstract digital landscape with protective shields over soft network topology, privacy atmosphere",
        20062003,
    ),
    (
        "praderas-b3-internet-not-safe-ii",
        "b3-internet-privacy-ii",
        "abstract obfuscated identity trails and privacy veil motifs over gentle web shapes",
        20062004,
    ),
    (
        "praderas-b3-social-privacy",
        "b3-social-media-privacy",
        "abstract social network nodes with privacy filters and muted sharing silhouettes",
        20062005,
    ),
    (
        "praderas-b3-geolocation",
        "b3-geolocation-internet",
        "abstract map pin and location signal motifs with soft privacy halo, restrained glow",
        20062006,
    ),
    # AI (B4)
    (
        "praderas-b4-ai-medicine",
        "b4-ai-medicine",
        "abstract neural network motifs gently intertwined with medical cross and diagnostic scan shapes",
        20062007,
    ),
    (
        "praderas-b4-ai-early-disease-detection",
        "b4-ai-early-disease-detection",
        "abstract early signal detection waves and neural subgraphs suggesting preventive health analytics",
        20062008,
    ),
    (
        "praderas-b4-ai-society-impact",
        "b4-ai-society-impact",
        "abstract human silhouettes connected to soft AI node clusters suggesting societal change",
        20062009,
    ),
    (
        "praderas-b4-ai-entertainment",
        "b4-ai-entertainment-industry",
        "abstract play button and creative media shapes linked by restrained neural glow",
        20062010,
    ),
    (
        "praderas-b4-ai-games-evolution",
        "b4-ai-video-games-evolution",
        "abstract game controller and evolving level-map motifs with subtle AI node links",
        20062011,
    ),
    (
        "praderas-b4-neural-nets",
        "b4-neural-networks-fundamentals",
        "abstract layered perceptron and neural graph fundamentals as calm node-and-link topology",
        20062012,
    ),
    # Productivity (B5)
    (
        "praderas-b5-remote-work-tips",
        "b5-remote-work-productivity-tips",
        "abstract home office still-life with calm timeline and focus motifs suggesting remote work",
        20062013,
    ),
    (
        "praderas-b5-etherpad-guide",
        "b5-etherpad-collaboration-guide",
        "abstract multi-cursor collaborative editing shapes and shared document panels",
        20062014,
    ),
    (
        "praderas-b5-taskwarrior-guide",
        "b5-taskwarrior-task-management",
        "abstract CLI task list and filter motifs as orderly stacked lanes and check shapes",
        20062015,
    ),
    (
        "praderas-b5-redmine-guide",
        "b5-redmine-project-management",
        "abstract ticketing board and project tracker silhouettes in a self-hosted tool mood",
        20062016,
    ),
    (
        "praderas-b5-nextcloud-deck",
        "b5-nextcloud-deck-groupware",
        "abstract kanban deck boards combined with cloud file and calendar motifs",
        20062017,
    ),
    (
        "praderas-b5-focalboard-guide",
        "b5-focalboard-task-project-management",
        "abstract self-hosted kanban boards with calm card columns and project lanes",
        20062018,
    ),
    # Mobile (B6)
    (
        "praderas-b6-mobile-dev-beginners",
        "b6-mobile-development-beginners",
        "abstract smartphone wireframes and beginner-friendly app scaffolding shapes",
        20062019,
    ),
    (
        "praderas-b6-first-mobile-app",
        "b6-first-mobile-app-practical",
        "abstract first mobile app blueprint with gentle phone frame and component blocks",
        20062020,
    ),
    (
        "praderas-b6-mobile-frameworks",
        "b6-mobile-frameworks-exploration",
        "abstract modular mobile framework blocks and cross-platform bridge motifs",
        20062021,
    ),
    (
        "praderas-b6-mobile-languages-tools",
        "b6-mobile-languages-tools",
        "abstract toolchain icons as soft shapes around a central mobile device silhouette",
        20062022,
    ),
    (
        "praderas-b6-mobile-testing-strategy",
        "b6-mobile-testing-strategy",
        "abstract test harness and device matrix motifs suggesting mobile QA strategy",
        20062023,
    ),
    (
        "praderas-b6-mobile-ui-ux",
        "b6-mobile-ui-ux-design",
        "abstract mobile UI cards, touch targets, and UX flow arrows without readable labels",
        20062024,
    ),
    # Crypto / blockchain (B7)
    (
        "praderas-b7-blockchain-crypto-intro",
        "b7-blockchain-crypto-intro",
        "abstract chained blocks and distributed ledger motifs with calm horizon scale",
        20062025,
    ),
    (
        "praderas-b7-bitcoin-node",
        "b7-bitcoin-node-setup",
        "abstract full node and peer network topology with restrained blockchain glow",
        20062026,
    ),
    (
        "praderas-b7-electrum-server",
        "b7-electrum-server-wallet",
        "abstract lightweight wallet and server sync motifs with gentle lightning-link shapes",
        20062027,
    ),
    (
        "praderas-b7-celestia-tia",
        "b7-celestia-tia-crypto-analysis",
        "abstract modular blockchain layers and data availability motifs, no coin logos",
        20062028,
    ),
    # Systems / society / education (B8)
    (
        "praderas-b8-debian-11-install",
        "b8-debian-11-install-walkthrough",
        "abstract server rack and Linux install wizard silhouettes suggesting Debian setup",
        20062029,
    ),
    (
        "praderas-b8-ubuntu-vs-debian",
        "b8-ubuntu-vs-debian-comparison",
        "abstract twin distro motifs as balanced scales over calm server shapes",
        20062030,
    ),
    (
        "praderas-b8-laptop-buying-guide",
        "b8-laptop-buying-guide",
        "abstract open laptop silhouette with spec comparison card shapes on a meadow desk",
        20062031,
    ),
    (
        "praderas-b8-linux-vm-productivity-tools",
        "b8-linux-vm-productivity-tools",
        "abstract virtual machine layers with productivity app icons as soft tool silhouettes",
        20062032,
    ),
    (
        "praderas-b8-emacs-guide",
        "b8-emacs-productivity-guide",
        "abstract Emacs buffer panes and org-mode capture motifs as calm editor chrome",
        20062033,
    ),
    (
        "praderas-b8-remote-team-productivity-tools",
        "b8-remote-team-productivity-tools",
        "abstract distributed team nodes connected by collaboration tool motifs",
        20062034,
    ),
    (
        "praderas-b8-online-learning-benefits",
        "b8-online-learning-benefits",
        "abstract remote classroom and flexible schedule motifs over digital meadow light",
        20062035,
    ),
    (
        "praderas-b8-future-of-education",
        "b8-future-of-education-digital-era",
        "abstract digital learning paths and open book silhouettes suggesting education evolution",
        20062036,
    ),
    (
        "praderas-b8-emerging-tech-trends-society",
        "b8-emerging-tech-trends-society",
        "abstract horizon line with emerging technology pillars affecting society shapes",
        20062037,
    ),
    (
        "praderas-b8-future-tech-innovation-horizon",
        "b8-future-tech-innovation-horizon",
        "abstract innovation horizon with soft prototype and research motif constellations",
        20062038,
    ),
]


def main() -> int:
    start = int(sys.argv[1]) if len(sys.argv) > 1 else 0
    end = int(sys.argv[2]) if len(sys.argv) > 2 else len(JOBS)
    subset = JOBS[start:end]
    print(f"Batch heroes: jobs {start}..{end - 1} ({len(subset)} of {len(JOBS)})")

    failed: list[str] = []
    for i, (key, stem, hint, seed) in enumerate(subset, start=start):
        png = REPO / f"assets/images/{stem}-hero.png"
        positive = BASE.format(hint=hint)
        prefix = f"praderas_batch_{i:03d}"
        cmd = [
            sys.executable,
            str(EXPORT),
            "--output",
            str(png),
            "--positive",
            positive,
            "--seed",
            str(seed),
            "--prefix",
            prefix,
            "--webp",
            "--webp-delete-png",
            "--translation-key",
            key,
        ]
        print(f"\n=== [{i + 1}/{len(JOBS)}] {key} -> {stem}-hero.webp ===")
        try:
            subprocess.run(cmd, check=True, cwd=REPO)
        except subprocess.CalledProcessError:
            failed.append(key)
            print(f"FAILED: {key}", file=sys.stderr)

    if failed:
        print(f"\n{len(failed)} failure(s):", ", ".join(failed), file=sys.stderr)
        return 1
    print("\nAll batch jobs completed.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
