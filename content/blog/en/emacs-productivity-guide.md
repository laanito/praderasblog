---
Title: "Emacs productivity guide: a powerful, customizable editor"
Description: Install paths on major OSes, starter init.el ideas, basic key chords, and productivity modes like Org-mode, Magit, Eshell, and Dired—with pointers to upstream docs.
Author: Luis Amigo
Date: 2023-09-02 11:33AM
Template: post
Tags: Productividad
Lang: en
Translation_Key: praderas-b8-emacs-guide
Image: /assets/images/b8-emacs-productivity-guide-hero.webp

---

**Emacs** is a highly extensible text environment—decades of Lisp customization turned it into a productivity platform, not only an editor. This guide covers install basics, starter configuration, everyday commands, and a few modes that punch above their weight.

## What is Emacs?

Emacs is an open-source **GNU** project built around a Lisp runtime. Beyond editing buffers, you can orchestrate mail, notes, version control, shells, and more—at the cost of a learning curve.

## Installation

#### Debian/Ubuntu

```bash
sudo apt-get update
sudo apt-get install emacs
```

#### Fedora / RHEL family

```bash
sudo dnf install emacs
```

#### macOS (Homebrew cask)

```bash
brew tap homebrew/cask
brew install --cask emacs
```

#### Windows

Download installers from the official GNU Emacs page: `https://www.gnu.org/software/emacs/download.html`.

## Configuration basics

Most users keep `~/.emacs` or `~/.emacs.d/init.el`. Small examples:

#### Line numbers

```elisp
(global-display-line-numbers-mode t)
```

#### Theme

```elisp
(load-theme 'solarized-dark t)
```

#### Default font (adjust to taste)

```elisp
(set-face-attribute 'default nil :font "DejaVu Sans Mono 12")
```

(`global-linum-mode` is legacy on newer Emacs builds—`display-line-numbers-mode` is the modern default.)

## Everyday commands

- Open file: `C-x C-f`  
- Save: `C-x C-s`  
- Close buffer: `C-x k`  
- Undo: `C-/` or `C-x u`

Muscle memory beats memorizing everything upfront—learn three chords per week.

## Productivity-oriented modes

### Org-mode

Structured notes, todos, agendas, and export pipelines. Usually bundled; see the official Org manual for install via **ELPA**/MELPA when you need newer features.

### Magit

A full **Git** UI inside Emacs. Install through the package manager (`M-x package-install RET magit RET` after configuring archives—see Magit’s documentation).

### Eshell

Lispy shell embedded in Emacs—handy for quick commands without leaving the editor.

### Dired

Directory browser for renaming, copying, and batch operations on files.

## Closing thoughts

Emacs rewards incremental customization: start simple, version-control your `init.el`, and adopt packages when a pain point repeats. Future articles can go deeper on specific workflows—Org capture, LSP integration, and performance profiling are natural next steps.
