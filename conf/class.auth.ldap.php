<?php
class ldapDcAuth extends dcAuth
{
        # The user can't change his password
        protected $allow_pass_change = false;

        # LDAP parameter
        private $server = "localhost";
        private $port = "389";
        private $base = "dc=yunohost,dc=org";

        public function checkUser($user_id, $pwd=null, $user_key=null, $check_blog=true)
        {
                if ($pwd == '') {
                        return parent::checkUser($user_id, null, $user_key, $check_blog);
                }

                # LDAP connection
                $ds = ldap_connect("ldap://".$this->server.":".$this->port);
                if ($ds)
                {
                        ldap_set_option ($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

                        if (ldap_bind($ds, "uid=".$user_id.",ou=users,".$this->base, $pwd))
                        {
                                # search the user in ldap, and get infos
                                $filter = "(&(|(objectclass=posixAccount))(uid=".$user_id.")(permission=cn=__APP__.admin,ou=permission,".$this->base."))";
                                $sr = ldap_search($ds, $this->base, $filter, array("dn", "cn", "sn", "mail", "givenname")); # /!\ fields have to be in lowercase
                                $info = ldap_get_entries($ds, $sr);

                                if ($info["count"] == 1)
                                {
                                        $this->con->begin();
                                        $cur = $this->con->openCursor($this->user_table);
                                        # Store the password
                                        $cur->user_pwd = $pwd;

                                        # Store informations about the user
                                        $cur->user_id = $user_id;
                                        $cur->user_email = $info[0]['mail'][0];
                                        $cur->user_name = $info[0]['sn'][0];
                                        $cur->user_firstname = $info[0]['givenname'][0];
                                        $cur->user_displayname = $info[0]['cn'][0];

                                        # If the user exist, then we just update his password.
                                        if ($this->core->userExists($user_id))
                                        {
                                                $this->sudo(array($this->core, 'updUser'), $user_id, $cur);
                                        }
                                        # If not, we create him.
                                        # In order for him to connect,
                                        # it is necessary to give him at least
                                        # a permission "usage" on the blog "default".
                                        else
                                        {
                                                $cur->user_lang = 'fr';                         # Can change this, PR are welcome
                                                $cur->user_tz = 'Europe/Paris';                 # Can change this, PR are welcome
                                                $cur->user_default_blog = 'default';            # Can change this, PR are welcome
                                                $this->sudo(array($this->core,'addUser'), $cur);
                                                # Possible roles:
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
                                                $this->sudo(array($this->core, 'setUserBlogPermissions'), $user_id, 'default', array('usage'=>true)); # Can change this, PR are welcome
                                        }
                                        $this->con->commit();
                                        
                                        # The previous operations proceeded without error,
                                        # we can now call the parent method
                                        return parent::checkUser($user_id, $pwd);
                                }
                        }
                }
                # In case of error we cancel and return "false"
                $this->con->rollback();
                return false;
        }
}
?>
