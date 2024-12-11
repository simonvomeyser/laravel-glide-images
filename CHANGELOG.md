# Changelog

All notable changes to `laravel-glide-images` will be documented in this file.

## Laravel 11.x compatibility   - 2024-12-11

- Update for use with laravel 11.x, php 8.3
- Remove glide laravel dep, see https://github.com/thephpleague/glide-laravel/issues/11

## Bug fixes and maintenance - 2024-12-03

- Fixed a small bug when calling helper with null
- Dependencies
- Documentation and readme

## 0.0.5 - 2024-02-10

- Fixed an error where passing null to helper function would cause an error
- `glide(null)` will now return an empty string

## 0.0.4 - 2024-02-08

- Fix error with signature
- Add tests for endpoint
- Fix error with phpstan

## 0.0.3 - 2024-02-08

- Added basic unit testing
- Refactored code to use class instead of code in helper function
- Keep other parameters in the url
- glide(glide(...)) will work
- Remove other parameters
- Add dynamic config parameters
- Fix issue with ignored "secure" option
- Return the input if it's an external url

## 0.0.2 - 2024-01-29

- Fixed bug in command

## 0.0.1 - 2024-01-27

- Experimental release
