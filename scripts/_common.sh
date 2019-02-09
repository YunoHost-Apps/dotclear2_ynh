# Curl abstraction to help with POST requests to local pages (such as installation forms)
#
# $domain and $path_url should be defined externally (and correspond to the domain.tld and the /path (of the app?))
#
# example: ynh_local_curl "/install.php?installButton" "foo=$var1" "bar=$var2"
# 
# usage: ynh_local_curl "page_uri" "key1=value1" "key2=value2" ...
# | arg: page_uri    - Path (relative to $path_url) of the page where POST data will be sent
# | arg: key1=value1 - (Optionnal) POST key and corresponding value
# | arg: key2=value2 - (Optionnal) Another POST key and corresponding value
# | arg: ...         - (Optionnal) More POST keys and values
ynh_local_curl () {
	# Define url of page to curl
	path_url=$(ynh_normalize_url_path $path_url)
	local local_page=$(ynh_normalize_url_path $1)
	local full_path=$path_url$local_page
	
	if [ "${path_url}" == "/" ]; then 
		full_path=$local_page
	fi
	
	local full_page_url=https://localhost$full_path

	# Concatenate all other arguments with '&' to prepare POST data
	local POST_data=""
	local arg=""
	for arg in "${@:2}"
	do
		POST_data="${POST_data}${arg}&"
	done
	if [ -n "$POST_data" ]
	then
		# Add --data arg and remove the last character, which is an unecessary '&'
		POST_data="--data ${POST_data::-1}"
	fi
	
	# Wait untils nginx has fully reloaded (avoid curl fail with http2)
	sleep 2

	# Curl the URL
	curl --silent --show-error -kL -H "Host: $domain" --resolve $domain:443:127.0.0.1 $POST_data "$full_page_url"
}

ynh_url_join() {
    if [ "$#" -eq 0 ]; then
        ynh_die "Illegal number of parameters"
    fi
    
    local full_url=""

    for var in "$@"
    do
        if [ "${var:0:1}" != "/" ]; then    # If the first character is not a /
		    var="/$var"    # Add / at begin of path variable
        fi

        if [ "${var:${#var}-1}" == "/" ]; then    # If the last character is a /
		    var="${var:0:${#var}-1}"	# Delete the last character
        fi
        full_url=${full_url}${var}
    done

    full_url=$(ynh_normalize_url_path $full_url)
    echo $full_url
}