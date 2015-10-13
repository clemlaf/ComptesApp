ComptesApp
========================

A self-hosted personal finance manager.

Installation
========================
- bower install
- composer install
- create a database with an account for the app
- php app/console doctrine:schema:update --force
- php app/console assetic:dump
- php app/console server:run (or configure apache/nginx or whatever).
