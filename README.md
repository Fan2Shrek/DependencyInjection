# SRUUUA Dependency Injection

Personal Dependency Injection

## How to use it

Register dependency is

```
# config/service.yml

#File that are not required
excludes:
  - '../src/Kernel.php'
  - '../src/Entity/'

services:
    router:
        class: Sruuua\Routing\RouterBuilder
        arg:
        - '@container'
```
