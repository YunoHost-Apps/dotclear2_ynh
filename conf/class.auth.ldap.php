<?php
class ldapDcAuth extends dcAuth
{
    # The user can't change his password
    protected $allow_pass_change = false;

    # LDAP parameter
    private $server = "localhost";
    private $port = "389";
    private $base = "dc=yunohost,dc=org";

    public function checkUser(string $user_id, ?string $pwd = NULL, ?string $user_key = NULL, bool $check_blog = true): bool
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
                    # To be case sensitive
                    if ($info[0]['dn'] != "uid=".$user_id.",ou=users,".$this->base) {
                        return parent::checkUser($user_id, $pwd);
                    }

                    try
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
                        $super_user = "__ADMIN__";
                        if ($super_user == $user_id) {
                            $cur->user_super = 1;
                        }
                        else {
                            $cur->user_super = 0;
                        }

                        # If the user exist, then we just update his password.
                        if (dcCore::app()->userExists($user_id))
                        {
                            $this->sudo(array(dcCore::app(), 'updUser'), $user_id, $cur);
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
                            $this->sudo(array(dcCore::app(),'addUser'), $cur);
                            # Possible roles:
                            # admin "administrator"
                            #   contentadmin "manage all entries and comments"
                            #     usage "manage their own entries and comments"
                            #     publish "publish entries and comments"
                            #     delete "delete entries and comments"
                            #   categories "manage categories"
                            #   media_admin "manage all media items"
                            #     media "manage their own media items"
                            #   pages "manage pages"
                            #   blogroll "manage blogroll"
                            $permissions = array(
                                'admin' => "__BLOG_ADMIN__",
                                'contentadmin' => "__BLOG_CONTENTADMIN__",
                                'usage' => "__BLOG_USAGE__",
                                'publish' => "__BLOG_PUBLISH__",
                                'delete' => "__BLOG_DELETE__",
                                'categories' => "__BLOG_CATEGORIES__",
                                'media_admin' => "__BLOG_MEDIA_ADMIN__",
                                'media' => "__BLOG_MEDIA__",
                                'pages' => "__BLOG_PAGES__",
                                'blogroll' => "__BLOG_BLOGROLL__",
                            );
                            $set_perms = [];

                            foreach ($permissions as $perm_id => $v) {
                                if (is_string($v) && $v == "true") {
                                    $set_perms[$perm_id] = true;
                                }
                            }
                            $this->sudo(array(dcCore::app(), 'setUserBlogPermissions'), $user_id, 'default', $set_perms, true);
                        }

                        $this->con->commit();
                    }
                    catch (Exception $e)
                    {
                        # In case of error we cancel and return "false"
                        $this->con->rollback();
                        return false;
                    }
                    # The previous operations proceeded without error,
                    # we can now call the parent method
                    return parent::checkUser($user_id, $pwd);
                }
            }
            else
            {
                error_log("Failed to connect with the user ".$user_id);
            }
        }
        return parent::checkUser($user_id, $pwd);
    }
}
?>
