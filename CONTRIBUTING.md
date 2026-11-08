# Contributing to ReportEase

Thanks for your interest in improving ReportEase! Contributions of all kinds are welcome.

## Getting Started

1. Fork the repository and clone your fork.
2. Follow the [Installation](README.md#-installation) steps to get a local environment running.
3. Create a feature branch:
   ```bash
   git checkout -b feature/my-change
   ```

## Development Workflow

- Follow **PSR-12** and the Laravel coding conventions.
- Run the test suite and code-style checks before opening a pull request:
  ```bash
  php artisan test
  vendor/bin/pint --test
  ```
- Run `vendor/bin/pint` to auto-format your code.
- Add or update tests for any behavior you change.
- Keep pull requests focused and include a clear description of the change.

## Commit Messages

Write clear, imperative commit messages (e.g. `Add role check to issue update`).

## Reporting Bugs

Open a GitHub issue and include:

- PHP and Laravel versions
- Steps to reproduce
- Expected vs. actual behavior
- Relevant logs or screenshots

## Pull Requests

1. Ensure CI passes (tests + Pint).
2. Reference any related issues.
3. Request a review and be responsive to feedback.

By contributing, you agree that your contributions will be licensed under the
[MIT License](LICENSE).
