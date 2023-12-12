#!/bin/bash

#=================================================
# PACKAGE UPDATING HELPER
#=================================================

# This script is meant to be run by GitHub Actions
# The YunoHost-Apps organisation offers a template Action to run this script periodically
# Since each app is different, maintainers can adapt its contents so as to perform
# automatic actions when a new upstream release is detected.

#=================================================
# FETCHING LATEST RELEASE AND ITS ASSETS
#=================================================

# Fetching information
current_version=$(cat manifest.toml | grep "version =" | sed 's|version = "\(.*\)~ynh[0-9]*"|\1|')
# repo=$(cat manifest.json | jq -j '.upstream.code|split("https://github.com/")[1]')
asset=$(curl --silent "https://download.dotclear.org/latest/" | grep "dotclear-.*?.zip" -Po | head -1)
version=${asset%.zip}
version=${version#dotclear-}

# Later down the script, we assume the version has only digits and dots
# Sometimes the release name starts with a "v", so let's filter it out.
# You may need more tweaks here if the upstream repository has different naming conventions.
if [[ ${version:0:1} == "v" || ${version:0:1} == "V" ]]; then
    version=${version:1}
fi

# Setting up the environment variables
echo "Current version: $current_version"
echo "Latest release from upstream: $version"
echo "VERSION=$version" >> $GITHUB_ENV
# For the time being, let's assume the script will fail
echo "PROCEED=false" >> $GITHUB_ENV

# Proceed only if the retrieved version is greater than the current one
if ! dpkg --compare-versions "$current_version" "lt" "$version" ; then
    echo "::warning ::No new version available"
    exit 0
# Proceed only if a PR for this new version does not already exist
elif git ls-remote -q --exit-code --heads https://github.com/$GITHUB_REPOSITORY.git ci-auto-update-v$version ; then
    echo "::warning ::A branch already exists for this update"
    exit 0
fi

#=================================================
# UPDATE SOURCE FILES
#=================================================

src="app"

# Create the temporary directory
tempdir="$(mktemp -d)"

# Download sources and calculate checksum
curl --silent -4 -L http://download.dotclear.org/latest/dotclear-$version.tar.gz -o "$tempdir/$asset"
checksum=$(sha256sum "$tempdir/$asset" | head -c 64)

# Delete temporary directory
rm -rf $tempdir

# Get extension
if [[ $asset == *.zip ]]; then
  extension=zip
fi

# Rewrite source file
set -x
sed -i "s|/dotclear-.*.tar.gz|/dotclear-$version.tar.gz|" manifest.toml
sed -i "s|sha256 = \".*\"|sha256 = \"$checksum\"|" manifest.toml
sed -i "s|version = \".*\"|version = \"$version~ynh1\"|" manifest.toml
#=================================================
# SPECIFIC UPDATE STEPS
#=================================================

# Any action on the app's source code can be done.
# The GitHub Action workflow takes care of committing all changes after this script ends.

#=================================================
# GENERIC FINALIZATION
#=================================================

# No need to update the README, yunohost-bot takes care of it

# The Action will proceed only if the PROCEED environment variable is set to true
echo "PROCEED=true" >> $GITHUB_ENV
exit 0
