---
Title: "The internet is not a safe place (II) — privacy"
Description: How privacy is at risk when we browse carelessly—and what large platforms learn from cookies, embeds, and cross-site scripts.
Author: Luis Amigo
Date: 2020-12-10
Template: post
Tags: Ciberseguridad, Privacidad
Lang: en
Translation_Key: praderas-b3-internet-not-safe-ii
Image: /assets/images/b3-internet-privacy-ii-hero.webp

---

Security is not our only concern online. Privacy has become a central battleground: companies accumulate user data for their own products—or sell aggregated feeds to third parties—often with limited transparency.

### What data is collected, and how?

The most common mechanism is **tracking cookies**—small pieces of state stored in your browser. With a per-user identifier, visits can be stitched together over time.

Cookies are scoped to the domain that set them, so in theory Google, Facebook, Twitter, or Amazon only read their IDs on their own domains. That limitation would make third-party tracking useless—which is why those platforms encourage publishers to embed **their** JavaScript or iframes on other sites. When third-party code runs in your page, it can access its own cookies and often interact with the host page’s data model, correlate it with the tracking ID, and phone home with telemetry.

### What gets embedded?

Google Analytics is ubiquitous—rough estimates suggest a large share of sites use it, alongside Google’s dominance in search and Android. That concentration means Google’s trackers can appear across a huge fraction of pages and devices, linking queries, browsing patterns, device types, and—where accounts exist—purchase history, email content (for Gmail users), and payment instruments.

### What do they do with the data?

**Profiles**—lots of them. Broadly, data is used in two ways:

- **Product and ads optimization:** Targeted advertising is a major revenue line for large platforms; similar logic applies to “related products” modules on marketplaces.
- **Data sales:** Vendors claim aggregation and anonymization, but incidents like Cambridge Analytica showed how brittle those promises can be. Even “anonymous” datasets are often re-identifiable, sometimes aided by IP-based location—especially risky outside dense cities where fewer users share the same coarse location bucket.

### How can you reduce exposure?

As discussed elsewhere, a VPN on desktop can reduce how trivially your IP anchors profiles—but it is not a complete fix.

Simple, imperfect mitigations:

- Turn on **every privacy control** the vendor offers (e.g., disable web activity logging, location history, YouTube history, and ad personalization where available).
- Use **tracker blockers**; many sites work fine without third-party analytics scripts.
- Avoid **Chrome** as a daily driver if you distrust Google’s incentives—even “incognito” does not erase the business model.
- Prefer **non-Google search** where feasible (Bing, DuckDuckGo, Ecosia, etc.).
- Use **Facebook or Twitter in private windows** only, if you must use them.
- Treat **Amazon and eBay similarly**—private sessions reduce cross-site correlation.
- On mobile, **revoke non-essential permissions** (location, background data) for large consumer apps.
- Small habit changes often make ads feel less “telepathic”—not because microphones are everywhere, but because cross-site identity shrinks.
