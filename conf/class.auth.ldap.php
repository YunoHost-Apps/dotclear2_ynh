<?php
class myDcAuth extends dcAuth
{
        # L'utilisateur n'a pas le droit de changer son mot de passe
        protected $allow_pass_change = false;

        # La méthode de vérification du mot de passe
        public function checkUser($user_id, $pwd=null, $user_key=null, $check_blog=true)
        {
                # Pas de mot de passe, on appelle la méthode parente.
                if ($pwd == '') {
                        return parent::checkUser($user_id, null, $user_key, $check_blog);
                }

                # On démarre une transaction et on ouvre un curseur sur la
                # table utilisateur de Dotclear pour créer ou modifier
                # l'utilisateur.
                $this->con->begin();
                $cur = $this->con->openCursor($this->user_table);

                # parmetre de configuration pour l'interface PHP pour administrer
                # notre annuaire LDAP
                $server = "localhost";
                $port = "389";
                $racine = "dc=yunohost,dc=org";

                #connection au serveur ldap
                $ds=ldap_connect($server);
                ldap_set_option ($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
                if (ldap_bind($ds,"uid=".$user_id.",ou=users,dc=yunohost,dc=org",$pwd))
                {
                        # On définit le mot de passe, il est inséré dans tous les cas.
                        $cur->user_pwd = $pwd;

                        # Si l'utilisateur existe, nous allons uniquement mettre à jour
                        # son mot de passe dans la table utilisateur de Dotclear.
                        if ($this->core->userExists($user_id))
                        {
                                $this->sudo(array($this->core,'updUser'),$user_id,$cur);
                                $this->con->commit();
                        }
                        # Si l'utilisateur n'existe pas, nous allons le créer.
                        # Afin qu'il puisse se connecter, il est nécessaire de lui donner
                        # au moins une permission "usage" sur le blog "default".
                        else
                        {
                                #on recherche l'utilisateur dans le ldap pour recuperer toutes les informations
                                $sr=ldap_search($ds,$racine,"uid=$user_id",array( "dn", "cn", "sn", "mail", "givenName"));
                                $info = ldap_get_entries($ds, $sr);


                                #si le ldap ne ramene qu'un seul utilisateur
                                if ($info["count"] ==1)
                                {
                                        $cur->user_id = $user_id;
                                        $cur->user_email = $info[0]['mail'][0];
                                        $cur->user_name = $info[0]['givenName'][0];
                                        $cur->user_firstname = $info[0]['sn'][0];
                                        $cur->user_lang = 'fr';
                                        $cur->user_tz = 'Europe/Paris';
                                        $cur->user_default_blog = 'default';
                                        $this->sudo(array($this->core,'addUser'),$cur);
                                        #admin "administrator"
                                        #usage "manage their own entries and comments"
                                        #publish "publish entries and comments"
                                        #delete "delete entries and comments"
                                        #contentadmin "manage all entries and comments"
                                        #categories "manage categories"
                                        #media "manage their own media items"
                                        #media_admin "manage all media items"
                                        #pages "manage pages"
                                        #blogroll "manage blogroll"
                                        $this->sudo(array($this->core,'setUserBlogPermissions'),$user_id,'default',array('admin'=>true));
                                        $this->con->commit();
                                }
                        }

                        # Les opérations précédentes se sont déroulées sans erreur, nous
                        # pouvons maintenant appeler la méthode parente afin d'initialiser
                        # l'utilisateur dans l'object $core->auth
                        return parent::checkUser($user_id, $pwd, $user_key, $check_blog);
                }
                # En cas d'erreur on annule la transaction et on renvoie "false"
                $this->con->rollback();
                return false;
        }
}
?>