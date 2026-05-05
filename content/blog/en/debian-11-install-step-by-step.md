---
Title: "Debian 11 (Bullseye) installation walkthrough"
Description: Prepare boot media, walk the text/graphical installer for language, disk partitioning, user creation, desktop metapackages, and GRUB—aimed at a clean stable workstation or server.
Author: Luis Amigo
Date: 2023-08-09
Template: post
Tags: Sistemas
Lang: en
Translation_Key: praderas-b8-debian-11-install
---

## Introduction

**Debian 11 “Bullseye”** is a stable, security-conscious Linux distribution popular for servers and careful desktops. This guide walks through a standard install with emphasis on **disk planning**—the step most likely to cause regret if rushed.

## Preparation

1. **Download an ISO** from `https://www.debian.org` matching your architecture (most laptops: **amd64**). Netinst images stay small; full DVD sets bundle more offline packages.  
2. **Create boot media** with **balenaEtcher**, **Rufus**, or `dd` from another Linux box. Verify the USB actually boots before wiping a production machine.

## Installation flow

1. **Boot the installer** from USB/DVD; firmware settings must prefer removable media.  
2. Choose **Install** (not merely live mode) when you intend to persist to disk.  
3. Select **language**, **locale**, and **timezone**—this drives default formats and clock behavior.  
4. **Keyboard layout:** test characters; auto-detect helps but manual selection is fine.  
5. **Networking and hostname:** wired DHCP usually “just works”; set a memorable hostname for LAN services.  
6. **User account:** pick a strong password; note whether the installer adds you to `sudo` (varies by image and choices).  
7. **Disk partitioning:** guided partitioning suits beginners; advanced users may carve separate `/`, `/home`, and `swap` (or swap files on recent defaults). Double-check device names before committing destructive changes.  
8. **Confirm partition map** on the summary screen—this is your last easy undo.  
9. **Base system install** copies packages and configures essential services.  
10. **Software selection:** choose a desktop metapackage (GNOME, KDE, Xfce, etc.) or a minimal system if you will install services manually.  
11. **Install GRUB** to the correct disk when dual-booting—mis-targeting here overwrites other OS bootloaders.  
12. **Reboot** into the new system; remove installation media when prompted.

## Conclusion

Debian’s installer rewards patience: especially on **partitioning**, read twice and apply once. After first boot, run full updates, configure backups, and harden remote access if you exposed SSH.

Welcome to a distribution valued for predictability—next steps are yours: containers, web stacks, or a daily-driver desktop with years of security support.
