# minimalism

minimalism is a service-based PHP MVC framework. Every element inside minimalism is either a **module** or a 
**service**, which are loaded just by adding them to `composer`.

It has been designed to offer a simple approach to developing softwares. Developers can focus on writing **models**
and **views** (in case of web applications) and forget everything that goes behind the scene.

## Status

This framework is fastly evolving and changing. As it is a personal project evolved in something more than a pet
project, you should expect quick changes. Every time the structure of the core component of minimalism is changed, a
new major version will be released.

# Modules and Services

## Services

A service is a system with a single entry point which offers a series of functionalities.
Each service defines the configurations required and implements its functionalities.

A service is always instantiates during bootstrap, and ready to be used during the code execution 

## Modules

A module is a core services which not necessarily implements callable functionalities, but defines logical structures.
This may include a type of return object ([{json:api}](https://jsonapi.org) for example) which is used by other modules
or services. 

# Getting Started

## Prerequisites

## Installing

# Requirements

minimalism requires **php 7.4**

# Available Modules and Services

## Modules

* jsonApi
* jaApiController
* jaWebController
* jaCliController
* core
* coreApoController
* coreCliCliController
* coreWebController

## Services

* apiCaller
* MySQL
* geolocator
* imgix
* rabbitMQ
* redis
* resourceBuilder
* encrypter
* security

# Versioning

minimalism, its modules and services use [Semantic Versioning](https://semver.org) to define a specific version:
MAJOR.MINOR.PATCH

# Authors
* **Carlo Nicora** - initial work - [carlonicora](https://github.com/carlonicora)
* **Sergey Kuzminich** - maintenance and expansion - [aldoka](https://github.com/aldoka)

# License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT) - see the [LICENSE.md](LICENSE.md) file for details


[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)