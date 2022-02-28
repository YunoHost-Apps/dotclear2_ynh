# DotClear 2 pour YunoHost

[![Niveau d'intégration](https://dash.yunohost.org/integration/dotclear2.svg)](https://dash.yunohost.org/appci/app/dotclear2) ![](https://ci-apps.yunohost.org/ci/badges/dotclear2.status.svg) ![](https://ci-apps.yunohost.org/ci/badges/dotclear2.maintain.svg)  
[![Installer DotClear 2 avec YunoHost](https://install-app.yunohost.org/install-with-yunohost.svg)](https://install-app.yunohost.org/?app=dotclear2)

*[Read this readme in english.](./README.md)*
*[Lire ce readme en français.](./README_fr.md)*

> *Ce package vous permet d'installer DotClear 2 rapidement et simplement sur un serveur YunoHost.
Si vous n'avez pas YunoHost, regardez [ici](https://yunohost.org/#/install) pour savoir comment l'installer et en profiter.*

## Vue d'ensemble

Moteur de blog

**Version incluse :** 2.21.1~ynh1

**Démo :** https://www.softaculous.com/demos/Dotclear

## Captures d'écran

![](./doc/screenshots/ss2_dotclear.png)

## Avertissements / informations importantes

## Configuration

How to configure this app: by an admin panel.

#### Multi-users support

Are LDAP supported? **Yes**  
Are HTTP auth supported? **No** (PR are welcome!)  
Can the app be used by multiple users? **Yes**

## Documentations et ressources

* Site officiel de l'app : https://dotclear.org
* Documentation officielle de l'admin : https://dotclear.org/documentation/2.0
* Dépôt de code officiel de l'app : https://git.dotclear.org/dev/dotclear
* Documentation YunoHost pour cette app : https://yunohost.org/app_dotclear2
* Signaler un bug : https://github.com/YunoHost-Apps/dotclear2_ynh/issues

## Informations pour les développeurs

Merci de faire vos pull request sur la [branche testing](https://github.com/YunoHost-Apps/dotclear2_ynh/tree/testing).

Pour essayer la branche testing, procédez comme suit.
```
sudo yunohost app install https://github.com/YunoHost-Apps/dotclear2_ynh/tree/testing --debug
ou
sudo yunohost app upgrade dotclear2 -u https://github.com/YunoHost-Apps/dotclear2_ynh/tree/testing --debug
```

**Plus d'infos sur le packaging d'applications :** https://yunohost.org/packaging_apps