# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2021-06-28

### Added

- Sign in to the Magento backend using a single sign-on Identity Provider for authentication
- Backend users are created automatically and updated on every sign in using the information from the Identity Provider
- Single sign-on users can be assigned to different roles using an Identity Provider attribute
- Single sign-on users can no longer sign in using a password or reset their password
- Single sign-on users are not required to enter their password when completing backend actions such as adding an integration
- A flag on the user profile allows toggling between single sign-on and regular password authentication for existing users
