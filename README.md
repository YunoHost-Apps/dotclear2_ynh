# DotClear 2 app for YunoHost

[DotClear2](http://dotclear.org/) package for [Yunohost](https://yunohost.org/#/)

# TODO

- Add a 'protected' value to 'public' argument, so admin interface is protected
- Replace 'password' argument by http_auth or ldap authent

# Problème connu

Dans le panneau d'administration, le sous menu Utilisateurs affiche ce message d'erreur

    1038 Out of sort memory, consider increasing sort buffer size
    
Pour corriger se connecter au serveur, éditer `/etc/mysql/my.cnf` et mettre `sort_buffer_size = 256K`. Puis `service mysql restart`


# Backup and restore

YunoHost backup & restore is not stable yet, you've to save your blog yourself and make sure you know how to restore it.

## Backup

In a root:root 750 /etc/cron.daily/yunohost script. 

    yunohost backup create

Note, do not use --hooks option, archives produced can't seems to be restored? And there will be two bugs preventing this command to work on a brand new YunoHost installation as of 09/2015. First one that you may fix asap.

    root@debian-jessie:~# yunohost backup create
    Traceback (most recent call last):
    File "/usr/bin/yunohost", line 160, in
    print_json=PRINT_JSON, use_cache=USE_CACHE)
    File "/usr/lib/python2.7/dist-packages/moulinette/__init__.py", line 117, in cli
    moulinette.run(args, print_json)
    File "/usr/lib/python2.7/dist-packages/moulinette/interfaces/cli.py", line 202, in run
    ret = self.actionsmap.process(args, timeout=5)
    File "/usr/lib/python2.7/dist-packages/moulinette/actionsmap.py", line 462, in process
    return func(**arguments)
    File "/usr/lib/moulinette/yunohost/backup.py", line 68, in backup_create
    if name in backup_list()['archives']:
    File "/usr/lib/moulinette/yunohost/backup.py", line 302, in backup_list
    logging.info("unable to iterate over local archives: %s", str(e))
    NameError: global name 'logging' is not defined

The fix

    sed -i -e "302s/logging/logger/" /usr/lib/moulinette/yunohost/backup.py

The second bug, that may only be fixed after a first failed backup attempt as this will create the 50-dotclear2 file

    root@debian-jessie:~/dotclear2_ynh# yunohost backup create --hooks dotclear2
    Exécution des scripts de sauvegarde...
    Exécution du script...
    /bin/bash: 50-dotclear2: Permission non accordée
    Création de l'archive de sauvegarde...
    Succès ! Sauvegarde terminée

Then you can fix it

    chown admin /etc/yunohost/hooks.d/backup/50-dotclear2

## Restore

Second bug and its fix also apply to restore script, /etc/yunohost/hooks.d/restore/50-dotclear. DotClear2 restore don't work so far. 
