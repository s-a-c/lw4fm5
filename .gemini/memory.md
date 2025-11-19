# Project Memory

---

<details>
<summary>Expand for Table of Contents</summary>

- [Project Memory](#project-memory)
  - [1. Project Overview](#1-project-overview)
  - [2. Technology Stack](#2-technology-stack)
  - [3. Key Directories](#3-key-directories)
  - [4. Key Guidelines Summary](#4-key-guidelines-summary)
  - [5. Active Context](#5-active-context)
  - [6. Useful Commands](#6-useful-commands)

</details>

---

## 1. Project Overview

- **Name**: lw4fm5 (Laravel Boost Project)
- **Git Remote**: `https://github.com/s-a-c/lw4fm5.git`
- **Path**: `/Users/s-a-c/Herd/lw4fm5`

## 2. Technology Stack

- **Framework**: Laravel 12
- **Admin Panel**: Filament v5
- **Frontend**: Livewire v4, Volt, Flux UI Pro, Tailwind CSS v3
- **Testing**: Pest 4 (Browser Testing with Playwright)
- **Runtime**: PHP 8.4+, Bun 1.1.0+
- **Database**: SQLite (dev), MySQL (prod likely)

## 3. Key Directories

- `app`: Core application logic
- `tests`: Pest tests (`Feature`, `Unit`, `Browser`)
- `docs`: Project documentation
- `config`: Configuration files
- `database`: Migrations, seeders, factories
- `resources`: Views, CSS, JS

## 4. Key Guidelines Summary

- **Strict Typing**: `declare(strict_types=1);` in all PHP files.
- **Testing**: 100% coverage required. Use Pest.
- **Secrets**: NO secrets in repo.
- **Code Style**: Run `pint` before committing.
- **Docs**: Use `search-docs` tool (if available) or check `docs/` folder.

## 5. Active Context

- **ZSH Redesign**: Stage 3 of 7 in progress (from `AI-GUIDELINES.md` context, though this seems to be from the dotfiles repo context mixed in guidelines. *Correction*: The project seems to be a Laravel app, but the `AI-GUIDELINES.md` mentioned a dotfiles repo. *Clarification*: The `AI-GUIDELINES.md` file content shown in step 8 *does* mention "This is a personal dotfiles repository". However, `AGENTS.md` (step 7) explicitly says "This application is a Laravel application...". The user request is for "this project" (`lw4fm5`). I will prioritize `AGENTS.md` for project specifics and `AI-GUIDELINES.md` for general AI behavior/compliance.)

> [!NOTE]
> `AI-GUIDELINES.md` seems to be a shared/symlinked file or copied from a dotfiles repo. `AGENTS.md` is specific to this Laravel project.

## 6. Useful Commands

- `php artisan test`
- `bun run build`
- `bun run dev`
- `vendor/bin/pint`
