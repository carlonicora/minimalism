# Minimalism

[Minimalism](https://github.com/carlonicora/minimalism) is a modular MVC (Model-View-Controller) 
framework for PHP 8.1. Minimalism is written to support both Webpages as well as APIs.
The framework is small and doesn't offer many functions "_off the hook_", but it has been build with 
extensibility in mind. You can use minimalism without any other frills, or you can install one of the many
services already present and use those functionalities.

Minimalism is tested, but please keep in mind this is a project which is maintained to support projects
which we are currently developing. This means the time and effort we can put on minimalism is not as much
as we would love.

## Why did we write minimalism?

Yes, there are lots of frameworks out there, we appreciate it. Yet, we believed we could do something 
that was closer to our needs. We wanted a small framework with very basic support and a great epandability. 
This is why we created minimalism: **it's what we needed**.

If you think minimalism hits the right notes and you want to help, please jump to the [Contribution]() 
section and send your love!

## Minimalism Interfaces

Some of minimalism services are based on some common interfaces. These interfaces are implemented in
services which offers different views on the same interface.

Please note that every installation of minimalism can only use one service implementing a minimalism
interface. This means that should you like to use [MySQL](https://github.com/carlonicora/minimalism-service-mysql)
you won't be able to use any other service which implements 
[minimalism-interface-sql](https://github.com/carlonicora/minimalism-interface-sql).

Currently, the following interfaces have been created:

- [Sql](https://github.com/carlonicora/minimalism-interface-sql):
  Interface to access data in database 
- [User](https://github.com/carlonicora/minimalism-interface-user):
Interface to define a project-specific user to be used in generic services
- [Mailer](https://github.com/carlonicora/minimalism-interface-mailer):
Interface to send emails
- [Cache](https://github.com/carlonicora/minimalism-interface-cache):
Interface to cache data
- [Encrypter](https://github.com/carlonicora/minimalism-interface-encrypter):
Interface to manage simple data encryption

The interfaces do not offer functionalities, but they support services which implement
the actual features

## Minimalism Services

- [Encrypter](https://github.com/carlonicora/minimalism-service-encrypter):
Encrypt ids to avoid sending int ids to the client
- [Redis](https://github.com/carlonicora/minimalism-service-redis):
Access Redis database
- [Cacher](https://github.com/carlonicora/minimalism-service-cacher):
Implement the [cache interface](https://github.com/carlonicora/minimalism-interface-cache) using
[Redis](https://github.com/carlonicora/minimalism-service-redis)
- [MySQL](https://github.com/carlonicora/minimalism-service-mysql):
Implements the [Sql interface](https://github.com/carlonicora/minimalism-interface-sql) for MySQL
- [Twig](https://github.com/carlonicora/minimalism-service-twig):
Implements 
- [RabbitMQ](https://github.com/carlonicora/minimalism-service-rabbitmq):

- [Twig for Mailer](https://github.com/carlonicora/minimalism-service-mailer-twig):

- [Mandrill for Mailer](https://github.com/carlonicora/minimalism-service-mailer-mandrill):

- [Sendgrid for Mailer](https://github.com/carlonicora/minimalism-service-mailer-sendgrid):

- [S3](https://github.com/carlonicora/minimalism-service-s3):

- [Slack](https://github.com/carlonicora/minimalism-service-slack):

- [Geolocator](https://github.com/carlonicora/minimalism-service-geolocator):

- [Logger](https://github.com/carlonicora/minimalism-service-logger):

- [Imgix](https://github.com/carlonicora/minimalism-service-imgix):

- [Data Validator](https://github.com/carlonicora/minimalism-service-datavalidator):

- [Data Mapper](https://github.com/carlonicora/minimalism-service-data-mapper):

- [Elastic Search](https://github.com/carlonicora/minimalism-service-elasticsearch):

- [Active Campaign](https://github.com/carlonicora/minimalism-service-active-campaign):

- [Builder](https://github.com/carlonicora/minimalism-service-builder):

- [Auth](https://github.com/carlonicora/minimalism-service-auth):

- [Firebase](https://github.com/carlonicora/minimalism-service-firebase):

- [Groups](https://github.com/carlonicora/minimalism-service-groups):

- [Massaging](https://github.com/carlonicora/minimalism-service-messaging):

- [Advanced Logger](https://github.com/carlonicora/minimalism-service-advanced-logger):

- [Stripe](https://github.com/carlonicora/minimalism-service-stripe):

## Installation

### Docker

## Contribution

If you think you can make minimalism better, we would love to hear from you! 
From bug fixes to proposed changes

### All Changes Happen Through Pull Requests
Pull requests are the best way to propose changes. We actively welcome your pull requests:

1. Fork the repo and create your branch from master.
2. If you've added code that should be tested, add some tests' example.
3. If you've changed APIs, update the documentation.
4. Issue that pull request!

### Your interfaces and services

If you have developed any interface or service for minimalism, please do let us know and we will add it in the list
of interfaces and services!

## Build With

* PHP 8.1
* [minimalism](https://github.com/carlonicora/minimalism) - minimal modular PHP MVC framework

## Versioning

This project use [Semantiv Versioning](https://semver.org/) for its tags.

## Author

- [Carlo Nicora](https://github.com/carlonicora)
- [Sergey Kuzminich](https://github.com/aldoka)

## License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT) - see the
[LICENSE.md](LICENSE.md) file for details

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)