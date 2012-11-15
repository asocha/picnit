#!/bin/bash

# Convert all indentations using spaces to tabs
sed -r -i s/\ \ \ \ \ \ \ \ /\\t/g $@

# Remove all trailing whitespace
sed -r -i s/\\\ {1\,}\$//g $@

# Remove all indentations on empty lines
sed -r -i s/\\t{1\,}\$//g $@

# Convert mixed space-tab indentations to tab-only
sed -r -i s/\\\ {1\,7}\\t/\\t/g $@
