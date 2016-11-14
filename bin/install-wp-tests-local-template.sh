#!/usr/bin/env bash

export WP_TESTS_DIR=`pwd`/tests/wordpress-tests-lib/
export WP_CORE_DIR=`pwd`/tests/wordpress/
./bin/install-wp-tests.sh wptest root ''
