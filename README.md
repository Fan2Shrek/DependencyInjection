# SRUUUA Dependency Injection

Personal Dependency Injection

## How to use it

Register dependency is 

```
#

name:
    class: App\Folder\Name
    func:
        - myFunc:
            - ['myArg']

# If otherName needs name
otherName:
    class: App\OtherFolder\OtherName
    arg:
        - @otherName
```