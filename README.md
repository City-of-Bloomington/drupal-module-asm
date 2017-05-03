Shelter Manager Module
----------------------

The ASM module provides read-only integration with Animal Shelter Manager.

* https://github.com/bobintetley/asm3
* http://sheltermanager.com

## Installation
We recommend using composer for installing this module.  Right now, this module is still a sandbox project, so you will need to explicitly add this repository to your composer.json.

Main development for this module is on Github.  The Drupal.org repository is downstream from Github.

```json
    "repositories": [
        {
            "type": "package",
            "package": {
                "name": "City-of-Bloomington/asm",
                "type": "drupal-module",
                "version": "dev",
                "source": {
                    "type": "git",
                    "url": "https://github.com/City-of-Bloomington/drupal-module-asm",
                    "reference": "8.x-1.x"
                }
            }
        }
    ]
```
