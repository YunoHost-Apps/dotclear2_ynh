# DotClear 2 app for YunoHost

Currently following [this guide](https://yunohost.org/#/packaging_apps_fr) to package DotClear2 blog for YunoHost, along with [the example](https://github.com/YunoHost/example_ynh).

# TODO

- Add a 'protected' value to 'public' argument, so admin interface is protected
- Replace 'password' argument by http_auth or ldap authent

# Backup 

Here's the command which should be tested and included in a /etc/cron.daily/dotclear2 script. Until Yunohost do allow user to manage backup through WebUI.

    yunohost backup create --hooks dotclear2

There might be two bugs preventing this command to work. First one that you may fix asap.

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

The correction to be applied

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

Here is an example of a proper backup

    root@debian-jessie:~/dotclear2_ynh# yunohost backup create --hooks dotclear2
    Exécution des scripts de sauvegarde...
    Exécution du script...
    + app=dotclear2
    + backup_dir=/home/yunohost.backup/tmp/1440164746/apps/dotclear2
    + sudo mkdir -p /home/yunohost.backup/tmp/1440164746/apps/dotclear2
    + sudo cp -a /var/www/dotclear2/. /home/yunohost.backup/tmp/1440164746/apps/dotclear2/sources
    ++ sudo yunohost app setting dotclear2 db_password
    + db_password=zLiS4XNmfYUk
    + sudo mysqldump -u dotclear2 -pzLiS4XNmfYUk dotclear2
    50-dotclear2: ligne 16: /home/yunohost.backup/tmp/1440164746/apps/dotclear2/dump.sql: Permission non accordée
    + sudo cp -a /etc/yunohost/apps/dotclear2/. /home/yunohost.backup/tmp/1440164746/apps/dotclear2/yunohost
    ++ sudo yunohost app setting dotclear2 domain
    + domain=vagrant.test
    + sudo cp -a /etc/nginx/conf.d/vagrant.test.d/dotclear2.conf /home/yunohost.backup/tmp/1440164746/apps/dotclear2/nginx.conf
    Création de l'archive de sauvegarde...
    Succès ! Sauvegarde terminée

# Restore


