# DotClear 2 app for YunoHost

[![Integration level](https://dash.yunohost.org/integration/dotclear2.svg)](https://dash.yunohost.org/appci/app/dotclear2)  
[![Install dotclear2 with YunoHost](https://install-app.yunohost.org/install-with-yunohost.png)](https://install-app.yunohost.org/?app=dotclear2)

> *This package allow you to install dotclear2 quickly and simply on a YunoHost server.  
If you don't have YunoHost, please see [here](https://yunohost.org/#/install) to know how to install and enjoy it.*

## Overview
Quick description of this app.

**Shipped version:** 2.14.3

## Screenshots

![](https://installatron.com/images/remote/ss2_dotclear.png)

## Configuration

How to configure this app: by an admin panel.

## Documentation

 * Official documentation: https://dotclear.org/documentation/2.0

## YunoHost specific features

#### Multi-users support

Are LDAP and HTTP auth supported? **Yes**  
Can the app be used by multiple users? **Yes**

#### Supported architectures

* x86-64b - [![Build Status](https://ci-apps.yunohost.org/ci/logs/dotclear2%20%28Community%29.svg)](https://ci-apps.yunohost.org/ci/apps/dotclear2/)
* ARMv8-A - [![Build Status](https://ci-apps-arm.yunohost.org/ci/logs/dotclear2%20%28Community%29.svg)](https://ci-apps-arm.yunohost.org/ci/apps/dotclear2/)
* Jessie x86-64b - [![Build Status](https://ci-stretch.nohost.me/ci/logs/dotclear2%20%28Community%29.svg)](https://ci-stretch.nohost.me/ci/apps/dotclear2/)

## Links

 * Report a bug: https://github.com/YunoHost-Apps/dotclear2_ynh/issues
 * App website: https://dotclear.org/
 * YunoHost website: https://yunohost.org/

---

Developers info
----------------

**Only if you want to use a testing branch for coding, instead of merging directly into master.**
Please do your pull request to the [testing branch](https://github.com/YunoHost-Apps/dotclear2_ynh/tree/testing).

To try the testing branch, please proceed like that.
```
sudo yunohost app install https://github.com/YunoHost-Apps/dotclear2_ynh/tree/testing --debug
or
sudo yunohost app upgrade dotclear2 -u https://github.com/YunoHost-Apps/dotclear2_ynh/tree/testing --debug
```

# TODO

- Add a 'protected' value to 'public' argument, so admin interface is protected
- Replace 'password' argument by http_auth or ldap authent
