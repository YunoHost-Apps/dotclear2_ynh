#!/bin/bash

#=================================================
# COMMON VARIABLES AND CUSTOM HELPERS
#=================================================

_dotclear2_setup_source() {
    # In case of a new version, the url change from http://download.dotclear.org/latest/dotclear-X.X.X.tar.gz to http://download.dotclear.org/attic/dotclear-X.X.X.tar.gz

    src_url=$(cat $YNH_APP_BASEDIR/manifest.toml | toml_to_json | jq '.resources.sources.latest.url' -r)

    if curl --output /dev/null --silent --head --fail "$src_url"; then
        ynh_setup_source --dest_dir="$install_dir" --source_id="latest"
    else
        ynh_setup_source --dest_dir="$install_dir" --source_id="attic"
    fi
}
