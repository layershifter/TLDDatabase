#!/usr/bin/env bash

########################################################################################################################
#                                                                                                                      #
# TLDDatabase: Abstraction for Public Suffix List in PHP.                                                              #
# https://github.com/layershifter/TLDDatabase                                                                          #
#                                                                                                                      #
# Script for updating TLDDatabase and making commit to github.com.                                                     #
#                                                                                                                      #
# Copyright (c) 2016, Alexander Fedyashov                                                                              #
# https://raw.githubusercontent.com/layershifter/TLDDatabase/master/LICENSE Apache 2.0 License                         #
#                                                                                                                      #
########################################################################################################################

#
# Helper functions declaration.
#

function echo_success {
  local green=$(tput setaf 2)
  local reset=$(tput sgr0)

  echo -ne "${green}[OK]    ${reset}" && echo -e "$@" >&2
}

function echo_error {
  local red=$(tput setaf 1)
  local reset=$(tput sgr0)

  echo -ne "${red}[ERROR] ${reset}" && echo -e "$@" >&2
}

#
# Checking for "--force" option.
#

if [ -z $1 ] || [ $1 != "--force" ]; then
 tput setaf 1
 echo -e "WARNING!"
 tput sgr0

 echo -e
 echo "This script is used only by maintainers of TLDDatabase. Don't use it in production. If you you want run it," \
      "use --force option."

 exit 1
fi

#
# Check existence of utilities.
#

git --version > /dev/null 2>&1 ||
 { echo_error "This tool requires git, try: apt-get install git OR yum install git. Aborting."; exit 1; }
php -v > /dev/null 2>&1 ||
 { echo_error "This tool requires PHP >= 5.6, see: http://php.net/manual/ru/install.php. Aborting."; exit 1; }
composer -v > /dev/null 2>&1 ||
 { echo_error "This tool requires composer, see: https://getcomposer.org/download/. Aborting."; exit 1; }
github-release > /dev/null 2>&1 ||
 { echo_error "This tool requires github-release, see: https://github.com/aktau/github-release. Aborting."; exit 1; }

echo_success "All necessary tools are present."

#
# Checking github environment variables.
#

if [ -z "$GITHUB_USER" ]; then
  echo_error "This tool GITHUB_USER environment variable, set it with: export GITHUB_USER=your_username. Aborting."

  exit 1
fi

if [ -z "$GITHUB_REPO" ]; then
  echo_error "This tool GITHUB_REPO environment variable, set it with: export GITHUB_REPO=your_repository. Aborting."

  exit 1
fi

if [ -z "$GITHUB_TOKEN" ]; then
  echo_error "This tool GITHUB_TOKEN environment variable, see:" \
   "https://help.github.com/articles/creating-an-access-token-for-command-line-use/. Aborting."

  exit 1
fi

echo_success "All environment variables are present."

#
# Checking token and get latest release tag.
#

echo "        Getting latest release tag..."

TAG_OUTPUT=`github-release info -u $GITHUB_USER -r $GITHUB_REPO`

if [ $? -ne 0 ]; then
  echo_error "Error while getting latest release tag from github.com. Aborting."

  exit 1
fi

CURRENT_TAG="$(echo $TAG_OUTPUT | sed 's/.*releases://g' | cut -d',' -f1 | cut -d'-' -f2 | sed 's/ //g')"

if ! [[ $CURRENT_TAG =~ ^([0-9]+\.[0-9]+\.[0-9]+)$ ]]; then
  echo_error "Fetched invalid version tag ($CURRENT_TAG). Aborting."

  exit 1
fi

IFS=.; read -a VERSION_PARTS <<<"$CURRENT_TAG"
PATCH_PART=$((VERSION_PARTS[2]+1))

NEW_TAG="${VERSION_PARTS[0]}.${VERSION_PARTS[1]}.${PATCH_PART}"

echo_success "Fetched valid version tag: $CURRENT_TAG. New version tag will be: $NEW_TAG"

#
# Change working directory.
#

PROJECT_DIR="$(dirname "$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)")"
cd $PROJECT_DIR

echo_success "Project directory is $PROJECT_DIR"

#
# Running git reset.
#

if [ ! -d "$PROJECT_DIR/.git" ]; then
  echo_error ".git directory not exists, something wrong"

  exit 1
fi

git reset --hard HEAD 1> /dev/null
git clean  -d  -fx "" 1> /dev/null

git checkout -B master origin/master 1> /dev/null
git pull 1> /dev/null

#
# Install library dependencies.
#

composer install 1> /dev/null

if [ $? -ne 0 ]; then
  echo_error "Error while install dependencies. Aborting."

  exit 1
fi

#
# Performing database update with PHP code.
#

echo "        Running database update..."

read -r -d '' VARIABLE << EOM
require_once '$PROJECT_DIR/vendor/autoload.php';

try {
  \$updater = new \LayerShifter\TLDDatabase\Update();
  \$updater->run();
} catch(\Exception \$e) {
  echo \$e->getMessage();
  exit(1);
}

exit(0);
EOM

UPDATE_OUTPUT=`php -r "$VARIABLE"`

if [ $? -ne 0 ]; then
  echo_error "Error while updating database: $UPDATE_OUTPUT"

  exit 1
fi

echo_success "Database updated successfully"

#
# Git commit update.
#

git-update-index 2>&1 1> /dev/null
git commit -m "Automatic database update" "$PROJECT_DIR/resources/database.php" 2>&1 1>/dev/null

if [ $? -ne 0 ]; then
  echo_success "Database doesn't have update. Aborting."

  exit 0
fi

COMMIT_COUNT=`git log origin/master..HEAD | wc -l`

if [ $COMMIT_COUNT -eq 0 ]; then
  echo_success "Database doesn't have update. Aborting."

  exit 0
fi

echo_success "Database has update. Commit generated."

#
# Git push changes and tags.
#

echo "        Running push changes ..."
git push "https://$GITHUB_TOKEN@github.com/$GITHUB_USER/$GITHUB_REPO.git" 1> /dev/null

if [ $? -ne 0 ]; then
  echo_error "Push changes failed. Aborting."

  exit 1
fi

echo "        Running push tag ..."
git tag "$NEW_TAG" && git push --tags "https://$GITHUB_TOKEN@github.com/$GITHUB_USER/$GITHUB_REPO.git" 1> /dev/null

if [ $? -ne 0 ]; then
  echo_error "Push tag failed. Aborting."

  exit 1
fi

echo_success "Push was completed successfully."

#
# Creating release tag.
#

github-release release \
    --user "$GITHUB_USER" \
    --repo "$GITHUB_REPO" \
    --name "Release $NEW_TAG" \
    --tag "$NEW_TAG" \
    --description "Database update" 2>&1 1> /dev/null

if [ $? -ne 0 ]; then
  echo_success "Release failed. Aborting."

  exit 1
fi

echo_success "Update procedure was completed successfully."