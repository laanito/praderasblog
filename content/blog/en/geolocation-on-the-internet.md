---
Title: Geolocation on the internet
Description: How an IP address can be mapped to a rough physical location—and why that matters for privacy.
Author: Luis Amigo
Date: 2020-06-23
Template: post
Tags: Privacidad
Lang: en
Translation_Key: praderas-b3-geolocation
Image: /assets/images/b3-geolocation-internet-hero.webp

---

### How does the internet work at a high level?

Think of the internet as a road network. To travel far, you combine highways and local roads, switching links at junctions—**nodes** in networking terms.

Data travels similarly: packets hop across high-capacity links and smaller networks until they reach a destination.

### What is an IP address?

Each endpoint on the network has a unique identifier—an **IP address**. **IPv4** uses four numbers from 0–255 (`x.x.x.x`), yielding a little over four billion combinations—too small for today’s device count. **IPv6** scales that dramatically with eight groups of hexadecimal values—an enormous address space.

### What does your IP reveal?

Return traffic must find you, so messages include your IP (and the last hop’s IP). That address is highly sensitive: it anchors profiling, can enable abuse attempts against your network edge, and—our focus here—supports **IP geolocation**.

### How do you “get” an IP?

Unless you pay for a static address, your ISP owns the pool and assigns addresses **dynamically** when you connect. In practice, home routers often stay on for weeks, so the address changes rarely.

### What is IP geolocation?

ISPs map address blocks to regions and publish allocations to internet registries. Companies aggregate those maps; given an IP, they can often infer a **city or district** with limited error. Many sites—including DuckDuckGo—will show you what they infer about your connection.

### What is it used for?

Common uses include **audience segmentation**: analytics (where visits originate), advertising (exclude non-serviceable regions), and local results (“coffee near me” needs a coarse location signal).
