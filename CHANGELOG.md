# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] - 2022-07-15

### Fixed

- Fixed an error when logging in with Azure AD SSO using one time passcode authentication (thanks @p24-max!)

## [1.1.0] - 2022-06-17

### Added

- Added compatibility with Azure SSO (thanks @p24-max!)
- Added the ability to customise the Service Provider entity ID
- Added the ability to set a static user role instead of using the Identity Provider attribute
- Added the ability to customise the Identity Provider attribute names for firstname, lastname, email and role

## [1.0.0] - 2021-06-28

### Added

- Sign in to the Magento backend using a single sign-on Identity Provider for authentication
- Backend users are created automatically and updated on every sign in using the information from the Identity Provider
- Single sign-on users can be assigned to different roles using an Identity Provider attribute
- Single sign-on users can no longer sign in using a password or reset their password
- Single sign-on users are not required to enter their password when completing backend actions such as adding an integration
- A flag on the user profile allows toggling between single sign-on and regular password authentication for existing users
