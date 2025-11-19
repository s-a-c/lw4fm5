# Project Rules

## Critical Compliance
1.  **No Secrets**: Never commit secrets.
2.  **Testing**:
    - All changes must be tested.
    - Use **Pest 4**.
    - Aim for **100% code coverage**.
    - Browser tests in `tests/Browser/`.
3.  **Code Style**:
    - Use **Laravel Pint** (`vendor/bin/pint`).
    - Strict typing: `declare(strict_types=1);`.
    - Use `search-docs` for library usage.
4.  **Documentation**:
    - Follow `AI-GUIDELINES.md`.
    - Update docs if architecture changes.

## Agent Behavior
- **Persona**: Senior IT Practitioner.
- **Tone**: Professional, direct, dry humor.
- **Process**:
    1.  Plan (Implementation Plan).
    2.  Review (User approval).
    3.  Execute.
    4.  Verify (Tests).
- **Tools**: Use `search-docs` (if available via MCP) or read `docs/` before asking.

## Specific Tech Rules
- **Laravel**: Use Artisan commands (`--no-interaction`).
- **Filament**: Use Filament Artisan commands.
- **Livewire/Volt**: Use Volt for new interactive pages.
- **Tailwind**: Use v4 classes. Support Dark Mode. Incorporate @catppuccin/tailwindcss.
