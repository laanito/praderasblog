---
Title: Tuqan — Stage 3: Configuration Externalization and Safer Queries
Description: How in Stage 3 of the Tuqan modernization we started removing hardcoded credentials and replacing string-concatenated SQL queries with prepared statements on the most critical paths of the application.
Date: 2026-05-26 10:00AM
Template: post
Author: Luis Amigo
Tags: Desarrollo Web, Sistemas, Productividad, Ciberseguridad
Lang: en
Translation_Key: tuqan-stage-3-config-secrets-query-safety
Series: Tuqan Modernization
Series_Slug: tuqan-modernization
Series_Order: 3
Image: /assets/images/tuqan-stage-3-config-secrets-hero.webp
---

# Tuqan — Stage 3: From Hardcoded Credentials to Prepared Statements

In previous stages we discussed the audit of the project and the decision to create a 100% Docker-based development environment. In Stage 3 we took a more concrete and high-impact step: we began removing secrets from the codebase and reducing SQL injection risks on the most sensitive flows of the application.

This article is not a technical diff summary. It explains **why** these changes matter in a real application that has been managing quality and environmental systems for nearly 20 years.

## The Problem We Inherited

Tuqan is a tool used by organizations to comply with ISO 9001 and ISO 14001 standards. This means it handles sensitive information over many years: controlled documents, audit records, signatures, user permissions, and more.

The original code (from 2005) contained several patterns that were very common at the time:

- Database credentials written directly inside PHP files.
- SQL queries built through string concatenation.
- Multiple entry points repeating the same connection and query logic.

While these patterns were acceptable back then, today they represent real risks:

- Anyone with access to the repository can see the credentials.
- Queries built with concatenation are vulnerable to SQL injection if any value is not properly controlled.
- It is extremely difficult to write reliable automated tests when the database is tightly coupled everywhere.

## What We Did in Stage 3

The work in this stage focused on two main fronts:

### 1. Configuration Externalization

We moved the reading of credentials and some configuration parameters so they are loaded from environment variables instead of being hardcoded in the code.

This brings several immediate benefits:

- Credentials no longer travel inside the repository.
- In the development environment (Docker) we can have a local `.env.docker` file that is never committed.
- In production, variables can be securely injected through the orchestrator or the server.

There is still work ahead to clean up all the legacy points that read the old configuration, but the correct direction has been established.

### 2. Introducing Prepared Statements on Critical Paths

We created a new method `consultaPreparada()` in the data access layer that allows executing queries with parameters safely.

We then started replacing, on the most exposed flows, the old string-concatenated queries with this new method:

- User data loading during authentication.
- Company login logic.
- Document updates in the editor.
- Internal message reading.

This is not a massive migration done all at once (that would be too risky). It is about gradually protecting the highest-exposure points while maintaining compatibility with the rest of the legacy code.

## The Importance of Testability

One of the clearest learnings from this stage is that **you cannot sustainably improve security without also improving testability**.

To confidently refactor database access code, we introduced injection points (`setDbHandler()`) in the most critical classes. This allows us to write unit tests that verify that prepared statements are being used correctly, without needing a real database running for every test execution.

These kinds of changes, although they may seem small, are what allow a 20-year-old application to keep evolving safely instead of becoming frozen out of fear of breaking something.

## What This Means for Tuqan Users

For organizations that rely on Tuqan for their quality and environmental management systems, these changes have a direct impact:

- A smaller attack surface in one of the tools that handles the most sensitive data.
- Greater ability to evolve the system without fear of introducing classic vulnerabilities.
- A clearer path toward adding automated tests that protect current behavior.

This is not a change visible from the user interface. It is a change felt in the peace of mind with which the system can continue to be maintained and improved over the coming years.

## Next Steps

Stage 3 leaves us with a much more solid foundation regarding secret management and safer queries. Future stages will likely focus on:

- Continuing to replace uses of the old query builder in more parts of the system.
- Improving test coverage on critical flows.
- Advancing the cleanup of legacy dependencies that complicate maintenance.

As always, all the work is recorded both in the Tuqan repository and in this series of articles.

---

This article is part of the **Tuqan Modernization** series. You can read the previous ones here:

- [Tuqan — Phase 0: Strategic Foundation Audit and Roadmap](https://blog.praderas.org/blog/en/tuqan-phase-0-strategic-foundation-audit-and-roadmap)
- [Tuqan — PHP 8 Migration Plan, Docker Environment and Testing Harness](https://blog.praderas.org/blog/en/tuqan-php8-docker-migration-plan)

The migration code and documents are available at [github.com/laanito/tuqan](https://github.com/laanito/tuqan).