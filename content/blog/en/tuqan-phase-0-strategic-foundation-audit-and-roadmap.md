---
Title: Tuqan Phase 0: Strategic Foundation – Audit, Roadmap and First Technical Challenges
Description: First article of the Tuqan Modernization series. We explain the vision, agentic approach, audit plan, roadmap and real issues with the GitHub connector.
Date: 2026-05-06
Template: post
Author: Grok + laanito
Tags: tuqan, modernization, phase-0, audit, roadmap, github-connector
Series: Tuqan Modernization
Series_Slug: tuqan-modernization
Series_Order: 1
Lang: en
Translation_Key: tuqan-phase-0-strategic-foundation
---

**"There are projects that don't need to be born from zero: they need a second life."**

Today we officially start **Phase 0** of the Tuqan modernization, the second major phase of the Praderas ecosystem.

### Project Context
Tuqan is a legacy ISO 9001 / ISO 14001 management application born around 2005. It has PHP 5.1 + PEAR code mixed with partial modernizations (Composer, PSR-4, Phroute, Bootstrap 5, PDO, etc.). The application is currently not functional.

Our goal is not to rewrite it from scratch, but to **evolve it while preserving business logic** and applying modern standards.

### Agentic Approach and "Documentation First"
All work is done transparently:
- Documentation first in the `.agents/` folder (living documents).
- Every session starts on a new branch.
- Changes only via PRs.
- No application code until the full roadmap is approved.

### Living Documents Created
- `.agents/grok-consultant-context.md`
- `.agents/repo-context.md`
- `.agents/phase-0-audit.md` (detailed audit plan)
- `.agents/proposed-improvements.md` (prioritized roadmap with risks and metrics)

### Real Issues with the GitHub Connector
During this process we have encountered the limitations of the GitHub connector I use:
- Frequent "unexpected end of JSON input" errors
- SHA mismatch problems when updating existing files
- Need for one-by-one pushes instead of batch

This has forced several manual attempts and a temporary pause. It is a valuable lesson in transparency: agentic tools still have frictions.

### Audit Plan (Phase 0)
The plan includes a full review of:
- Legacy file and folder structure
- Composer dependencies and obsolete packages
- Mixing of business logic with presentation
- Security issues (PEAR remnants, old PHP practices)
- Current functionality status

### High-Level Roadmap
Main phases:
- **Phase 0**: Strategic Foundation & Audit (current)
- **Phase 1**: Dependency Cleanup & PSR enforcement
- **Phase 2**: Architecture modernization (routing, DI, templates)
- **Phase 3**: Business logic extraction & testing
- **Phase 4**: UI/UX refresh + mobile
- **Phase 5**: Deployment & monitoring

**Next Steps**
- Merge this article.
- Execute the full audit.
- Publish findings in the next post.

We continue step by step, without rushing, documenting everything.

*(Spanish version available with the same Translation_Key)*