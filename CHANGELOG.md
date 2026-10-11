# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- `psr/log` may now be `^3` as well as `^1.1`; the package only uses its `LogLevel` constants, which are the same in both.

### Fixed

- `AnnotatedResponse::__construct()` declared its optional argument implicitly nullable, which PHP 8.4+ deprecates.
