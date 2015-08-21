# DotClear 2 app for YunoHost

Currently following [this guide](https://yunohost.org/#/packaging_apps_fr) to package DotClear2 blog for YunoHost, along with [the example](https://github.com/YunoHost/example_ynh).

# TODO

- Add a 'protected' value to 'public' argument, so admin interface is protected
- Replace 'password' argument by http_auth or ldap authent

# Backup & Restore

Default YunoHost installation got a small bug that prevent `yunohost backup create`, here's the fix: `sed -i -e "302s/logging/logger/" /usr/lib/moulinette/yunohost/backup.py`

Yet it doesn't seems to save apps, at least in my test environment. To be investigated.
    
