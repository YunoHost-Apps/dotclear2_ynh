#!/bin/bash

#=================================================
# COMMON VARIABLES
#=================================================

#=================================================
# PERSONAL HELPERS
#=================================================

_check_if_source_available() {
    source_id=$1

    source_url=$(cat "$YNH_APP_BASEDIR/manifest.toml" | toml_to_json | jq ".resources.sources[\"$source_id\"][\"url\"]")

    curl --output /dev/null --silent --head --fail "$source_url"
}

#=================================================
# EXPERIMENTAL HELPERS
#=================================================

#=================================================
# FUTURE OFFICIAL HELPERS
#=================================================
